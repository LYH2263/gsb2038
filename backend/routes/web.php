<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['name' => '茶文化 API', 'version' => '1.0']);
});
