<?php

namespace Wjbecker\CurrentRms\Data;

use Wjbecker\CurrentRms\Client\CurrentRmsClient;

class ItemData
{
    /**
     * Create a new item data instance.
     */
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?array $tag_list = null,
        public ?int $allowed_stock_type = null,
        public ?string $allowed_stock_type_name = null,
        public ?int $stock_method = null,
        public ?string $stock_method_name = null,
        public ?string $buffer_percent = null,
        public ?int $post_rent_unavailability = null,
        public ?string $replacement_charge = null,
        public ?string $weight = null,
        public ?string $barcode = null,
        public ?bool $active = null,
        public ?bool $accessory_only = null,
        public ?bool $discountable = null,
        public ?int $product_group_id = null,
        public ?int $tax_class_id = null,
        public ?int $rental_revenue_group_id = null,
        public ?int $sale_revenue_group_id = null,
        public ?int $sub_rental_cost_group_id = null,
        public ?string $sub_rental_price = null,
        public ?int $sub_rental_rate_definition_id = null,
        public ?int $purchase_cost_group_id = null,
        public ?string $purchase_price = null,
        public ?array $assigned_inspection_ids = null,
        public ?array $custom_fields = null,
        public ?array $product_group = null,
        public ?array $tax_class = null,
        public ?array $icon = null,
        public ?array $rental_revenue_group = null,
        public ?array $sale_revenue_group = null,
        public ?array $sub_rental_cost_group = null,
        public ?array $purchase_cost_group = null,
        public ?array $accessories = null,
        public ?array $alternative_products = null,
        public ?array $attachments = null,
        public ?array $product_surcharges = null,
        public ?array $rental_rates = null,
        public ?array $sale_rates = null,
        public ?array $rental_rate = null,
        public ?array $sale_rate = null,
        public ?array $item_inspections = null,
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
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            tag_list: $data['tag_list'] ?? null,
            allowed_stock_type: $data['allowed_stock_type'] ?? null,
            allowed_stock_type_name: $data['allowed_stock_type_name'] ?? null,
            stock_method: $data['stock_method'] ?? null,
            stock_method_name: $data['stock_method_name'] ?? null,
            buffer_percent: $data['buffer_percent'] ?? null,
            post_rent_unavailability: $data['post_rent_unavailability'] ?? null,
            replacement_charge: $data['replacement_charge'] ?? null,
            weight: $data['weight'] ?? null,
            barcode: $data['barcode'] ?? null,
            active: $data['active'] ?? null,
            accessory_only: $data['accessory_only'] ?? null,
            discountable: $data['discountable'] ?? null,
            product_group_id: $data['product_group_id'] ?? null,
            tax_class_id: $data['tax_class_id'] ?? null,
            rental_revenue_group_id: $data['rental_revenue_group_id'] ?? null,
            sale_revenue_group_id: $data['sale_revenue_group_id'] ?? null,
            sub_rental_cost_group_id: $data['sub_rental_cost_group_id'] ?? null,
            sub_rental_price: $data['sub_rental_price'] ?? null,
            sub_rental_rate_definition_id: $data['sub_rental_rate_definition_id'] ?? null,
            purchase_cost_group_id: $data['purchase_cost_group_id'] ?? null,
            purchase_price: $data['purchase_price'] ?? null,
            assigned_inspection_ids: $data['assigned_inspection_ids'] ?? null,
            custom_fields: $data['custom_fields'] ?? null,
            product_group: $data['product_group'] ?? null,
            tax_class: $data['tax_class'] ?? null,
            icon: $data['icon'] ?? null,
            rental_revenue_group: $data['rental_revenue_group'] ?? null,
            sale_revenue_group: $data['sale_revenue_group'] ?? null,
            sub_rental_cost_group: $data['sub_rental_cost_group'] ?? null,
            purchase_cost_group: $data['purchase_cost_group'] ?? null,
            accessories: $data['accessories'] ?? null,
            alternative_products: $data['alternative_products'] ?? null,
            attachments: $data['attachments'] ?? null,
            product_surcharges: $data['product_surcharges'] ?? null,
            rental_rates: $data['rental_rates'] ?? null,
            sale_rates: $data['sale_rates'] ?? null,
            rental_rate: $data['rental_rate'] ?? null,
            sale_rate: $data['sale_rate'] ?? null,
            item_inspections: $data['item_inspections'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Check if item is active.
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }

    /**
     * Check if item is accessory only.
     */
    public function isAccessoryOnly(): bool
    {
        return $this->accessory_only === true;
    }

    /**
     * Check if item is discountable.
     */
    public function isDiscountable(): bool
    {
        return $this->discountable === true;
    }

    /**
     * Get the product group name.
     */
    public function getProductGroupName(): ?string
    {
        return $this->product_group['name'] ?? null;
    }

    /**
     * Get the tax class name.
     */
    public function getTaxClassName(): ?string
    {
        return $this->tax_class['name'] ?? null;
    }

    /**
     * Get the icon URL.
     */
    public function getIconUrl(): ?string
    {
        return $this->icon['url'] ?? null;
    }

    /**
     * Get the icon thumbnail URL.
     */
    public function getIconThumbUrl(): ?string
    {
        return $this->icon['thumb_url'] ?? null;
    }

    /**
     * Get a custom field value by key.
     */
    public function getCustomField(string $key): mixed
    {
        return $this->custom_fields[$key] ?? null;
    }
}
