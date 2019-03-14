<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Counter;
use DB;

class InvoiceController extends Controller
{
    public function index()
    {
    	$invoices = Invoice::with(['customer'])
    		->orderBy('created_at', 'DESC')
    		->paginate(15);

    	return [compact('invoices')];	
    }

    public function create()
    {
    	$counter = Counter::where('key', 'invoice')->first();

    	$form = [
    		'number' => $counter->prefix . $counter->value,
    		'customer_id' => null,
    		'customer'	=> null,
    		'date' => date('Y-m-d'),
    		'due_date' => null,
    		'reference' => null,
    		'discount' => 0,
    		'terms_and_conditions' => 'Default Terms',
    		'items' => [
    			[				
	    			'product_id' => null,
	    		    'product' => null,
	    		    'unit_price' => 0,
	    		    'qty' => 1
    		    ]
    		]
    	];

    	return [compact('form')];
    }

    public function store(Request $request)
    {
    	$this->validate([
    		'customer_id' 	=> 'required|integer|exists:customers, id',
    		'date' 			=> 'required|date_format:Y-m-d',
    		'due_date' 		=> 'required|date_format:Y-m-d',
    		'reference'		=> 'nullable|max:100',
    		'discount' 		=> 'required|numeric|min:0',
    		'terms_and_conditions'	=> 'required|max:2000',
    		'items'			=> 'required|array|min:1',
    		'items.*.product_id' => 'required|integer|exists:products, id',
    		'items.*.unit_price' => 'required|numeric|min:0',
    		'items.*.qty'		 => 'required|integer|min:1'					
    	]);

    	$invoice = new Invoice;
    	$invoice->fill($request->except('items'));


    	$invoice->sub_total = collect($request->items)->sum(function($item){
    		return $item['qty'] * $item['unit_price'];
    	});

    	$invoice = DB::transaction(function() use($invoice, $request){
    		$counter = Counter::where('key', 'invoice')->first();

    		$invoice->number = $counter->prefix . $counter->value;
	
	    	
	    	//custom method from app/Helper/HasManyRelation
	    	$invoice->storeHasMany([
	    		'items' => $request->items
	    	]);

	    	$counter->increment('value');

	    	return $invoice;

    	});

    	return response()
    		->json(['saved' => true, 'id' => $invoice->id]);
    }

    public function show($id)
    {
    	$invoice = Invoice::with(['customer', 'items.product'])
    		->findOrFail($id);

    	return [compact('invoice')];	
    }

    public function edit($id)
    {
    	$invoice = Invoice::with(['customer', 'items.product'])
    		->findOrFail($id);

    	return [compact('invoice')];	
    }

    public function update(Request $request, $id)
    {
    	$invoice = Invoice::findOrFail($id);

    	$this->validate([
    		'customer_id' 	=> 'required|integer|exists:customers, id',
    		'date' 			=> 'required|date_format:Y-m-d',
    		'due_date' 		=> 'required|date_format:Y-m-d',
    		'reference'		=> 'nullable|max:100',
    		'discount' 		=> 'required|numeric|min:0',
    		'terms_and_conditions'	=> 'required|max:2000',
    		'items'			=> 'required|array|min:1',
    		'items.*.id'	=> 'sometimes|required|integer|exists:invoce_items,id,invoice_id'.$invoice->id,
    		'items.*.product_id' => 'required|integer|exists:products, id',
    		'items.*.unit_price' => 'required|numeric|min:0',
    		'items.*.qty'		 => 'required|integer|min:1'					
    	]);

    	$invoice->fill($request->except('items'));

    	$invoice->sub_total = collect($request->items)->sum(function($item){
    		return $item['qty'] * $item['unit_price'];
    	});

    	$invoice = DB::transaction(function() use($invoice, $request){
	    	
	    	//custom method from app/Helper/HasManyRelation
	    	$invoice->updateHasMany([
	    		'items' => $request->items
	    	]);

	    	return $invoice;

    	});

    	return response()
    		->json(['saved' => true, 'id' => $invoice->id]);
    }

    public function destroy($id)
    {
    	$invoice = Invoice::findOrFail($id);

    	$invoice->items()->delete();
    	$invoice->delete();

    	// return responce()
    	// 	->json(['deleted' => true]);	
    	return [compact('invoice')];
    }


}
