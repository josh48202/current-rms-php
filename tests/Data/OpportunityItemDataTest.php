<?php

use Wjbecker\CurrentRms\Data\OpportunityItemData;

it('creates opportunity item data from array', function () {
    $data = [
        'id' => 1,
        'opportunity_id' => 100,
        'product_id' => 50,
        'quantity' => 5,
        'item_type' => 'rental',
        'name' => 'Test Product',
        'sku' => 'PROD-001',
        'price' => '100.00',
        'product' => ['name' => 'Test Product', 'sku' => 'PROD-001'],
    ];

    $item = OpportunityItemData::from($data);

    expect($item->id)->toBe(1);
    expect($item->opportunity_id)->toBe(100);
    expect($item->product_id)->toBe(50);
    expect($item->quantity)->toBe(5);
    expect($item->item_type)->toBe('rental');
    expect($item->name)->toBe('Test Product');
    expect($item->sku)->toBe('PROD-001');
});

it('checks if item is rental type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 1]);

    expect($item->isRental())->toBeTrue();
});

it('checks if item is not rental type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 2]);

    expect($item->isRental())->toBeFalse();
});

it('checks if item is sale type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 2]);

    expect($item->isSale())->toBeTrue();
});

it('checks if item is not sale type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 1]);

    expect($item->isSale())->toBeFalse();
});

it('checks if item is service type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 3]);

    expect($item->isService())->toBeTrue();
});

it('checks if item is not service type', function () {
    $item = OpportunityItemData::from(['transaction_type' => 1]);

    expect($item->isService())->toBeFalse();
});

it('gets product name from direct property', function () {
    $item = OpportunityItemData::from(['name' => 'Direct Product Name']);

    expect($item->getProductName())->toBe('Direct Product Name');
});

it('gets product name from nested item array', function () {
    $item = OpportunityItemData::from([
        'item' => ['name' => 'Nested Item Name'],
    ]);

    expect($item->getProductName())->toBe('Nested Item Name');
});

it('returns null when product name is not set', function () {
    $item = OpportunityItemData::from([]);

    expect($item->getProductName())->toBeNull();
});

it('gets product sku from direct property', function () {
    $item = OpportunityItemData::from(['sku' => 'SKU-123']);

    expect($item->getProductSku())->toBe('SKU-123');
});

it('gets product sku from nested item array', function () {
    $item = OpportunityItemData::from([
        'item' => ['barcode' => 'NESTED-BARCODE'],
    ]);

    expect($item->getProductSku())->toBe('NESTED-BARCODE');
});

it('returns null when product sku is not set', function () {
    $item = OpportunityItemData::from([]);

    expect($item->getProductSku())->toBeNull();
});

it('gets custom field value', function () {
    $item = OpportunityItemData::from([
        'custom_fields' => [
            'field1' => 'value1',
            'field2' => 'value2',
        ],
    ]);

    expect($item->getCustomField('field1'))->toBe('value1');
    expect($item->getCustomField('field2'))->toBe('value2');
});

it('returns null for non-existent custom field', function () {
    $item = OpportunityItemData::from([
        'custom_fields' => ['field1' => 'value1'],
    ]);

    expect($item->getCustomField('nonexistent'))->toBeNull();
});
