<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;

// custom classess
use Lovata\Shopaholic\Models\Category as CategoryModel;
use Shohabbos\Shopaholicapi\Resources\CategoryResource;


class CategoryList extends Controller
{

	public function index() {
		$search = input('search');

		$query = new CategoryModel;
		$data = $query->getAllRoot();

		return CategoryResource::collection($data);
	}


	public function page($id) {
		$model = CategoryModel::find($id);

		if (!$model) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		return new CategoryResource($model);
	}

 
	public function children($id) {
		$model = CategoryModel::find($id);

		if (!$model) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		return CategoryResource::collection($model->getChildren());
	}


}
