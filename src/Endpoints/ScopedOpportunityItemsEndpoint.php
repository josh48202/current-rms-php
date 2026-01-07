<?php

namespace Wjbecker\CurrentRms\Endpoints;

use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Data\OpportunityItemData;

/**
 * Scoped endpoint for opportunity items within a specific opportunity.
 *
 * This endpoint is used for nested routes:
 * - GET /opportunities/{opportunity_id}/opportunity_items
 * - GET /opportunities/{opportunity_id}/opportunity_items/{id}
 * - POST /opportunities/{opportunity_id}/opportunity_items
 * - PUT /opportunities/{opportunity_id}/opportunity_items/{id}
 * - DELETE /opportunities/{opportunity_id}/opportunity_items/{id}
 */
class ScopedOpportunityItemsEndpoint extends BaseEndpoint
{
    protected string $endpoint;
    protected string $dataClass = OpportunityItemData::class;
    protected int $maxPageSize = 100;

    /**
     * Create a new scoped opportunity items endpoint.
     */
    public function __construct(
        CurrentRmsClient $client,
        protected int $opportunityId
    ) {
        // Build dynamic endpoint path based on opportunity ID
        $this->endpoint = "/opportunities/{$opportunityId}/opportunity_items";

        parent::__construct($client);
    }

}
