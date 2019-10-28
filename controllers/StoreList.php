<?php namespace Shohabbos\Shopaholicapi\Controllers;

use Input;
use Lang;
use JWTAuth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

// custom classess
use Kharanenka\Helper\Result;
use RainLab\User\Models\User;
use Shohabbos\Stores\Models\Store;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Models\Product as ProductModel;

class StoreList  extends Controller
{
	public function index() {		
		
		$list = Store::get();		

		return $list;
	}

	public function page($id) {
		$store = Store::find($id);		
		
		if (!$store){
			return response()->json('not found', 401);
		}

		return $store;
	}

	public function mystore() {
		$user = $this->auth();		
		$store = Store::where('id', $user->store->id)->first();		
		
		return $store;
	}

	public function update(Request $request) {
		$user = $this->auth();

		if (!$user->store) 
        {
            return response()->json(['error' => 'store not found'], 401);
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'string',
			'legal_name' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $store = Store::where('id', $user->store->id)->first();
		$store->legal_name = $request['legal_name'];
		$store->name = $request['name'];
		$store->address = $request['address'];
		$store->contacts = $request['contacts'];
		$store->email = $request['email'];
		if (empty($store->slug)){
			$store->slug = $user->name;	
		}
		$store->header_image = Input::file('header_image');		
		$store->logo = Input::file('logo');		
		$store->save();

        return $store;		
	}

	public function products() {
		$user = $this->auth();
		$id = $user->store->id;

		$products = ProductModel::where('store_id', $id)->get();			

		return $products;
	}

	// private methods
    
    private function auth() {
		return JWTAuth::parseToken()->authenticate();
	}
}