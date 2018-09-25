<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableRequest;

class CouponCodesController extends Controller
{
    public function show($code){
    	if(!$record = CouponCode::where('code',$code)->first()){
    		throw new CouponCodeUnavailableRequest('优惠券不存在');
    	}

    	$record->checkAvailable();

    	return $record;
    }
}
