<?php

namespace App\Http\Controllers;

use App\Mail\ChannelRequest;
use App\Model\ArchiveRequest;
use App\Model\ArchiverGroup;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $errors;

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    /**
     * Display the form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('main')
            ->with('groupTrees', $this->groupTreesToOptions());
    }

    /**
     * Conver the groupTrees data into a format suitable to use as
     * the options array in the client-side vue-treeselect widget.
     *
     * @return array
     */
    protected function groupTreesToOptions()
    {
        $trees = [];
        foreach ($this->archiverGroupTrees() as $obj) {
            $trees[] = $obj->toArray();
        }
        return $trees;
    }

    /**
     * Returns an array containing the top-level archive groups.
     * These groups will in turn contain collections of their children.
     * @return array
     */
    protected function archiverGroupTrees()
    {
        $trees = [];
        foreach (ArchiveRequest::groups() as $group) {
            $parts = explode(':', $group, 2);
            $key = array_shift($parts);
            if (!array_key_exists($key, $trees)) {
                $trees[$key] = new ArchiverGroup($key);
            } else {
                if (!empty($parts)) {
                    $trees[$key]->addFromString($parts[0]);  //We already shifted original first elem into $key
                }
            }
        }
        return $trees;
    }

    /**
     * Submlit the form
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function submit(Request $request)
    {
        try {
            $archiveRequest = ArchiveRequest::make($request->all());
            if ($archiveRequest->validate()) {
                Mail::send(new ChannelRequest($archiveRequest));
                return response()->json('success');
            }
            return response()->json($archiveRequest->errors, 400);
        } catch (\Exception $e) {
            dd($e);
            return response()->json($e->getMessage(), 500);
        }

    }

}
