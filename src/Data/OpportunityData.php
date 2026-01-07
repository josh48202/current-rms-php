<?php

namespace Wjbecker\CurrentRms\Data;

use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Endpoints\ScopedOpportunityItemsEndpoint;

class OpportunityData
{
    /**
     * The client instance for lazy loading relationships.
     */
    protected ?CurrentRmsClient $client = null;
    /**
     * Create a new opportunity data instance.
     */
    public function __construct(
        public ?int $id = null,
        public ?int $store_id = null,
        public ?int $project_id = null,
        public ?int $member_id = null,
        public ?int $billing_address_id = null,
        public ?int $venue_id = null,
        public ?int $tax_class_id = null,
        public ?string $subject = null,
        public ?string $description = null,
        public ?string $number = null,
        public ?string $starts_at = null,
        public ?string $ends_at = null,
        public ?string $charge_starts_at = null,
        public ?string $charge_ends_at = null,
        public ?string $ordered_at = null,
        public ?string $quote_invalid_at = null,
        public ?int $state = null,
        public ?string $state_name = null,
        public ?bool $use_chargeable_days = null,
        public ?int $chargeable_days = null,
        public ?bool $open_ended_rental = null,
        public ?int $status = null,
        public ?string $status_name = null,
        public ?bool $invoiced = null,
        public ?int $rating = null,
        public ?string $revenue = null,
        public ?bool $customer_collecting = null,
        public ?bool $customer_returning = null,
        public ?string $reference = null,
        public ?string $external_description = null,
        public ?string $delivery_instructions = null,
        public ?int $owned_by = null,
        public ?string $prep_starts_at = null,
        public ?string $prep_ends_at = null,
        public ?string $load_starts_at = null,
        public ?string $load_ends_at = null,
        public ?string $deliver_starts_at = null,
        public ?string $deliver_ends_at = null,
        public ?string $setup_starts_at = null,
        public ?string $setup_ends_at = null,
        public ?string $show_starts_at = null,
        public ?string $show_ends_at = null,
        public ?string $takedown_starts_at = null,
        public ?string $takedown_ends_at = null,
        public ?string $collect_starts_at = null,
        public ?string $collect_ends_at = null,
        public ?string $unload_starts_at = null,
        public ?string $unload_ends_at = null,
        public ?string $deprep_starts_at = null,
        public ?string $deprep_ends_at = null,
        public ?string $charge_total = null,
        public ?string $charge_excluding_tax_total = null,
        public ?string $charge_including_tax_total = null,
        public ?string $rental_charge_total = null,
        public ?string $sale_charge_total = null,
        public ?string $surcharge_total = null,
        public ?string $service_charge_total = null,
        public ?string $tax_total = null,
        public ?string $original_rental_charge_total = null,
        public ?string $original_sale_charge_total = null,
        public ?string $original_surcharge_total = null,
        public ?string $original_service_charge_total = null,
        public ?string $original_tax_total = null,
        public ?string $replacement_charge_total = null,
        public ?string $provisional_cost_total = null,
        public ?string $actual_cost_total = null,
        public ?string $predicted_cost_total = null,
        public ?string $weight_total = null,
        public ?bool $item_returned = null,
        public ?bool $prices_include_tax = null,
        public ?bool $pricing_locked = null,
        public ?int $latest_approval_document_status = null,
        public ?bool $has_deal_price = null,
        public ?bool $has_opportunity_deal = null,
        public ?bool $auto_costing_enabled = null,
        public ?int $source_opportunity_id = null,
        public ?array $tag_list = null,
        public ?array $assigned_surcharge_group_ids = null,
        public ?array $custom_fields = null,
        public ?array $participants = null,
        public ?array $owner = null,
        public ?array $member = null,
        public ?array $billing_address = null,
        public ?array $venue = null,
        public ?array $opportunity_surcharges = null,
        public ?array $opportunity_items = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    /**
     * Create instance from array.
     *
     * @param  array<string, mixed>  $data
     * @param  CurrentRmsClient|null  $client  Optional client for lazy loading relationships
     */
    public static function from(array $data, ?CurrentRmsClient $client = null): self
    {
        $instance = new self(
            id: $data['id'] ?? null,
            store_id: $data['store_id'] ?? null,
            project_id: $data['project_id'] ?? null,
            member_id: $data['member_id'] ?? null,
            billing_address_id: $data['billing_address_id'] ?? null,
            venue_id: $data['venue_id'] ?? null,
            tax_class_id: $data['tax_class_id'] ?? null,
            subject: $data['subject'] ?? null,
            description: $data['description'] ?? null,
            number: $data['number'] ?? null,
            starts_at: $data['starts_at'] ?? null,
            ends_at: $data['ends_at'] ?? null,
            charge_starts_at: $data['charge_starts_at'] ?? null,
            charge_ends_at: $data['charge_ends_at'] ?? null,
            ordered_at: $data['ordered_at'] ?? null,
            quote_invalid_at: $data['quote_invalid_at'] ?? null,
            state: $data['state'] ?? null,
            state_name: $data['state_name'] ?? null,
            use_chargeable_days: $data['use_chargeable_days'] ?? null,
            chargeable_days: $data['chargeable_days'] ?? null,
            open_ended_rental: $data['open_ended_rental'] ?? null,
            status: $data['status'] ?? null,
            status_name: $data['status_name'] ?? null,
            invoiced: $data['invoiced'] ?? null,
            rating: $data['rating'] ?? null,
            revenue: $data['revenue'] ?? null,
            customer_collecting: $data['customer_collecting'] ?? null,
            customer_returning: $data['customer_returning'] ?? null,
            reference: $data['reference'] ?? null,
            external_description: $data['external_description'] ?? null,
            delivery_instructions: $data['delivery_instructions'] ?? null,
            owned_by: $data['owned_by'] ?? null,
            prep_starts_at: $data['prep_starts_at'] ?? null,
            prep_ends_at: $data['prep_ends_at'] ?? null,
            load_starts_at: $data['load_starts_at'] ?? null,
            load_ends_at: $data['load_ends_at'] ?? null,
            deliver_starts_at: $data['deliver_starts_at'] ?? null,
            deliver_ends_at: $data['deliver_ends_at'] ?? null,
            setup_starts_at: $data['setup_starts_at'] ?? null,
            setup_ends_at: $data['setup_ends_at'] ?? null,
            show_starts_at: $data['show_starts_at'] ?? null,
            show_ends_at: $data['show_ends_at'] ?? null,
            takedown_starts_at: $data['takedown_starts_at'] ?? null,
            takedown_ends_at: $data['takedown_ends_at'] ?? null,
            collect_starts_at: $data['collect_starts_at'] ?? null,
            collect_ends_at: $data['collect_ends_at'] ?? null,
            unload_starts_at: $data['unload_starts_at'] ?? null,
            unload_ends_at: $data['unload_ends_at'] ?? null,
            deprep_starts_at: $data['deprep_starts_at'] ?? null,
            deprep_ends_at: $data['deprep_ends_at'] ?? null,
            charge_total: $data['charge_total'] ?? null,
            charge_excluding_tax_total: $data['charge_excluding_tax_total'] ?? null,
            charge_including_tax_total: $data['charge_including_tax_total'] ?? null,
            rental_charge_total: $data['rental_charge_total'] ?? null,
            sale_charge_total: $data['sale_charge_total'] ?? null,
            surcharge_total: $data['surcharge_total'] ?? null,
            service_charge_total: $data['service_charge_total'] ?? null,
            tax_total: $data['tax_total'] ?? null,
            original_rental_charge_total: $data['original_rental_charge_total'] ?? null,
            original_sale_charge_total: $data['original_sale_charge_total'] ?? null,
            original_surcharge_total: $data['original_surcharge_total'] ?? null,
            original_service_charge_total: $data['original_service_charge_total'] ?? null,
            original_tax_total: $data['original_tax_total'] ?? null,
            replacement_charge_total: $data['replacement_charge_total'] ?? null,
            provisional_cost_total: $data['provisional_cost_total'] ?? null,
            actual_cost_total: $data['actual_cost_total'] ?? null,
            predicted_cost_total: $data['predicted_cost_total'] ?? null,
            weight_total: $data['weight_total'] ?? null,
            item_returned: $data['item_returned'] ?? null,
            prices_include_tax: $data['prices_include_tax'] ?? null,
            pricing_locked: $data['pricing_locked'] ?? null,
            latest_approval_document_status: $data['latest_approval_document_status'] ?? null,
            has_deal_price: $data['has_deal_price'] ?? null,
            has_opportunity_deal: $data['has_opportunity_deal'] ?? null,
            auto_costing_enabled: $data['auto_costing_enabled'] ?? null,
            source_opportunity_id: $data['source_opportunity_id'] ?? null,
            tag_list: $data['tag_list'] ?? null,
            assigned_surcharge_group_ids: $data['assigned_surcharge_group_ids'] ?? null,
            custom_fields: $data['custom_fields'] ?? null,
            participants: $data['participants'] ?? null,
            owner: $data['owner'] ?? null,
            member: $data['member'] ?? null,
            billing_address: $data['billing_address'] ?? null,
            venue: $data['venue'] ?? null,
            opportunity_surcharges: $data['opportunity_surcharges'] ?? null,
            opportunity_items: isset($data['opportunity_items'])
                ? array_map(fn($item) => OpportunityItemData::from($item, $client), $data['opportunity_items'])
                : null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );

        $instance->client = $client;

        return $instance;
    }

    /**
     * Get a scoped endpoint for this opportunity's items.
     *
     * Enables lazy loading of opportunity items:
     * ```php
     * foreach ($client->opportunities()->list() as $opportunity) {
     *     $items = $opportunity->items()->get();
     * }
     * ```
     *
     * @throws \RuntimeException If no client is available
     */
    public function items(): ScopedOpportunityItemsEndpoint
    {
        if ($this->client === null) {
            throw new \RuntimeException(
                'Cannot lazy load items: no client available. '.
                'Use $client->opportunities()->items($id) instead.'
            );
        }

        if ($this->id === null) {
            throw new \RuntimeException(
                'Cannot lazy load items: opportunity has no ID.'
            );
        }

        return new ScopedOpportunityItemsEndpoint($this->client, $this->id);
    }

    /**
     * Check if opportunity is in draft state.
     */
    public function isDraft(): bool
    {
        return $this->state === 1;
    }

    /**
     * Check if opportunity is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 0;
    }

    /**
     * Get the opportunity subject/title.
     */
    public function getTitle(): ?string
    {
        return $this->subject;
    }

    /**
     * Get the member (customer) name.
     */
    public function getMemberName(): ?string
    {
        return $this->member['name'] ?? null;
    }

    /**
     * Get the owner name.
     */
    public function getOwnerName(): ?string
    {
        return $this->owner['name'] ?? null;
    }

    /**
     * Get a custom field value by key.
     */
    public function getCustomField(string $key): mixed
    {
        return $this->custom_fields[$key] ?? null;
    }
}
