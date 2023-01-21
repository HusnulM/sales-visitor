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
        return view('transaksi.salesvisit.visit');
    }

    public function saveCheckLog()
    {
        DB::beginTransaction();
        try{

        }catch(\Exception $e){
            DB::rollBack();
            // dd($e);
            // return Redirect::to("/transaksi/salesvisit")->withError($e->getMessage());
        }
    }

    public function save(Request $req)
    {
        // return $req;
        DB::beginTransaction();
        try{

            $visitNumber = generateVisitNumber();
            // dd($visitNumber);
            $material = $req['kode_barang'];
            $matdesc  = $req['nama_barang'];
            $orderstt = $req['status_order'];
            $remarks  = $req['keterangan'];

            DB::table('ts_salesvisit01')->insert([
                'nomorvisit' => $visitNumber,
                'tgl_visit'  => date('Y-m-d'),
                'salesman'   => $req['salesMan'],
                'createdon'  => getLocalDatabaseDateTime(),
                'createdby'  => Auth::user()->email ?? Auth::user()->username
            ]);
            $insertData = array();
            $count = 0;
            for($i = 0; $i < sizeof($material); $i++){
                $count = $count + 1;
                $data = array(
                    'nomorvisit'   => $visitNumber,
                    'lineitem'     => $count,
                    'material'     => $material[$i],
                    'matdesc'      => $matdesc[$i],
                    'status_order' => $orderstt[$i],
                    'keterangan'   => $remarks[$i],
                    'createdon'    => getLocalDatabaseDateTime(),
                    'createdby'    => Auth::user()->email ?? Auth::user()->username
                );
                array_push($insertData, $data);
            }
            insertOrUpdate($insertData,'ts_salesvisit02');
            DB::commit();
            return Redirect::to("/transaksi/salesvisit")->withSuccess('Data kunjungan sales berhasil disimpan '. $visitNumber);
        } catch(\Exception $e){
            DB::rollBack();
            // dd($e);
            return Redirect::to("/transaksi/salesvisit")->withError($e->getMessage());
        }
    }
}
