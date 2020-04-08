<?php

use App\Model\ArchiverGroup;
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



Route::get('/test', function () {
    foreach (archiverGroupTrees() as $name => $tree){
        if ($name != 'Applications') dd($tree->toArray());
    }
});









