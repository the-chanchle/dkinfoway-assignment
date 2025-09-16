<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockEntry extends Model
{
    protected $fillable = [
        'stock_no',
        'item_code',
        'item_name',
        'quantity',
        'location',
        'store_id',
        'in_stock_date',
        'status'
    ];

    protected $dates = ['in_stock_date'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if (!$model->stock_no) {
                DB::table('stock_entries')->where('id', $model->id)
                    ->update(['stock_no' => 100000 + $model->id]);
                $model->stock_no = 100000 + $model->id;
            }
        });
    }
}