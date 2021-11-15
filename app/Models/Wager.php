<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wager extends Model
{
    use HasFactory;

    protected $table = 'wagers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'total_wager_value',
        'odds',
        'selling_percentage',
        'selling_price',
        'current_selling_price',
        'percentage_sold',
        'amount_sold',
        'placed_at',
        'created_at',
        'updated_at'
    ];
}
