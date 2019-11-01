<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Classes\Collection\BrandCollection;
use Lovata\Shopaholic\Classes\Item\BrandItem;
use Lovata\Shopaholic\Models\Brand as BrandModel;
use Shohabbos\Shopaholicapi\Resources\BrandResource;

class BrandList extends Controller
{



	public function index() {
		$sort = input('sort');
		$page = input('page', 1);
		$search = input('search');
		$perpage = input('perpage', 20);
		$category = input('category');

		//
		// filter
		//
		$list = BrandCollection::make()->active();


		if ($sort) {
			$list->sort($sort);
		}

		if ($category) {
			$list->category($category);
		}

		if ($search) {
			$list->search($search);
		}

		$list = $list->page($page, $perpage);


		//
		// result
		//
		$data = [];
		foreach ($list as $key => $value) {
			$data[] = new BrandResource($value);
		}

		return [
			'data' => $data
		];
	}

	public function page($id) {
		$brand = BrandModel::find($id);

		if (!$brand) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		return new BrandResource($brand);
	}


}
