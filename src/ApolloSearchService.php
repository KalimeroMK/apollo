<?php

namespace Kalimeromk\Apollo;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApolloSearchService
{
    /**
     * Guzzle client instance to interact with the Apollo API.
     */
    protected Client $client;

    /**
     * Constructor that sets up the Guzzle client with necessary headers.
     *
     * @param  array  $config  An associative array containing 'api_key' and 'base_uri',
     *                       for example:
     *                       [
     *                         'api_key'  => 'YOUR_APOLLO_API_KEY',
     *                         'base_uri' => 'https://api.apollo.io/v1'
     *                       ]
     */
    public function __construct(array $config)
    {
        $this->client = new Client([
            'base_uri' => $config['base_uri'] ?? 'https://api.apollo.io/v1',
            'headers' => [
                'Authorization' => 'Bearer '.($config['api_key'] ?? ''),
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
                'accept' => 'application/json',
            ],
        ]);
    }

    /**
     * People Search
     *
     * Sends a POST request to /people/search to find people in Apollo's database.
     * You can pass various filters described in Apollo's docs, such as 'person_titles',
     * 'person_location', 'person_seniorities', etc. You can also include pagination
     * parameters: 'page' and 'per_page'.
     *
     * @param  array  $searchParams  Associative array of search filters.
     * @param  int    $page  Page number for pagination.
     * @param  int    $perPage  Number of records per page (default is 25).
     * @return array               The response from the API or an error array.
     * @throws GuzzleException
     */
    public function searchPeople(array $searchParams = [], int $page = 1, int $perPage = 25): array
    {
        try {
            $payload = array_merge($searchParams, [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            $response = $this->client->post('/people/search', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Organization Search
     *
     * Sends a POST request to /organizations/search to find organizations.
     * You can pass different filters as described in Apollo's docs, such as
     * 'org_name', 'org_locations', etc.
     *
     * @param  array  $searchParams  Associative array of search filters.
     * @return array               The response from the API or an error array.
     * @throws GuzzleException
     */
    public function searchOrganizations(array $searchParams = []): array
    {
        try {
            $response = $this->client->post('/organizations/search', [
                'json' => $searchParams,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Organization Job Postings Search (Optional)
     *
     * Sends a POST request to /organizations/job_postings/search
     * to find job postings for certain organizations, based on filters like
     * 'org_ids', 'keywords', 'location', etc.
     *
     * @param  array  $searchParams  Associative array of parameters, such as:
     *                             [
     *                               'org_ids' => ['some_apollo_org_id'],
     *                               'keywords' => ['developer'],
     *                               ...
     *                             ]
     * @return array               The response from the API or an error array.
     * @throws GuzzleException
     */
    public function searchOrganizationJobPostings(array $searchParams = []): array
    {
        try {
            $response = $this->client->post('/organizations/job_postings/search', [
                'json' => $searchParams,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}
