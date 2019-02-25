<?php

namespace App\Http\Controllers\StatusServer;

use App\Http\Controllers\Controller;
use App\Models\StatusServer;
use Illuminate\Http\Request;
/**
 * Class CompaniesController
 * @package App\Http\Controllers\Backend
 */
class StatusServerController extends Controller
{



    public function index(Request $request)
    {

   		$status_ = StatusServer::where('name','like',env("APP_ENV"))->whereDate('created_at', \DB::raw('CURDATE()'))->orderBy('created_at')->get();
        if($request->has('date'))
        	$status_= StatusServer::where('name','like',env("APP_ENV"))->whereDate('created_at',$request->get('date') )->orderBy('created_at')->get();
		$last_slow = 0;
        if(isset($status_[0]))
        	$last_slow = $status_[0]->fpm_slow_requests;
        return view('statusServer.index',compact('status_',"last_slow"));
    }
}