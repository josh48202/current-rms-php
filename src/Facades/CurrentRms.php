<?php

namespace Wjbecker\CurrentRms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Wjbecker\CurrentRms\Endpoints\OpportunitiesEndpoint opportunities()
 * @method static \Wjbecker\CurrentRms\Endpoints\OpportunityItemsEndpoint opportunityItems()
 * @method static array get(string $endpoint, array $query = [])
 * @method static array post(string $endpoint, array $data = [])
 * @method static array put(string $endpoint, array $data = [])
 * @method static array patch(string $endpoint, array $data = [])
 * @method static bool delete(string $endpoint)
 * @method static \Psr\Http\Message\ResponseInterface request(string $method, string $endpoint, array $options = [])
 * @method static \GuzzleHttp\Client getHttpClient()
 * @method static void setHttpClient(\GuzzleHttp\Client $client)
 *
 * @see \Wjbecker\CurrentRms\Client\CurrentRmsClient
 */
class CurrentRms extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Wjbecker\CurrentRms\Client\CurrentRmsClient::class;
    }
}
