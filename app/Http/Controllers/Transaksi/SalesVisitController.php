<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables, Auth, DB;
use Validator,Redirect,Response;

class SalesVisitController extends Controller
{
    public function index()
    {
        return view('transaksi.salesvisit.index');
    }
}
