<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;

// custom classess
use Lovata\Shopaholic\Classes\Item\CategoryItem;
use Lovata\Shopaholic\Models\Category as CategoryModel;

use Shohabbos\Shopaholicapi\Resources\Category\MultiResource;
use Shohabbos\Shopaholicapi\Resources\Category\SingleResource;


class CategoryList extends Controller
{

	public function index() {
		$search = input('search');

		$query = new CategoryModel;
		$data = $query->getAllRoot();

		return MultiResource::collection($data);
	}


	public function page($id) {
		$model = CategoryItem::make($id);

		if (!$model) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		return new SingleResource($model);
	}

 
	public function children($id) {
		$model = CategoryModel::find($id);

		if (!$model) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		return MultiResource::collection($model->getChildren());
	}



}
