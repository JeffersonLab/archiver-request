<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

class ArchiveRequest extends Model
{

    public $errors;

    public $guarded = [];

    /**
     * @var Collection
     */
    public $channelCollection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->errors = new MessageBag();
        $this->makeChannelCollection();

    }

    public static function make(array $attributes = [])
    {
        if (isset($attributes['requestType'])){
            switch ($attributes['requestType']) {
                case 'add-channels' : return new ArchiveRequest($attributes);
                default             : return new ArchiveRequest($attributes);
            }
        }
        throw new \Exception('Invalid Request Type');
    }

    /**
     * Return an array containing the hierarchical group listing.
     * Note: UserSet:* items are stripped not returned.
     * @TODO call archive command
     * @TODO cache command output for remainder of request
     */
    public static function groups()
    {
        foreach (file(storage_path('app/groups.txt')) as $line) {
            if ('UserSet' == substr($line, 0, 7)) {
                continue;
            }
            $groups[] = trim($line);
        }
        return $groups;
    }

    protected function makeChannelCollection(){
        switch ($this->selectMethod){
            case 'form' : return $this->collectChannelsFromForm();
            case 'bulk' : return $this->collectChannelsFromBulk();
            case 'file' : return $this->collectChannelsFromFile();
        }

    }

    protected function collectChannelsFromForm(){
        //dd($this->channels);
        $this->channelCollection = collect($this->channels)
            ->filter(function ($value, $key) {
                return ! empty($value['channel']);
        });
    }

    protected function collectChannelsFromBulk(){
        //dd($this->channels);
        $this->channelCollection = new Collection();
        $lines = preg_split('/\n|\r\n?/', $this->bulk);
        $n = 0;
        foreach ($lines as $line){
            $n++;
            //ignore blank lines
            if (preg_match('/^([\s\t])*$/', $line, $m)){
                continue;
            }
            //Then verify either a channel or channel-whitespace-deadband pattern
            if (preg_match('/^(\S+)([\s\t]+(\S+))?$/', $line, $m)){
                $this->channelCollection->push([
                    'channel' => $m[1],
                    'deadband'  => isset($m[3]) ? $m[3] : '' ,
                ]);
            }else{
                $this->errors->add('channels',
                    "Bad format at line $n of bulk channel specification");
            }
        }

    }

    // TODO use database lookup.
    public function email(){
        return $this->username .'@jlab.org';
    }

    public function channels(){
        return $this->channelCollection;
    }

    public function validate()
    {
        $this->validateCommonFields();
        $this->validateChannels();

        if ($this->requestType != 'change-deadbands'){
            $this->validateMetadata();
        }

        return $this->errors->isEmpty();
    }

    protected function validateCommonFields()
    {
        $this->validateDeployment();
        $this->validateUsername();
    }

    function validateMetadata(){
        $this->validateGroup();
    }

    protected function validateUsername()
    {
        //TODO database lookup of username
        if (!$this->username) {
            $this->errors->add('username', 'A valid username is required');
        }
    }

    protected function validateChannels(){
        switch ($this->selectMethod){
            case 'form' : return $this->validateFormChannels();
            case 'bulk' : return $this->validateBulkChannels();
            case 'file' : return $this->validateFileChannels();
        }
    }

    protected function validateHasChannels(){
        if ($this->channelCollection->isEmpty()){
            $this->errors->add('channels', 'At least one channel name must be specified');
        }
    }
    protected function validateFormChannels(){
        $this->validateHasChannels();
    }

    protected function validateBulkChannels(){
        $this->validateHasChannels();
    }

    protected function validateFileChannels(){

    }

    protected function validateGroup()
    {
        if (!$this->group) {
            $this->errors->add('group', 'An archiver group must be specified');
        } else {
            $this->validateExistingGroup();
        }
    }

    protected function validateDeployment()
    {
        if (!in_array($this->deployment, config('mya.deployments'))) {
            $this->errors->add('deployment', 'Invalid deployment');
        }
    }

    protected function validateExistingGroup()
    {
        if (!$this->newGroup) {
            if (!in_array($this->group, self::groups())) {
                $this->errors->add('group', 'Not a valid existing group name');
            }
        }
    }
}
