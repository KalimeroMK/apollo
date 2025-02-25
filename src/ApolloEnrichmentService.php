<?php

namespace Kalimeromk\Apollo;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApolloEnrichmentService
{
    public Client $client;

    public function __construct(array $config)
    {
        $this->client = new Client([
            'base_uri' => $config['base_uri'] ?? 'https://api.apollo.io/api/v1',
            'headers' => [
                'x-api-key' => $config['api_key'] ?? '',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ]
        ]);
    }


    /**
     * People Enrichment (Single)
     *
     * Makes a POST request to /people/match to enrich data for one person.
     * The $params array can include keys such as 'first_name', 'last_name',
     * 'email', 'domain', etc. The optional $revealPersonalEmails and
     * $revealPhoneNumber parameters determine whether Apollo attempts to
     * return personal emails and phone numbers (increasing credit costs).
     *
     * @param  array  $params  Person details (e.g. ['first_name'=>'Tim', 'domain'=>'apollo.io'])
     * @param  bool   $revealPersonalEmails  Whether to reveal personal emails
     * @param  bool   $revealPhoneNumber  Whether to reveal phone numbers
     * @return array                        API response or an error array
     * @throws GuzzleException
     */
    public function enrichPerson(
        array $params = [],
        bool $revealPersonalEmails = false,
        bool $revealPhoneNumber = false
    ): array {
        try {
            $query = [
                'reveal_personal_emails' => $revealPersonalEmails ? 'true' : 'false',
                'reveal_phone_number' => $revealPhoneNumber ? 'true' : 'false',
            ];

            $response = $this->client->post('/people/match', [
                'query' => $query,
                'json' => $params,
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
     * Bulk People Enrichment
     *
     * Makes a POST request to /people/bulk_match to enrich data for multiple people.
     * Expects an array of person objects (similar structure to the single enrichment).
     *
     * @param  array  $peopleData  An array of person data, each entry might look like ['first_name'=>'Tim', 'domain'=>'apollo.io']
     * @return array             API response or an error array
     * @throws GuzzleException
     */
    public function bulkEnrichPeople(array $peopleData = []): array
    {
        try {
            $response = $this->client->post('/people/bulk_match', [
                'json' => [
                    'people' => $peopleData
                ],
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
     * Organization Enrichment (Single)
     *
     * Makes a POST request to /organizations/enrich to enrich data for one company.
     * Usually requires at least 'domain' => 'example.com' in the $params array.
     *
     * @param  array  $params  An array that can include 'domain' and other fields allowed by Apollo
     * @return array        API response or an error array
     * @throws GuzzleException
     */
    public function enrichOrganization(array $params = []): array
    {
        try {
            $response = $this->client->post('/organizations/enrich', [
                'json' => $params,
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
     * Bulk Organization Enrichment
     *
     * Makes a POST request to /organizations/bulk_enrich to enrich data for up to 10 companies
     * in a single call. You must provide an array of domains, for example:
     * ['apollo.io', 'microsoft.com'].
     *
     * @param  array  $domains  An array of domains you want to enrich, e.g. ['apollo.io', 'microsoft.com']
     * @return array          API response or an error array
     * @throws GuzzleException
     */
    public function bulkEnrichOrganizations(array $domains = []): array
    {
        try {
            $queryParams = http_build_query(['domains' => $domains], '', '&', PHP_QUERY_RFC3986);

            $response = $this->client->post("https://api.apollo.io/api/v1/organizations/bulk_enrich?$queryParams");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }




}