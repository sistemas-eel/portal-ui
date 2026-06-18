<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::view('/portal-ui-demo-minimal', 'portal-ui::examples.minimal-showcase')->name('portal-ui.demo.minimal');
    Route::view('/portal-ui-demo', 'portal-ui::examples.simple-showcase')->name('portal-ui.demo');
    Route::view('/portal-ui-demo-crud', 'portal-ui::examples.admin-crud-showcase')->name('portal-ui.demo.crud');
    Route::view('/portal-ui-demo-guest', 'portal-ui::examples.guest-showcase')->name('portal-ui.demo.guest');
});
