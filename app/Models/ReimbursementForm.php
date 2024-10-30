<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReimbursementForm extends Model
{
    protected $fillable =
    [
        'name',
        'title',
        'price',
        'before',
        'after',
        'documentation'
    ];
}
