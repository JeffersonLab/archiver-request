<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [Controller::class, 'show']);

Route::post('/', [Controller::class, 'submit']);

Route::get('/data/staff', [Controller::class,'staff']);


Route::get('/test', function (Request $request) {
    $query = $request->get('q',null);
    if ($query && strlen($query) > 2) {
        dd(Staff::whereNotNull('username')
            ->where('username', 'LIKE', '%' . $request->get('q') . '%')
            ->get()
            ->pluck('username'));
    }
});
