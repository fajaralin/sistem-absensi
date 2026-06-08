<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Properti yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];
}
