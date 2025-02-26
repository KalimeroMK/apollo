<?php

namespace Kalimeromk\Apollo;

use Exception;
use Illuminate\Support\Facades\Http;

class ApolloAccountService
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
     * Create an Account
     */
    public function createAccount(array $accountData = []): array
    {
        return $this->request('post', '/accounts', $accountData);
    }

    /**
     * Update an Account
     */
    public function updateAccount(string $accountId, array $updateData = []): array
    {
        return $this->request('put', "/accounts/{$accountId}", $updateData);
    }

    /**
     * Search for Accounts
     */
    public function searchAccounts(array $params = [], int $page = 1, int $perPage = 25): array
    {
        return $this->request('post', '/accounts/search', array_merge($params, [
            'page' => $page,
            'per_page' => $perPage,
        ]));
    }

    /**
     * Update Account Stage for Multiple Accounts
     */
    public function updateAccountStageForMultipleAccounts(array $accountIds, string $accountStageId): array
    {
        return $this->request('post', '/accounts/bulk_update', [
            'account_ids' => $accountIds,
            'account_stage_id' => $accountStageId,
        ]);
    }

    /**
     * Update Account Owner for Multiple Accounts
     */
    public function updateAccountOwnerForMultipleAccounts(array $accountIds, string $ownerId): array
    {
        return $this->request('post', '/accounts/update_owners', [
            'account_ids' => $accountIds,
            'owner_id' => $ownerId,
        ]);
    }

    /**
     * List Account Stages
     */
    public function listAccountStages(): array
    {
        return $this->request('get', '/account_stages');
    }
}
