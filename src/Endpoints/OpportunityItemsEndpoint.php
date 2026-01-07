<?php

namespace Wjbecker\CurrentRms\Endpoints;

use Wjbecker\CurrentRms\Data\OpportunityItemData;

class OpportunityItemsEndpoint extends BaseEndpoint
{
    protected string $endpoint = '/opportunity_items';
    protected string $dataClass = OpportunityItemData::class;
    protected int $maxPageSize = 100;
}
