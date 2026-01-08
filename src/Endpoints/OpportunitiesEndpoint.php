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
     * Finalize check-in for an opportunity.
     *
     * This will finalize all opportunity item assets at checked-in status,
     * setting their status to Completed where the quantity outstanding is zero.
     *
     * Example structure:
     * [
     *   'return' => [
     *     'return_at' => '2025-01-15T18:00:00.000Z'
     *   ],
     *   'move_outstanding' => false,
     *   'complete_sales_items' => false
     * ]
     *
     * @param  int  $id  The opportunity ID
     * @param  array<string, mixed>  $data
     */
    public function finalizeCheckIn(int $id, array $data): OpportunityData
    {
        $response = $this->post("/{$id}/finalise", $data);

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
