<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;


class ArchiveRequest extends Model
{
    /**
     * Any errors accumulated by validate()
     * @var MessageBag
     */
    public $errors;

    /**
     * The list of properties which may not be mass-assigned.
     *
     * Empty array implies all properties may be mass-assigned.
     *
     * @var array
     */
    public $guarded = [];

    /**
     * The channels to which the request applies.
     *
     * @var Collection
     */
    public $channelCollection;



    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->errors = new MessageBag();
        $this->makeChannelCollection();

    }

    /**
     * Factory method to create specific types of Archiver Requests.
     *
     * @param array $attributes
     * @return ArchiveRequest|MetadataRequest
     * @throws \Exception
     */
    public static function make(array $attributes = [])
    {
        if (isset($attributes['requestType'])){
            switch ($attributes['requestType']) {
                case 'change-metadata' : return new MetadataRequest($attributes);
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
	foreach(self::readGroups() as $line){	        
	//foreach (file(storage_path('app/groups.txt')) as $line) {
            if ('UserSet' == substr($line, 0, 7)) {
                continue;
            }
            $groups[] = trim($line);
        }
        return $groups;
    }

    protected static function readGroups(){
	return explode("\n", shell_exec(env('ARCHIVE_CMD')));
    }

    protected function makeChannelCollection(){
        switch ($this->selectMethod){
            case 'form' : return $this->collectChannelsFromForm();
            case 'bulk' : return $this->collectChannelsFromBulk();
            case 'file' : return $this->collectChannelsFromFile();
        }

    }

    /**
     * Builds the channels collection when input was array of {channel, deadband} pairs.
     *
     * Any items with an empty value for channel will be excluded from the
     * collection.
     */
    protected function collectChannelsFromForm(){
        $this->channelCollection = collect($this->channels)
            ->filter(function ($value, $key) {
                return ! empty($value['channel']);
        });
    }

    /**
     * Builds the channels collection when input was text blob
     *   channel   (optional) deadband
     *   channel   {optional) deadband
     *
     * Any blank lines are skipped.
     * Lines that don't match the pattern above generate errors.
     */
    protected function collectChannelsFromBulk(){
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

    /**
     * Makes the channel collection include a single element that simply says
     * "see attached file"
     */
    protected function collectChannelsFromFile(){
        $this->channelCollection = new Collection();
        $this->channelCollection->push([
            'channel' => 'See attached file',
            'deadband'  => '' ,
        ]);
    }


    /**
     * Return a full staff object based on the user info provided to construct this object.
     *
     * @return Staff | null
     */
    public function staff()
    {
        if ($this->user && isset($this->user['username'])) {
            return Staff::where('username', $this->user['username'])->first(); // first b/c usernames are unique;
        }
        return null;
    }

    /**
     * Is there staff data?
     *
     * @return bool
     */
    public function isStaff(){
        return $this->staff() != null;
    }

    public function email(){
        return $this->staff() ? $this->staff()->email : '' ;
    }


    /**
     * @return Collection
     */
    public function channels(){
        return $this->channelCollection;
    }

    /**
     * Validate the constructed object.
     * @return bool
     */
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
        $this->validateUser();
    }

    function validateMetadata(){
        $this->validateGroup();
    }

    protected function validateUser()
    {
        if (!$this->user && $this->user['username']) {
            $this->errors->add('username', 'A valid username is required');
            return false;
        }
        if (!$this->isStaff()) {
            $this->errors->add('username', 'Username not found');
        }
        if ($this->isStaff() && !$this->email()) {
            $this->errors->add('username', 'No email address for user');
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
        $this->validateHasFile();
    }

    protected function validateHasFile(){
        $validator = Validator::make(['file' => $this->file], [
            'file' => 'required|file',
        ]);
        if ($validator->fails()) {
            $this->errors->add('channels','File upload containing channels is missing');
        }
        if (filesize($this->file->path()) < 1){     // 1 byte file is still probably useless
            $this->errors->add('channels','File uploaded appears to be empty');
        }
    }

    public function hasFile(){
        return ! empty($this->file);
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
