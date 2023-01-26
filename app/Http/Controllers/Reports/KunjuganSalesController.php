<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables, Auth, DB;
use Validator,Redirect,Response;

class KunjuganSalesController extends Controller
{
    public function index(){
        return view('laporan.salesvisit');
    }

    public function datakunjuganan(Request $req){
        // $data = DB::table('v_total_waktu_kunjungan')->get();
        $query = DB::table('v_total_waktu_kunjungan');

        if(isset($req->datefrom) && isset($req->dateto)){
            $query->whereBetween('date', [$req->datefrom, $req->dateto]);
        }elseif(isset($req->datefrom)){
            $query->where('date', $req->datefrom);
        }elseif(isset($req->dateto)){
            $query->where('date', $req->dateto);
        }

        $query->orderBy('id');

        return DataTables::queryBuilder($query)
        // ->editColumn('amount', function ($query){
        //     return [
        //         'amount1' => number_format($query->amount,0)
        //      ];
        // })->editColumn('approved_amount', function ($query){
        //     return [
        //         'amount2' => number_format($query->approved_amount,0)
        //      ];
        // })
        ->toJson();
    }
}
