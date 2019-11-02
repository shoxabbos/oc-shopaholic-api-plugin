<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use RainLab\User\Models\User;
use Shohabbos\Stores\Models\Store;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Models\Product as ProductModel;

use Shohabbos\Shopaholicapi\Resources\StoreResource;
use Shohabbos\Shopaholicapi\Resources\ProductResource;


class StoreList  extends Controller
{

	public function index() {
		$page = input('page', 1);
		$perpage = input('perpage', 20);
		$searchQuery = input('query');
 		
		$query = Store::with('logo');

		if ($searchQuery && strlen($searchQuery) > 1) {
			$query->where('name', 'like', "%{$searchQuery}%");
		}

		$list =	$query->paginate($perpage, $page);		

		return StoreResource::collection($list);
	}

	public function page($id) {
		$store = Store::with(
			'logo', 'user', 'banners', 'banners.image',
			'header_image', 'orders'
		)->find($id);
		
		if (!$store){
			return response()->json('not found', 401);
		}

		return new StoreResource($store);
	}

	public function storeProducts($id) {
		$page = input('page', 1);
		$perpage = input('perpage', 20);
		$searchQuery = input('query');


		$query = ProductModel::with(['preview_image', 'images'])
			->where('store_id', $id);

		if ($searchQuery && strlen($searchQuery) > 1) {
			$query->where('name', 'like', "%{$searchQuery}%");
		}

		$stores = $query->paginate($perpage, $page);

		return ProductResource::collection($stores);	
	}




}