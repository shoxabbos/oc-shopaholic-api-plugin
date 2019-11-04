<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use RainLab\User\Models\User;
use Shohabbos\Stores\Models\Store;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Models\Product as ProductModel;

use Shohabbos\Shopaholicapi\Resources\Store\SingleResource as StoreSingleResource;
use Shohabbos\Shopaholicapi\Resources\Store\MultiResource as StoreMultiResource;
use Shohabbos\Shopaholicapi\Resources\Product\SingleResource as ProductSingleResource;
use Shohabbos\Shopaholicapi\Resources\Product\MultiResource as ProductMultiResource;


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

		return StoreMultiResource::collection($list);
	}

	public function page($id) {
		$store = Store::with(
			'logo', 'user', 'banners', 'banners.image',
			'header_image', 'orders'
		)->find($id);
		
		if (!$store){
			return response()->json('not found', 401);
		}

		return new StoreSingleResource($store);
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

		return ProductMultiResource::collection($stores);	
	}




}