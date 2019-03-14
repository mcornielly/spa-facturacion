<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
    	
    	$search = $request->search;

    	$result = Customer::where('firstname', 'like', '%' . $search . '%')
    		->orWhere('lastname', 'like', '%' . $search . '%')
    		->orWhere('email', 'like', '%' . $search . '%')
    		->orderBy('firstname', 'DESC')
    		->limit(10)
    		->get();

    		return [compact('result')];

    }
}
