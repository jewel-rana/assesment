<?php

use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $rates = new \App\Services\ExchangeRateCalculationService();
    echo CommonHelper::formatNumber($rates->calculate(500, 'JPY'));
});
