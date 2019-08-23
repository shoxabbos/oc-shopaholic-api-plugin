<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Classes\Collection\CurrencyCollection;
use Lovata\Shopaholic\Classes\Item\CurrencyItem;
use Lovata\Shopaholic\Models\Currency as CurrencyModel;

class CurrencyList extends Controller
{



	public function index() {
		$sort = input('sort');
		
		//
		// filter
		//
		$list = CurrencyCollection::make()->active();


		if ($sort) {
			$list->sort($sort);
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


	public function page($id) {
		$brand = CurrencyItem::make($id);

		$result = $brand->toArray();
		
		return $result;
	}


}
