<?php

test('returns a successful response', function () {
    $response = $this->get(route('stocks.index'));

    $response->assertOk();
});
