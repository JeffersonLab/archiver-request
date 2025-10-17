<?php


namespace App\Models;


use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\View\Compilers\Concerns\CompilesJson;

class ArchiverGroup implements Jsonable
{
    public $path;

    public $name;

    /**
     * @var Collection
     */
    public $children;

    public function __construct($name, $path =''){
        $this->name = $name;
        $this->path = $path;
        $this->children = new Collection();
    }

    /**
     * Populates children with descendant(s) parsed from string
     * in the format of group:subgroup:subgroup.
     *
     * @param $string
     */
    public function addFromString($string){
        $parts = explode(':', $string, 2);
        $key = array_shift($parts);
        if (! $this->children->contains('name',$key)){
            $this->children->push(new ArchiverGroup($key, $this->makePath()));
        }
        if (! empty($parts)){
            $parent = $this->children->firstWhere('name', $key);
            $parent->addFromString(array_shift($parts));
        }

    }

    public function makePath(){
        if ($this->path){
           return $this->path.':'.$this->name;
        }
        return $this->name;
    }

    public function toArray(){
        return [
            'text' => $this->name,
            'path' => $this->makePath(),
            'children' => $this->children->map(function ($item, $key) {
                    return $item->toArray();
                })->toArray()
        ];
    }

    public function toJson($options = null){
        return json_encode($this->toArray(), $options);
    }

}
