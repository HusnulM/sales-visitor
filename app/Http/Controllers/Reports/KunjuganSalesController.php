<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables, Auth, DB;
use Validator,Redirect,Response;

class KunjuganSalesController extends Controller
{
    public function index(){

    }

    public function datakunjuganan(){
        $data = DB::table('users');
    }
}
