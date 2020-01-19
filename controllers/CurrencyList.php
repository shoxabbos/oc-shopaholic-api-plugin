<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\Shopaholic\Classes\Collection\CurrencyCollection;
use Lovata\Shopaholic\Classes\Item\CurrencyItem;
use \Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\Shopaholic\Models\Currency as CurrencyModel;

use Shohabbos\Shopaholicapi\Resources\CurrencyResource;

class CurrencyList extends Controller
{



	public function index() {
		$sort = input('sort');
		
		//
		// filter
		//
		$list = CurrencyCollection::make()->active();


		if ($sort) {
			$list->sort($sort);
		}

 
		//
		// result
		//
		$data = [];
		foreach ($list as $key => $value) {
			$data[] = new CurrencyResource($value);
		}

		return $data;
	}


	public function page($id) {
		$brand = CurrencyModel::find($id);

		return new CurrencyResource($brand);
	}


	// switch currency
	public function switch() {
		$code = Input::get('currency');

        return CurrencyHelper::instance()->switchActive($code);
    }


}
