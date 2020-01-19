<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\LabelsShopaholic\Classes\Collection\LabelCollection;

use Shohabbos\Shopaholicapi\Resource\LabelResource;

class LabelList extends Controller
{



	public function index() {
		$sort = input('sort');
		$available = input('available');
		
		//
		// filter
		//
		$list = LabelCollection::make()->active();


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
			$data[] = new LabelResource($value);
		}

		return $data;
	}



}
