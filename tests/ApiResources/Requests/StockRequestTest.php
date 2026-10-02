<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\ApiResources\Requests;

use Closure;
use Igniter\Api\ApiResources\Requests\StockRequest;
use Igniter\Cart\Models\MenuItemOptionValue;
use Igniter\Local\Models\Location;

it('accepts menu option value stockables', function(): void {
    $location = Location::factory()->create();
    $optionValue = MenuItemOptionValue::factory()->create();
    $request = StockRequest::create('/', 'POST', [
        'location_id' => $location->getKey(),
        'stockable_type' => 'menu_option_values',
        'stockable_id' => $optionValue->getKey(),
        'is_tracked' => true,
    ]);

    $validator = validator($request->all(), $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('fails when stockable does not exist', function(): void {
    $location = Location::factory()->create();
    $request = StockRequest::create('/', 'POST', [
        'location_id' => $location->getKey(),
        'stockable_type' => 'menus',
        'stockable_id' => 999999,
        'is_tracked' => true,
    ]);

    $validator = validator($request->all(), $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('stockable_id'))->toContain('invalid');
});

it('treats unknown stockable types as invalid in the closure', function(): void {
    $request = StockRequest::create('/', 'POST', [
        'stockable_type' => 'unknown',
        'stockable_id' => 1,
    ]);
    $rules = $request->rules();
    $closure = collect($rules['stockable_id'])->first(fn($rule): bool => $rule instanceof Closure);

    $message = null;
    $closure('stockable_id', 1, function(string $fail) use (&$message): void {
        $message = $fail;
    });

    expect($message)->toBe('The selected stockable is invalid.');
});
