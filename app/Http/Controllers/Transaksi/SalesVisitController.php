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
        $checkLog = DB::table('ts_checklog')->where('date', date('Y-m-d'))
                    ->where('userid', Auth::user()->id)
                    ->where('checkoutstatus', 'O')
                    ->first();
        if($checkLog){
            $dataToko = DB::table('md_toko')->where('qrtoko', $checkLog->qrtoko)->first();
        }else{
            $dataToko = null;
        }
        return view('transaksi.salesvisit.visit', ['checklogstatus' => $checkLog, 'dataToko' => $dataToko]);
    }

    public function newVisit()
    {
        return view('transaksi.salesvisit.newvisit');
    }

    public function saveCheckLog(Request $req)
    {
        DB::beginTransaction();
        try{
            $dataToko = DB::table('md_toko')->where('qrtoko', $req['qrtoko'])->first();
            if($dataToko){
                $ckstat = '0';
                $checkCheckLog = DB::table('ts_checklog')
                                ->where('userid', Auth::user()->id)
                                ->where('qrtoko', $req['qrtoko'])
                                ->where('date', date('Y-m-d'))
                                ->where('checkoutstatus', 'O')
                                ->first();
                if($checkCheckLog){
                    DB::table('ts_checklog')
                    ->where('userid', Auth::user()->id)
                    ->where('qrtoko', $req['qrtoko'])
                    ->where('date', date('Y-m-d'))
                    ->update([
                        'checkout'       => getLocalDatabaseDateTime(),
                        'checkinstat'    => 'Y', 
                        'checkoutstat'   => 'Y',
                        'checkoutstatus' => 'C'
                    ]);

                    $ckstat = '1';
                }else{
                    DB::table('ts_checklog')->insert([
                        'userid'      => Auth::user()->id,
                        'qrtoko'      => $req['qrtoko'],
                        'date'        => date('Y-m-d'),
                        'checkin'     => getLocalDatabaseDateTime(),
                        'checkout'    => null,
                        'checkinstat' => 'Y' 
                    ]);
                }
                DB::commit();
    
                
                return response()->json(['success'=>'Checklog Berhasil', 'datatoko' => $dataToko, 'ckstat' => $ckstat]);
            }else{
                return response()->json(['error' => 'QR Toko Tidak Valid / Belum Terdaftar']);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function saveNew(Request $req){
        DB::beginTransaction();
        try{

            $idOutlet = generateIDoutlet($req['jenisoutlet']);

            $qrcode = Carbon::now()->timestamp;
            // $filepath = null;
            // if($request->file('pototoko')){
            //     $companyLogo = $request->file('pototoko');
            //     $filename    = $companyLogo->getClientOriginalName();
            //     $filepath    = '/storage/files/toko/'. $filename;  
            //     $companyLogo->move('storage/files/toko/', $filename);  
            // }            

            $insertData = array();
            $data = array(
                'tanggal'        => date('Y-m-d'),
                'nama_outlet'    => $req['namaToko'],
                'kode_outlet'    => $idOutlet,
                'contact_person' => $req['contactperson'],
                'no_telp'        => $req['telp'],
                'alamat'         => $req['alamat'],
                'kel_desa'       => $req['kelurahan'] ?? null,
                'kecamatan'      => $req['kecamatan'] ?? null,
                'kota'           => $req['kotamadya'] ?? null,                
                'kode_pos'       => $req['kodepos'] ?? null,
                'poto_toko'      => null,
                'createdon'      => date('Y-m-d H:m:s'),
                'createdby'      => Auth::user()->email ?? Auth::user()->username,
                'qrtoko'         => $qrcode
            );
            array_push($insertData, $data);
            insertOrUpdate($insertData,'md_toko');

            $nextNumber = DB::select('call sp_NextNRivVisit("VISIT", "'.date('Y').'")');
            // dd($nextNumber);
            $visitNumber = $nextNumber[0]->nextnumb;

            // $visitNumber = generateVisitNumber();
            // dd($visitNumber);
            $material = $req['kode_barang'];
            $matdesc  = $req['nama_barang'];
            $orderstt = $req['status_order'];
            $remarks  = $req['keterangan'];

            $insertHdr = array();
            $datHdr = array(
                'nomorvisit' => $visitNumber,
                'tgl_visit'  => date('Y-m-d'),
                'qrtoko'     => $qrcode,
                'salesman'   => $req['salesMan'],
                'createdon'  => getLocalDatabaseDateTime(),
                'createdby'  => Auth::user()->email ?? Auth::user()->username
            );
            array_push($insertHdr, $datHdr);
            // DB::table('ts_salesvisit01')->insert([
            // ]);
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
            insertOrUpdate($insertHdr,'ts_salesvisit01');
            insertOrUpdate($insertData,'ts_salesvisit02');

            DB::table('ts_checklog')->insert([
                'userid'      => Auth::user()->id,
                'qrtoko'      => $qrcode,
                'date'        => date('Y-m-d'),
                'checkin'     => $req['tglKunjungan'],
                'checkout'    => getLocalDatabaseDateTime(),
                'checkinstat'    => 'Y', 
                'checkoutstat'   => 'Y',
                'checkoutstatus' => 'C'
            ]);

            DB::commit();
            return Redirect::to("/transaksi/salesvisit/new")->withSuccess('Data kunjungan sales berhasil disimpan '. $visitNumber);
        } catch(\Exception $e){
            DB::rollBack();
            // dd($e);
            return Redirect::to("/transaksi/salesvisit/new")->withError($e->getMessage());
        }
    }

    public function save(Request $req)
    {
        // return $req;
        DB::beginTransaction();
        try{
            $nextNumber = DB::select('call sp_NextNRivVisit("VISIT", "'.date('Y').'")');
            // dd($nextNumber);
            $visitNumber = $nextNumber[0]->nextnumb;

            // $visitNumber = generateVisitNumber();
            // dd($visitNumber);
            $material = $req['kode_barang'];
            $matdesc  = $req['nama_barang'];
            $orderstt = $req['status_order'];
            $remarks  = $req['keterangan'];

            $insertHdr = array();
            $datHdr = array(
                'nomorvisit' => $visitNumber,
                'tgl_visit'  => date('Y-m-d'),
                'qrtoko'     => $req['qrtoko'],
                'salesman'   => $req['salesMan'],
                'createdon'  => getLocalDatabaseDateTime(),
                'createdby'  => Auth::user()->email ?? Auth::user()->username
            );
            array_push($insertHdr, $datHdr);
            // DB::table('ts_salesvisit01')->insert([
            // ]);
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
            insertOrUpdate($insertHdr,'ts_salesvisit01');
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
