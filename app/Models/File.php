<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class File extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    public $incrementing = false;


    function users()
    {
        return $this->belongsToMany(User::class, 'accesses');
    }

    function author()
    {
        return $this->belongsTo(User::class);
    }

    static function genId()
    {
        return newUniqueId();
    }

    public function newUniqueId()
    {
        return newUniqueId();
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field);
    }
}

function newUniqueId()
{
    do {
        $file_id = Str::random(10);
    } while (File::where('id', $file_id)->exists());

    return $file_id;
}
