<?php

namespace App\Http\Controllers;

use App\Model\ArchiverGroup;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $errors;

    public function __construct()    {
        $this->errors = new MessageBag();
    }

    /**
     * Display the form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(){
        return view('main')
            ->with('groupTrees', $this->groupTreesToOptions());
    }

    /**
     * Submlit the form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function submit(Request $request){
        $this->validateSubmit($request);
        if ($this->errors->isEmpty()){
            return response()->json($request);
        }
        return response()->json($this->errors, 400);
    }

    protected function validateSubmit(Request $request){
        switch ($request->get('requestType')){
            case 'add-channels' : return $this->validateAddChannels($request);

        }
        $this->errors->add('requestType', 'Unrecognized request type');
    }


    protected function validateAddChannels(Request $request){
        $this->validateCommonFields($request);
        $this->validateGroup($request->get('group'),$request->get('newGroup'));
    }

    protected function validateCommonFields(Request $request){
        $this->validateDeployment($request->get('deployment'));
        $this->validateUsername($request->get('username'));
    }

    protected function validateDeployment($deployment){
        if (! in_array($deployment,['OPS', 'DEV'])){
           $this->errors->add('deployment','Invalid deployment');
        }
    }

    protected function validateUsername($username){
        //TODO database lookup of username
        if (! $username) {
            $this->errors->add('username', 'A valid username is required');
        }
    }

    protected function validateGroup($group, $newGroup){
        if (! $group){
            $this->errors->add('group','An archiver group must be specified');
        }else if (! $newGroup){
            $this->validateExistingGroup($group);
        }
    }

    protected function validateExistingGroup($group){
        if (! in_array($group, $this->archiverGroups())){
            $this->errors->add('group','Not a valid existing group name');
        }
    }


    /**
     * Conver the groupTrees data into a format suitable to use as
     * the options array in the client-side vue-treeselect widget.
     *
     * @return array
     */
    protected function groupTreesToOptions(){
        $trees = [];
        foreach ($this->archiverGroupTrees() as $obj){
            $trees[] = $obj->toArray();
        }
        return $trees;
    }

    /**
     * Return an array containing the hierarchical group listing.
     * Note: UserSet:* items are stripped not returned.
     */
    protected function archiverGroups(){
        foreach (file(storage_path('app/groups.txt')) as $line){
            if ('UserSet' == substr($line,0,7)){
                continue;
            }
            $groups[] = trim($line);
        }
        return $groups;
    }

    /**
     * Returns an array containing the top-level archive groups.
     * These groups will in turn contain collections of their children.
     * @return array
     */
    protected function archiverGroupTrees(){
        $trees = [];
        foreach ($this->archiverGroups() as $group){
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

}
