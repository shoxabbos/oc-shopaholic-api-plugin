<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Models\Product as ProductModel;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Item\CategoryItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\CompareShopaholic\Classes\Helper\CompareHelper;

use Shohabbos\Shopaholicapi\Resources\Product\SingleResource;
use Shohabbos\Shopaholicapi\Resources\Product\MultiResource;
use Shohabbos\Shopaholicapi\Resources\ReviewResource;


class ProductList extends Controller
{

	public function filterByid() {
		$id = input('id');
		$idlist = input('idlist');

		if (!empty($idlist)) {
			$idlistArray = explode(",", $idlist);
		}

		if (empty($idlistArray)) {
			return [
				'data' => []
			];
		}

		$result = ProductModel::with('offer', 'preview_image', 'images')
					->whereIn('id', $idlistArray)
					->get();

		return MultiResource::collection($result);
	}

	public function index() {
		$sort = input('sort');
		$store = input('store');
		$category = input('category');
		$brand = input('brand');
		$tag = input('tag');
		$viewed = input('viewed');
		$label = input('label');
		$page = input('page', 1);
		$perpage = input('perpage', 20);
		$search = input('search');
		$filters = input('filters', []);


		//
		// filter
		//
		$categoryModel = $category ? CategoryItem::make($category) : null;


		$list = ProductCollection::make()->active();


		// filter by properties
		if ($categoryModel && $filters && !empty($filters)) {
			$list->filterByProperty($filters, $categoryModel->offer_filter_property);
		}

		// values: 'no', 'price|asc', 'price|desc', 'new', 'popularity|desc', 'rating|desc', 'rating|asc'
		if ($sort) {
			$list->sort($sort);
		}

		if ($brand) {
			$list->brand($brand);
		}

		if ($tag) {
			$list->tag($tag);
		}

		if ($store) {
			$list->store($store);
		}

		if ($label) {
			$list->label($label);
		}

		if ($viewed) {
			$list->viewed();
		}

		if ($category) {
			$list->category($category, true);
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
            $data[] = new MultiResource($value);
        }

        return [
        	'data' => $data
        ];
	}


	public function page($id) {
		$product = ProductModel::with('related', 'accessory', 'review')->find($id);

		if (!$product) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		$data = new SingleResource($product);

		return $data;
	}


	public function reviews($id) {
		$product = ProductModel::find($id);

		if (!$product) {
			return response()->json(['message' => 'Not Found!'], 404);
		}

		$reviews = $product->review()->paginate(20, 1);

		return ReviewResource::collection($reviews);
	}


}
