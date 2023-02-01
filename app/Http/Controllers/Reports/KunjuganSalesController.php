<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DataTables, Auth, DB;
use Validator,Redirect,Response;
use App\Exports\DetailKunjunganSalesExport;
use App\Exports\KunjunganSalesExport;

class KunjuganSalesController extends Controller
{
    public function index(){
        $sales = DB::table('v_salesman')->get();
        return view('laporan.salesvisit', ['sales' => $sales]);
    }

    public function detailkunjungan(){
        $sales = DB::table('v_salesman')->get();
        return view('laporan.salesvisitdetail', ['sales' => $sales]);
    }

    public function findSalesman(Request $req){
        
        $query['data'] = DB::table('v_salesman')->where('name', 'like', '%'. $req->search . '%')->get();

        // return \Response::json($query);
        return $query;
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

        if(isset($req->sales)){
            $query->where('userid', $req->sales);
        }

        if(getJabatanCode() == "SLS"){
            $query->where('userid', Auth::user()->id);
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

    public function pemesananByKunjungan(Request $req){
        $query = DB::table('v_detail_data_kunjungan')->distinct()
                ->select('id','nomorvisit','tgl_visit','qrtoko','nama_outlet','salesman');

        if(isset($req->datefrom) && isset($req->dateto)){
            $query->whereBetween('tgl_visit', [$req->datefrom, $req->dateto]);
        }elseif(isset($req->datefrom)){
            $query->where('tgl_visit', $req->datefrom);
        }elseif(isset($req->dateto)){
            $query->where('tgl_visit', $req->dateto);
        }

        if(getJabatanCode() == "SLS"){
            $query->where('createdby', Auth::user()->email);
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

    public function detailDataKunjungan(Request $req){
        $query = DB::table('v_detail_data_kunjungan')->where('id', $req->docid)->get();
        return $query;
        // if(isset($req->datefrom) && isset($req->dateto)){
        //     $query->whereBetween('tgl_visit', [$req->datefrom, $req->dateto]);
        // }elseif(isset($req->datefrom)){
        //     $query->where('tgl_visit', $req->datefrom);
        // }elseif(isset($req->dateto)){
        //     $query->where('tgl_visit', $req->dateto);
        // }

        // $query->where('id', $req->docid);
        // $query->orderBy('id');

        // return DataTables::queryBuilder($query)
        // ->editColumn('amount', function ($query){
        //     return [
        //         'amount1' => number_format($query->amount,0)
        //      ];
        // })->editColumn('approved_amount', function ($query){
        //     return [
        //         'amount2' => number_format($query->approved_amount,0)
        //      ];
        // })
        // ->toJson();
    }

    public function exportWaktuKunjungan(Request $req){
        return Excel::download(new KunjunganSalesExport($req), 'Laporan-waktu-kunjungan-sales.xlsx');
    }

    public function exportdetailDataKunjungan(Request $req){
        return Excel::download(new DetailKunjunganSalesExport($req), 'Laporan-detail-kunjungan.xlsx');
    }
}
