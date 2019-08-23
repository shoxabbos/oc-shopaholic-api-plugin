<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\OrdersShopaholic\Classes\Collection\PaymentMethodCollection;

class PaymentMethodList extends Controller
{



	public function index() {
		$sort = input('sort');
		$available = input('available');
		
		//
		// filter
		//
		$list = PaymentMethodCollection::make()->active();


		if ($sort) {
			$list->sort($sort);
		}

		if ($available) {
			$list->available($available);
		}

		//
		// result
		//
		$data = [];
		foreach ($list as $key => $value) {
			$data[] = $value->toArray();
		}

		return $data;
	}



}
