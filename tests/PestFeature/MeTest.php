<?php

test('pest sample feature', function () {
    asUser()->get('/api/me')
        ->assertStatus(200)
        ->json();
});
