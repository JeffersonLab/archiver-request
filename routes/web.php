<?php

use App\Model\ArchiverGroup;
use App\Model\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * TODO:
 *   Remove groups from blade view -- no longer used.
 *   Autocomplete for username field
 *   Generate Email
 *   Client Side form validation
 *   Server side form validation
 *   Client side validation errors display
 *
 */


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

Route::get('/', 'Controller@show');

Route::post('/', 'Controller@submit');

Route::get('/data/staff', 'Controller@staff');


Route::get('/test', function (Request $request) {
    $query = $request->get('q',null);
    if ($query && strlen($query) > 2) {
        dd(Staff::whereNotNull('username')
            ->where('username', 'LIKE', '%' . $request->get('q') . '%')
            ->get()
            ->pluck('username'));
    }
});









