<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;

class ProductController extends Controller
{
    public function search(Request $request)
    {
    	
    	$search = $request->search;

    	$result = Product::where('item_code', 'like', '%' . $search . '%')
    		->orWhere('description', 'like', '%' . $search . '%')
    		->orderBy('item_code', 'DESC')
    		->limit(10)
    		->get();

    		return compact('result');

    }
}
