<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Models\Product as ProductModel;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\CompareShopaholic\Classes\Helper\CompareHelper;

use Shohabbos\Shopaholicapi\Resources\ProductResource;


class ProductList extends Controller
{

	public function addtocompare() {
		$iProductID = Input::get('product_id');

        /** @var CompareHelper $obCompareHelper */
        $obCompareHelper = app(CompareHelper::class);

        $obCompareHelper->add($iProductID);
        $arProductIDList = $obCompareHelper->getList();

        return $arProductIDList;
	}

	public function index() {
		$sort = input('sort');
		$category = input('category');
		$brand = input('brand');
		$tag = input('tag');
		$viewed = input('viewed');
		$label = input('label');
		$wishlist = input('wishlist');
		$compare = input('compare');
		$categories = input('categoryies', []);
		$page = input('page', 1);
		$perpage = input('perpage', 20);
		$search = input('search');

		//
		// filter
		//
		$list = ProductCollection::make()->active();


		// values: 'no', 'price|asc', 'price|desc', 'new', 'popularity|desc', 'rating|desc', 'rating|asc'
		if ($sort) {
			$list->sort($sort);
		}

		if ($category) {
			$list->category($category);
		}

		if ($brand) {
			$list->brand($brand);
		}

		if ($tag) {
			$list->tag($tag);
		}

		if ($label) {
			$list->label($label);
		}

		if ($viewed) {
			$list->viewed();
		}

		if ($wishlist) {
			$list->wishList();
		}

		if ($compare) {
			$list->compare();
		}

		if ($categories) {
			$list->category($categories);
		}

		if (is_array($categories) && $categories) {
			$list->category($categories);
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
            // $data[] = $value->toArray();
            $data[] = new ProductResource($value);
        }

        return [
        	'data' => $data
        ];
	}


	public function page($id) {
		$product = ProductItem::make($id);

		$result = $product->toArray();

		foreach ($product->offer as $key => $value) {
			$result['offers'][] = $value->toArray();
		}
		
		return $result;
	}


}
