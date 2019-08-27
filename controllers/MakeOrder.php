<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\OrdersShopaholic\Components\MakeOrder as MakeOrderComponent;

class MakeOrder extends Controller
{

	public $component;

	public function __construct() {
		$this->component = new MakeOrderComponent();
	}


	public function create() {
		return $this->component->onCreate();
	}

}
