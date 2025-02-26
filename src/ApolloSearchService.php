<?php

namespace Kalimeromk\Apollo;

use Exception;
use Illuminate\Support\Facades\Http;

class ApolloSearchService
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

    /**
     * People Search
     */
    public function searchPeople(array $searchParams = [], int $page = 1, int $perPage = 25): array
    {
        return $this->request('post', '/people/search', array_merge($searchParams, [
            'page' => $page,
            'per_page' => $perPage,
        ]));
    }

    /**
     * Organization Search
     */
    public function searchOrganizations(array $searchParams = []): array
    {
        return $this->request('post', '/organizations/search', $searchParams);
    }

    /**
     * Organization Job Postings Search
     */
    public function searchOrganizationJobPostings(array $searchParams = []): array
    {
        return $this->request('post', '/organizations/job_postings/search', $searchParams);
    }
}
