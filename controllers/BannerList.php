<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Lang;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Kharanenka\Helper\Result;
use Itmaker\Banner\Models\Banner as BannerModel;
use Shohabbos\Shopaholicapi\Resources\BannerResource;

class BannerList  extends Controller
{
	
	public function index($type = 'main') {
		if ($type == 'main') {
			$list = BannerModel::where('size_id', 26)->get();
		} else {
			$list = BannerModel::get();
		}

		return BannerResource::collection($list);
	}

}