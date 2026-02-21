<?php

use App\Models\Stock;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('index page lists stocks', function () {
    $stock = Stock::create(['symbol' => 'BBCA']);

    get(route('stocks.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Stocks/Index')
                ->has('stocks', 1)
                ->where('stocks.0.symbol', 'BBCA')
        );
});

test('can add a stock', function () {
    post(route('stocks.store'), [
        'symbol' => 'BBRI',
    ])
        ->assertRedirect();

    assertDatabaseHas('stocks', [
        'symbol' => 'BBRI',
    ]);
});

test('cannot add duplicate stock', function () {
    Stock::create(['symbol' => 'BBCA']);

    post(route('stocks.store'), [
        'symbol' => 'BBCA',
    ])
        ->assertSessionHasErrors(['symbol']);

    expect(Stock::count())->toBe(1);
});

test('can delete a stock', function () {
    $stock = Stock::create(['symbol' => 'BMRI']);

    delete(route('stocks.destroy', $stock))
        ->assertRedirect();

    assertDatabaseMissing('stocks', [
        'id' => $stock->id,
    ]);
});
