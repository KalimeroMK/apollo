<?php

namespace Kalimeromk\Apollo;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApolloAccountService
{
    /**
     * Guzzle client instance used to communicate with Apollo's API.
     */
    protected Client $client;

    /**
     * Constructor that sets up the Guzzle client with the required headers.
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
     * Create an Account
     *
     * Sends a POST request to /accounts to add a new account (company) to your Apollo team.
     *
     * Example of $accountData:
     * [
     *   'name'            => 'IrishConvertizers Inc.',
     *   'domain'          => 'irishconvertizers.ie',
     *   'owner_id'        => 'SOME_APOLLO_USER_ID',
     *   'account_stage_id'=> 'SOME_STAGE_ID',
     *   'phone'           => '+353 123456789',
     *   'raw_address'     => 'Dublin, Ireland'
     * ]
     *
     * @param  array  $accountData  Parameters for creating the account according to Apollo's docs
     * @return array               The JSON response from the API or an error array
     * @throws GuzzleException
     */
    public function createAccount(array $accountData = []): array
    {
        try {
            $response = $this->client->post('/accounts', [
                'json' => $accountData,
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
     * Update an Account
     *
     * Sends a PUT request to /accounts/{account_id} to update an existing account in Apollo.
     *
     * Example usage:
     * $service->updateAccount('66ebfa95cc239b212b81ad', [
     *     'name'  => 'The Fast Irish Copywriters',
     *     'domain'=> 'irishcopywriters.com',
     *     'phone' => '+353 888-999-7777',
     *     ...
     * ]);
     *
     * @param  string  $accountId  The ID of the account to update.
     * @param  array   $updateData  Associative array of fields to update (e.g. name, domain, phone).
     * @return array              The JSON response from the API or an error array.
     * @throws GuzzleException
     */
    public function updateAccount(string $accountId, array $updateData = []): array
    {
        try {
            $endpoint = '/accounts/'.$accountId;

            $response = $this->client->request('PUT', $endpoint, [
                'json' => $updateData,
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
     * Search for Accounts
     *
     * Sends a POST request to /accounts/search to retrieve accounts
     * that have been added to your team's Apollo account.
     * You can pass filters like 'q_organization_name', 'account_stage_ids',
     * 'sort_by_field', 'sort_ascending', etc., plus pagination.
     *
     * @param  array  $params  Associative array of search filters (e.g. ['q_organization_name' => ['microsoft']])
     * @param  int    $page  The page number (default is 1).
     * @param  int    $perPage  The number of results per page (default is 25).
     * @return array           The JSON response from Apollo or an error array.
     * @throws GuzzleException
     */
    public function searchAccounts(array $params = [], int $page = 1, int $perPage = 25): array
    {
        try {
            // Merge any given filters with pagination settings
            $payload = array_merge($params, [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            // Make the POST request to /accounts/search
            $response = $this->client->post('/accounts/search', [
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
     * Update Account Stage for Multiple Accounts
     *
     * Sends a POST request to /accounts/bulk_update to update the account stage
     * for several accounts in your team's Apollo account.
     *
     * Example usage:
     * $service->updateAccountStageForMultipleAccounts(['66ebfa95cc239b212b81ad', 'someOtherId'], '6075a7b08a01018ea50a6d07');
     *
     * @param  array   $accountIds  An array of account IDs you want to update.
     * @param  string  $accountStageId  The new stage ID to assign to these accounts.
     * @return array                 The JSON response from Apollo or an error array.
     * @throws GuzzleException
     */
    public function updateAccountStageForMultipleAccounts(array $accountIds, string $accountStageId): array
    {
        try {
            // Endpoint is /accounts/bulk_update
            // We provide account_ids and account_stage_id in the JSON body
            $payload = [
                'account_ids' => $accountIds,
                'account_stage_id' => $accountStageId,
            ];

            $response = $this->client->post('/accounts/bulk_update', [
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
     * Update Account Owner for Multiple Accounts
     *
     * Sends a POST request to /accounts/update_owners to assign
     * ownership of multiple accounts to a specific user (owner).
     *
     * Example usage:
     * $service->updateAccountOwnerForMultipleAccounts(
     *     ['66ebfa95cc239b212b81ad', 'someOtherId'],
     *     '60837298b16c9379abef'
     * );
     *
     * @param  array   $accountIds  An array of account IDs that you want to assign to the new owner.
     * @param  string  $ownerId  The ID of the user in your Apollo account who will become the new owner.
     * @return array              The JSON response from Apollo or an error array.
     * @throws GuzzleException
     */
    public function updateAccountOwnerForMultipleAccounts(array $accountIds, string $ownerId): array
    {
        try {
            // Endpoint is /accounts/update_owners
            // We provide account_ids and owner_id in the JSON body
            $payload = [
                'account_ids' => $accountIds,
                'owner_id' => $ownerId,
            ];

            $response = $this->client->post('/accounts/update_owners', [
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
     * List Account Stages
     *
     * Sends a GET request to /account_stages to retrieve all available
     * account stages in your team's Apollo account. This endpoint does
     * not require parameters.
     *
     * Example usage:
     * $stages = $service->listAccountStages();
     *
     * @return array  The JSON response from Apollo or an error array.
     * @throws GuzzleException
     */
    public function listAccountStages(): array
    {
        try {
            $response = $this->client->get('/account_stages');

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

}