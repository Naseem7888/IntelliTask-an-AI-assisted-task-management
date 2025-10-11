<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Define API routes here

Route::middleware('api')->group(function () {
    // Example route
    Route::get('/users', function (Request $request) {
        // Logic to retrieve users
    });
});