<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableRequest;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function show($code,Request $request){
    	if(!$record = CouponCode::where('code',$code)->first()){
    		throw new CouponCodeUnavailableRequest('优惠券不存在');
    	}

    	$record->checkAvailable($request->user());

    	return $record;
    }
}
