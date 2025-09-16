<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'code'];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }
}