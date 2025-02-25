<?php

namespace Kalimeromk\Apollo\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Kalimeromk\Apollo\ApolloEnrichmentService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApolloEnrichmentServiceTest extends TestCase
{
    /**
     * @var MockObject|Client
     */
    private $mockClient;

    /**
     * @var ApolloEnrichmentService
     */
    private ApolloEnrichmentService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = $this->createMock(Client::class);

        // Initialize the service with fake config
        $this->service = new ApolloEnrichmentService([
            'api_key'  => 'test_api_key',
            'base_uri' => 'https://api.apollo.io/v1'
        ]);

        // Inject the mock Guzzle client into the service
        $reflection = new \ReflectionClass($this->service);
        $clientProp = $reflection->getProperty('client');
        $clientProp->setAccessible(true);
        $clientProp->setValue($this->service, $this->mockClient);
    }

    /**
     * @throws GuzzleException
     */
    public function testEnrichPersonSuccess()
    {
        // Prepare a mock JSON response
        $mockApiResponse = [
            'person' => [
                'id' => 'fake-id',
                'name' => 'Test Person'
            ]
        ];

        // Configure mock client to return a 200 response with our JSON
        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/people/match', $this->anything()) // We could assert specific 'json' and 'query'
            ->willReturn(new Response(200, [], json_encode($mockApiResponse)));

        // Call the method
        $result = $this->service->enrichPerson([
            'first_name' => 'Test',
            'domain'     => 'example.com'
        ]);

        // Assert we got the mock response data
        $this->assertArrayHasKey('person', $result);
        $this->assertEquals('Test Person', $result['person']['name']);
    }

    /**
     * @throws GuzzleException
     */
    public function testEnrichPersonException()
    {
        // Simulate a Guzzle exception
        $this->mockClient->method('post')
            ->willThrowException(new \Exception('Something went wrong'));

        $result = $this->service->enrichPerson([
            'first_name' => 'Fail',
            'domain'     => 'error.com'
        ]);

        $this->assertTrue($result['error']);
        $this->assertStringContainsString('Something went wrong', $result['message']);
    }

    /**
     * @throws GuzzleException
     */
    public function testBulkEnrichPeopleSuccess()
    {
        $mockApiResponse = [
            'bulk_result' => [
                ['person' => 'Person A'],
                ['person' => 'Person B']
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/people/bulk_match', $this->anything())
            ->willReturn(new Response(200, [], json_encode($mockApiResponse)));

        $peopleData = [
            ['first_name' => 'Alice', 'domain' => 'example.com'],
            ['first_name' => 'Bob',   'domain' => 'example.org']
        ];

        $result = $this->service->bulkEnrichPeople($peopleData);
        $this->assertArrayHasKey('bulk_result', $result);
        $this->assertCount(2, $result['bulk_result']);
    }

    public function testEnrichOrganizationSuccess()
    {
        $mockApiResponse = [
            'organization' => [
                'id'   => 'org-123',
                'name' => 'Fake Org'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/organizations/enrich', $this->anything())
            ->willReturn(new Response(200, [], json_encode($mockApiResponse)));

        $result = $this->service->enrichOrganization([
            'domain' => 'fakeorg.com'
        ]);

        $this->assertArrayHasKey('organization', $result);
        $this->assertEquals('Fake Org', $result['organization']['name']);
    }

    /**
     * @throws GuzzleException
     */
    public function testBulkEnrichOrganizationsSuccess()
    {
        $mockApiResponse = [
            'organizations' => [
                ['domain' => 'apollo.io'],
                ['domain' => 'microsoft.com']
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/organizations/bulk_enrich', $this->anything())
            ->willReturn(new Response(200, [], json_encode($mockApiResponse)));

        $domains = ['apollo.io', 'microsoft.com'];
        $result = $this->service->bulkEnrichOrganizations($domains);

        $this->assertArrayHasKey('organizations', $result);
        $this->assertCount(2, $result['organizations']);
    }
}
