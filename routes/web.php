<?php

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
    return view('main')->with('groups', archiverGroups());
});


/**
 * Return an array containing the hierarchical group listing
 */
function archiverGroups(){
    $groups = [];
    foreach (file(storage_path('app/groups.txt')) as $line){
        if ('UserSet' == substr($line,0,7)){
            continue;
        }
        $groups[] = trim($line);
    }
    return $groups;
}







