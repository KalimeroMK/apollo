<?php

namespace Kalimeromk\Apollo;

use Exception;
use Illuminate\Support\Facades\Http;

class ApolloEnrichmentService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('apollo.base_url', 'https://api.apollo.io/api/v1');
        $this->apiKey = config('apollo.api_key');
    }

    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->{$method}("{$this->baseUrl}{$endpoint}", $data);

            return $response->json();
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function enrichPerson(array $params = [], bool $revealPersonalEmails = false, bool $revealPhoneNumber = false): array
    {
        return $this->request('post', '/people/match', [
            'reveal_personal_emails' => $revealPersonalEmails ? 'true' : 'false',
            'reveal_phone_number' => $revealPhoneNumber ? 'true' : 'false',
            ...$params,
        ]);
    }

    public function bulkEnrichPeople(array $peopleData = []): array
    {
        return $this->request('post', '/people/bulk_match', [
            'people' => $peopleData,
        ]);
    }

    public function enrichOrganization(array $params = []): array
    {
        return $this->request('post', '/organizations/enrich', $params);
    }

    public function bulkEnrichOrganizations(array $domains = []): array
    {
        return $this->request('post', '/organizations/bulk_enrich', [
            'domains' => $domains,
        ]);
    }
}
