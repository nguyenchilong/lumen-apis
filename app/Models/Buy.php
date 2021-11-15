<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buy extends Model
{
    protected $table = 'buy';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'wager_id',
        'buying_price',
        'bought_at',
        'created_at',
        'updated_at'
    ];
}
