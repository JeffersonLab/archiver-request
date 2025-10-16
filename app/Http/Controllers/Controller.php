<?php

namespace App\Http\Controllers;

use App\Http\Resources\StaffResource;
use app\Mail\ChannelRequest;
use App\Models\ArchiveRequest;
use App\Models\ArchiverGroup;
use App\Models\Staff;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $errors;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->errors = new MessageBag();
        $this->request = $request;
    }

    /**
     * Display the form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        try{
            return view('main')
                ->with('groupTrees', $this->groupTreesToOptions());
        }catch (\Exception $e){
            Log::error($e);
            abort(500, $e->getMessage());
        }
    }

    public function staff(Request $request){
        try{
            $query = $request->get('q',null);
            if ($query && strlen($query) > 2) {
                $query = strtolower($query . '%');
                $users = $this->getStaffLikeQuery($query);
                return $this->collectionResponse($users);
            }else{
                throw new \Exception('Minimum of 3 characters required for user search.');
            }
            } catch (\Throwable $e) {
                Log::error($e);
                return response()->json($e->getMessage(), 422, []);
            }
    }

    protected function getStaffLikeQuery($query){
        return Staff::whereNotNull('username')
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', $query)
                    ->orWhere(DB::raw('lower(firstname)'), 'like', $query)
                    ->orWhere(DB::raw('lower(lastname)'), 'like', $query);
            })->orderBy('lastname')->orderBy('firstname')->get();
    }

    protected function collectionResponse(Collection $users){
        $resource = StaffResource::collection($users);
        $response = $resource->toResponse($this->request);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
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
            $archiveRequest = ArchiveRequest::make(json_decode($request->get('form'),true));
            if ($request->hasFile('file')){
                $archiveRequest->file = $request->file('file');
            }
            if ($archiveRequest->validate()) {
                Mail::send(new ChannelRequest($archiveRequest));
                return response()->json('success');
            }
            return response()->json($archiveRequest->errors, 400);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

}
