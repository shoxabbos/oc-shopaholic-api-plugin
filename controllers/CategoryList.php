<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Classes\Collection\CategoryCollection;
use Lovata\Shopaholic\Classes\Item\CategoryItem;
use Lovata\Shopaholic\Models\Category as CategoryModel;

class CategoryList extends Controller
{



	public function index() {
		$tree = input('tree');
		$page = input('page', 1);
		$search = input('search');
		$perpage = input('perpage', 20);

		//
		// filter
		//
		$list = CategoryCollection::make()->active();

		if ($tree) {
			$list->tree();
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
			$data[] = $value->toArray();
		}

		return $data;
	}


	public function page($id) {
		$category = CategoryItem::make($id);

		$result = $category->toArray();
		
		return $result;
	}

	public function children($id) {
		$category = CategoryItem::make($id);

		$result = [];
		
		foreach ($category->children() as $key => $value) {
			$result[] = $value->toArray();
		}

		return $result;
	}


}
