<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends Model
{
    protected $fillable = ['Role'];

    // protected $table = 'roles';
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
