<?php

use App\Model\ArchiverGroup;
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
    return view('main')
        ->with('groups', archiverGroups())
        ->with('groupTrees',array_values(array_map(function($obj){ return $obj->toArray(); }, archiverGroupTrees())));
});

Route::get('/test', function () {
    foreach (archiverGroupTrees() as $name => $tree){
        if ($name != 'Applications') dd($tree->toArray());
    }
});


/**
 * Return an array containing the hierarchical group listing.
 * Note: UserSet:* items are stripped not returned.
 */
function archiverGroups(){
    foreach (file(storage_path('app/groups.txt')) as $line){
        if ('UserSet' == substr($line,0,7)){
            continue;
        }
        $groups[] = trim($line);
    }
    return $groups;
}




/**
 * Creates a tree from the archive groups data where the keys correspond to the
 * the archiver groups.
 * ex:
 * source data:
 *   Areas
 *   Areas:FEL
 *   Areas:FEL:DriveLaser
 *   Areas:GTS
 * becomes:
 *  [
 *    Areas => [
 *         FEL => [
 *             DriveLaser => null,
 *         ],
 *         GTS => null,
 *    ]
 * ]
 */
function archiverGroupsTree(){
    $master = new ArchiverGroup('master');
    foreach (archiverGroups() as $group){
        $master->addFromString($group);
    }
    return $master->toJson(0);
}

function archiverGroupTrees(){
    $trees = [];
    foreach (archiverGroups() as $group){
        $parts = explode(':', $group, 2);
        $key = array_shift($parts);
        if (! array_key_exists($key, $trees)){
            $trees[$key] = new ArchiverGroup($key);
        }else if (! empty($parts)){
            $trees[$key]->addFromString($parts[0]);  //We already shifted original first elem into $key
        }
    }
    return $trees;
}


/**
 * Slots a single colon-delimited data row into the parent tree.
 *
 * ex:  Areas:FEL:DriveLaser
 * becomes:   Areas => [ FEL => [ DriveLaser => null ] ]
 *
 * @param $parent - reference to associative array where data will be inserted
 * @param $group - string representation of group and its lineage
 */
function makeChild(&$parent, $group){
    $parts = explode(':', $group, 2);
    $key = array_shift($parts);
    if (empty($parts)) {
        $parent[$key] = null;
    }else{
        if (! isset($parent[$key])){
            $parent[$key] = array();
        }
        // Since we have more parts to go, call ourselves recursively
        // to keep building.
        makeChild($parent[$key], array_shift($parts));
    }


}







