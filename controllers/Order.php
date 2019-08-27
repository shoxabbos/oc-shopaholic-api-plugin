<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\OrdersShopaholic\Components\MakeOrder as MakeOrderComponent;
use Lovata\OrdersShopaholic\Components\OrderPage as OrderPageComponent;

class Order extends Controller
{

	public $makeOrder;
	public $orderPage;

	public function __construct() {
		$this->makeOrder = new MakeOrderComponent();
		$this->orderPage = new OrderPageComponent();
	}


	public function create() {
		return $this->makeOrder->onCreate();
	}

	public function get() {
		return $this->orderPage->get();
	}

}
