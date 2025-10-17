<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Staff
 * Maps data from the public staff view available in Oracle.
 * @package App\Model
 */
class Staff extends Model
{
    use HasFactory;

    protected $primaryKey = 'staff_id';
    public $timestamps = false;

}
