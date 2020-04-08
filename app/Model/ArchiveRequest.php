<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ArchiveRequest extends Model
{

    public $errors;

    public $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->errors = new MessageBag();

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

    // TODO use database lookup.
    public function email(){
        return $this->username .'@jlab.org';
    }

    public function channels(){
        return $this->channels;
    }

    public function validate()
    {
        $this->errors = new MessageBag();
        $this->validateCommonFields();
        $this->validateGroup();
        return $this->errors->isEmpty();
    }

    protected function validateCommonFields()
    {
        $this->validateDeployment();
        $this->validateUsername();
    }

    protected function validateUsername()
    {
        //TODO database lookup of username
        if (!$this->username) {
            $this->errors->add('username', 'A valid username is required');
        }
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
