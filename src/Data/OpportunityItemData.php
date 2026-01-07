<?php

namespace Wjbecker\CurrentRms\Data;

use Wjbecker\CurrentRms\Client\CurrentRmsClient;

class OpportunityItemData
{
    /**
     * Create a new opportunity item data instance.
     */
    public function __construct(
        public ?int $id = null,
        public ?int $opportunity_id = null,
        public ?int $item_id = null,
        public ?string $item_type = null,
        public ?int $opportunity_item_type = null,
        public ?string $name = null,
        public ?int $transaction_type = null,
        public ?int $accessory_inclusion_type = null,
        public ?int $accessory_mode = null,
        public ?int $quantity = null,
        public ?int $revenue_group_id = null,
        public ?int $rate_definition_id = null,
        public ?int $service_rate_type = null,
        public ?string $discount_percent = null,
        public ?bool $use_chargeable_days = null,
        public ?string $chargeable_days = null,
        public ?bool $sub_rent = null,
        public ?string $replacement_charge = null,
        public ?string $weight = null,
        public ?int $product_id = null,
        public ?string $sku = null,
        public ?string $description = null,
        public ?string $price = null,
        public ?string $charge = null,
        public ?string $rental_charge = null,
        public ?string $sale_charge = null,
        public ?string $service_charge = null,
        public ?string $tax = null,
        public ?string $total = null,
        public ?string $starts_at = null,
        public ?string $ends_at = null,
        public ?int $position = null,
        public ?bool $taxable = null,
        public ?array $custom_fields = null,
        public ?ItemData $item = null,
        public ?string $opportunity_item_type_name = null,
        public ?string $transaction_type_name = null,
        public ?string $accessory_inclusion_type_name = null,
        public ?string $accessory_mode_name = null,
        public ?int $status = null,
        public ?string $status_name = null,
        public ?string $service_rate_type_name = null,
        public ?string $path = null,
        public ?string $rental_charge_total = null,
        public ?string $sale_charge_total = null,
        public ?string $service_charge_total = null,
        public ?string $surcharge_total = null,
        public ?string $tax_total = null,
        public ?string $original_rental_charge_total = null,
        public ?string $original_sale_charge_total = null,
        public ?string $original_service_charge_total = null,
        public ?string $original_surcharge_total = null,
        public ?string $original_tax_total = null,
        public ?string $replacement_charge_total = null,
        public ?string $weight_total = null,
        public ?string $unit_base_charge = null,
        public ?string $unit_charge = null,
        public ?string $charge_amount = null,
        public ?string $taxable_charge_amount = null,
        public ?string $tax_amount = null,
        public ?string $surcharge_amount = null,
        public ?string $surcharge_tax_amount = null,
        public ?array $charging_periods = null,
        public ?string $charge_total = null,
        public ?string $charge_total_including_children = null,
        public ?string $weight_total_including_children = null,
        public ?string $replacement_charge_total_including_children = null,
        public ?array $lead_charging_period = null,
        public ?string $lead_charging_period_name = null,
        public ?bool $has_shortage = null,
        public ?bool $has_group_deal = null,
        public ?bool $is_in_deal = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    /**
     * Create instance from array.
     *
     * @param  array<string, mixed>  $data
     * @param  CurrentRmsClient|null  $client  Optional client (unused, for signature consistency)
     */
    public static function from(array $data, ?CurrentRmsClient $client = null): self
    {
        return new self(
            id: $data['id'] ?? null,
            opportunity_id: $data['opportunity_id'] ?? null,
            item_id: $data['item_id'] ?? null,
            item_type: $data['item_type'] ?? null,
            opportunity_item_type: $data['opportunity_item_type'] ?? null,
            name: $data['name'] ?? null,
            transaction_type: $data['transaction_type'] ?? null,
            accessory_inclusion_type: $data['accessory_inclusion_type'] ?? null,
            accessory_mode: $data['accessory_mode'] ?? null,
            quantity: $data['quantity'] ?? null,
            revenue_group_id: $data['revenue_group_id'] ?? null,
            rate_definition_id: $data['rate_definition_id'] ?? null,
            service_rate_type: $data['service_rate_type'] ?? null,
            discount_percent: $data['discount_percent'] ?? null,
            use_chargeable_days: $data['use_chargeable_days'] ?? null,
            chargeable_days: $data['chargeable_days'] ?? null,
            sub_rent: $data['sub_rent'] ?? null,
            replacement_charge: $data['replacement_charge'] ?? null,
            weight: $data['weight'] ?? null,
            product_id: $data['product_id'] ?? null,
            sku: $data['sku'] ?? null,
            description: $data['description'] ?? null,
            price: $data['price'] ?? null,
            charge: $data['charge'] ?? null,
            rental_charge: $data['rental_charge'] ?? null,
            sale_charge: $data['sale_charge'] ?? null,
            service_charge: $data['service_charge'] ?? null,
            tax: $data['tax'] ?? null,
            total: $data['total'] ?? null,
            starts_at: $data['starts_at'] ?? null,
            ends_at: $data['ends_at'] ?? null,
            position: $data['position'] ?? null,
            taxable: $data['taxable'] ?? null,
            custom_fields: $data['custom_fields'] ?? null,
            item: isset($data['item']) ? ItemData::from($data['item']) : null,
            opportunity_item_type_name: $data['opportunity_item_type_name'] ?? null,
            transaction_type_name: $data['transaction_type_name'] ?? null,
            accessory_inclusion_type_name: $data['accessory_inclusion_type_name'] ?? null,
            accessory_mode_name: $data['accessory_mode_name'] ?? null,
            status: $data['status'] ?? null,
            status_name: $data['status_name'] ?? null,
            service_rate_type_name: $data['service_rate_type_name'] ?? null,
            path: $data['path'] ?? null,
            rental_charge_total: $data['rental_charge_total'] ?? null,
            sale_charge_total: $data['sale_charge_total'] ?? null,
            service_charge_total: $data['service_charge_total'] ?? null,
            surcharge_total: $data['surcharge_total'] ?? null,
            tax_total: $data['tax_total'] ?? null,
            original_rental_charge_total: $data['original_rental_charge_total'] ?? null,
            original_sale_charge_total: $data['original_sale_charge_total'] ?? null,
            original_service_charge_total: $data['original_service_charge_total'] ?? null,
            original_surcharge_total: $data['original_surcharge_total'] ?? null,
            original_tax_total: $data['original_tax_total'] ?? null,
            replacement_charge_total: $data['replacement_charge_total'] ?? null,
            weight_total: $data['weight_total'] ?? null,
            unit_base_charge: $data['unit_base_charge'] ?? null,
            unit_charge: $data['unit_charge'] ?? null,
            charge_amount: $data['charge_amount'] ?? null,
            taxable_charge_amount: $data['taxable_charge_amount'] ?? null,
            tax_amount: $data['tax_amount'] ?? null,
            surcharge_amount: $data['surcharge_amount'] ?? null,
            surcharge_tax_amount: $data['surcharge_tax_amount'] ?? null,
            charging_periods: $data['charging_periods'] ?? null,
            charge_total: $data['charge_total'] ?? null,
            charge_total_including_children: $data['charge_total_including_children'] ?? null,
            weight_total_including_children: $data['weight_total_including_children'] ?? null,
            replacement_charge_total_including_children: $data['replacement_charge_total_including_children'] ?? null,
            lead_charging_period: $data['lead_charging_period'] ?? null,
            lead_charging_period_name: $data['lead_charging_period_name'] ?? null,
            has_shortage: $data['has_shortage'] ?? null,
            has_group_deal: $data['has_group_deal'] ?? null,
            is_in_deal: $data['is_in_deal'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Check if item is a rental type.
     */
    public function isRental(): bool
    {
        return $this->transaction_type === 1;
    }

    /**
     * Check if item is a sale type.
     */
    public function isSale(): bool
    {
        return $this->transaction_type === 2;
    }

    /**
     * Check if item is a service type.
     */
    public function isService(): bool
    {
        return $this->transaction_type === 3;
    }

    /**
     * Get the item/product name.
     */
    public function getItemName(): ?string
    {
        return $this->name ?? $this->item?->name ?? null;
    }

    /**
     * Get the item/product barcode.
     */
    public function getItemBarcode(): ?string
    {
        return $this->sku ?? $this->item?->barcode ?? null;
    }

    /**
     * Alias for getItemName() for backward compatibility.
     */
    public function getProductName(): ?string
    {
        return $this->getItemName();
    }

    /**
     * Alias for getItemBarcode() for backward compatibility.
     */
    public function getProductSku(): ?string
    {
        return $this->getItemBarcode();
    }

    /**
     * Get a custom field value by key.
     */
    public function getCustomField(string $key): mixed
    {
        return $this->custom_fields[$key] ?? null;
    }
}
