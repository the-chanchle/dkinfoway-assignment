<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock_entries';

    protected $fillable = [
        'stock_no',
        'item_code',
        'item_name',
        'quantity',
        'location',
        'store_id',
        'in_stock_date',
        'status',
    ];

    protected $dates = ['in_stock_date'];

    public function store()
    {
        return $this->belongsTo(\App\Store::class, 'store_id');
    }
}