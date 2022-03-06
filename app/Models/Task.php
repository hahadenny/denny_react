<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table        = 'Task';
    protected $primaryKey   = 'Id';
    protected $keyType      = 'integer';
    public $incrementing    = true;
    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'DateAdded',
		'DateUpdated',
		'Creator',
		'Assignee',
		'Description',
		'Priority',
		'DueDate',
		'Status'
    ];
}
