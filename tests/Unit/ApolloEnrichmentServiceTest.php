<?php

namespace Kalimeromk\Apollo\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Kalimeromk\Apollo\ApolloEnrichmentService;
use Orchestra\Testbench\TestCase;

class ApolloEnrichmentServiceTest extends TestCase
{
    protected ApolloEnrichmentService $apolloService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apolloService = new ApolloEnrichmentService();
    }

    /** @test */
    public function it_can_enrich_a_person()
    {
        Http::fake([
            'https://api.apollo.io/api/v1/people/match' => Http::response([
                'success' => true,
                'data' => ['name' => 'John Doe']
            ], 200),
        ]);

        $response = $this->apolloService->enrichPerson(['first_name' => 'John', 'domain' => 'example.com']);

        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertEquals('John Doe', $response['data']['name']);
    }

    /** @test */
    public function it_can_bulk_enrich_people()
    {
        Http::fake([
            'https://api.apollo.io/api/v1/people/bulk_match' => Http::response([
                'success' => true,
                'data' => [['name' => 'Jane Doe']]
            ], 200),
        ]);

        $response = $this->apolloService->bulkEnrichPeople([
            ['first_name' => 'Jane', 'domain' => 'example.com']
        ]);

        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertEquals('Jane Doe', $response['data'][0]['name']);
    }

    /** @test */
    public function it_can_enrich_an_organization()
    {
        Http::fake([
            'https://api.apollo.io/api/v1/organizations/enrich' => Http::response([
                'success' => true,
                'data' => ['company' => 'Example Corp']
            ], 200),
        ]);

        $response = $this->apolloService->enrichOrganization(['domain' => 'example.com']);

        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertEquals('Example Corp', $response['data']['company']);
    }

    /** @test */
    public function it_can_bulk_enrich_organizations()
    {
        Http::fake([
            'https://api.apollo.io/api/v1/organizations/bulk_enrich' => Http::response([
                'success' => true,
                'data' => [['company' => 'Example Corp']]
            ], 200),
        ]);

        $response = $this->apolloService->bulkEnrichOrganizations(['ogledalo.mk']);

        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertEquals('Example Corp', $response['data'][0]['company']);
    }

    /** @test */
    public function it_handles_api_errors()
    {
        Http::fake([
            '*' => Http::response(['error' => true, 'message' => 'API error'], 500),
        ]);

        $response = $this->apolloService->enrichPerson(['first_name' => 'John', 'domain' => 'example.com']);

        $this->assertIsArray($response);
        $this->assertTrue($response['error']);
        $this->assertEquals('API error', $response['message']);
    }

    /** @test */
    public function it_can_hit_live_api_to_search_for_a_domain()
    {
        $domain = 'youtube.com'; // Change this to a real domain if needed

        $response = $this->apolloService->enrichOrganization(['domain' => $domain]);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('organization', $response);

        // Ensure the response contains company-related data
        if (isset($response['organization']['name'])) {
            $this->assertNotEmpty($response['organization']['name']);
        }
    }
}
