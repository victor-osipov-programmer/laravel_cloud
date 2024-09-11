<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;


    function users() {
        return $this->belongsToMany(User::class, 'accesses');
    }

    function author() {
        return $this->belongsTo(User::class);
    }
}
