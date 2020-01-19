<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Lang;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Kharanenka\Helper\Result;
use Itmaker\Banner\Models\Banner as BannerModel;

class BannerList  extends Controller
{
	public function index() {		
		
		$list = BannerModel::get();		

		return $list;
	}
}