<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;


class InvoiceItem extends Model
{
	use HasManyRelation;

    protected $fillable = [
    	'product_id', 'unit_price', 'qty'
    ];

    public function product()
    {
    	return $this->belongsTo(Product::class);
    }
}	
