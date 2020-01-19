<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Lovata\ReviewsShopaholic\Components\MakeReview;

class Review extends Controller
{

	public $makeReview;

	public function __construct() {
		$this->makeReview = new MakeReview();
	}

	public function create() {
		return $this->makeReview->onCreate();
	}

}
