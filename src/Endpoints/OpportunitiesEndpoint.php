<?php

namespace Wjbecker\CurrentRms\Endpoints;

use Wjbecker\CurrentRms\Data\OpportunityData;

class OpportunitiesEndpoint extends BaseEndpoint
{
    protected string $endpoint = '/opportunities';
    protected string $dataClass = OpportunityData::class;
    protected int $maxPageSize = 25;

    /**
     * Checkout an opportunity (convert to order).
     *
     * @param  array<string, mixed>  $data
     */
    public function checkout(array $data): OpportunityData
    {
        $response = $this->post('/checkout', $data);

        return OpportunityData::from($response['opportunity']);
    }

    /**
     * Clone an existing opportunity.
     */
    public function clone(int $id): OpportunityData
    {
        $response = $this->post("/{$id}/clone");

        return OpportunityData::from($response['opportunity']);
    }

    /**
     * Get a scoped endpoint for opportunity items within a specific opportunity.
     *
     * This allows you to work with nested routes:
     * - $client->opportunities()->items(123)->list()
     * - $client->opportunities()->items(123)->find(456)
     * - $client->opportunities()->items(123)->create([...])
     */
    public function items(int $opportunityId): ScopedOpportunityItemsEndpoint
    {
        return new ScopedOpportunityItemsEndpoint($this->client, $opportunityId);
    }
}
