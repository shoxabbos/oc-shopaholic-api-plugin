<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\TagsShopaholic\Classes\Collection\TagCollection;
use Lovata\TagsShopaholic\Classes\Item\TagItem;

class TagList extends Controller
{


	public function index() {
		$sort = input('sort');
		$available = input('available');
		
		//
		// filter
		//
		$list = TagCollection::make()->active();


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

	public function page($id) {
		$brand = TagItem::make($id);

		$result = $brand->toArray();
		
		return $result;
	}

}
