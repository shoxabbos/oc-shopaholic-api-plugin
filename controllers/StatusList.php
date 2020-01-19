<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\OrdersShopaholic\Classes\Collection\StatusCollection;

class StatusList extends Controller
{



	public function index() {  
			
		$list = StatusCollection::make()->active();

		$data = [];
		foreach ($list as $key => $value) {
			$data[] = $value->toArray();
		}

		return $data;
	}



}
