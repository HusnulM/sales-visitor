<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables, Auth, DB;
use Validator,Redirect,Response;

class TokoController extends Controller
{
    public function index(){
        return view('master.toko.index');
    }

    public function create(){
        if(checkAllowedAuth('ALLOW_CREATE_MD_TOKO')){
            return view('master.toko.create');
        }else{
            return Redirect::to("/master/toko")->withError('Tidak di izinkan untuk menambah master Toko');
        }        
    }

    public function edit($id){
        if(checkAllowedAuth('ALLOW_CHANGE_MD_TOKO')){
            $data = DB::table('md_toko')->where('id', $id)->first();
            return view('master.toko.edit', ['dataToko' => $data]);
        }else{
            return Redirect::to("/master/toko")->withError('Tidak di izinkan untuk mengubah master Toko');
        }        
    }

    public function getTokoByQr($qrcode)
    {
        $data = DB::table('md_toko')->where('qrtoko', $qrcode)->first();
        return $data;
    }

    public function listToko(Request $request){
        // $params = $request->params;        
        // $whereClause = $params['sac'];

        if(getJabatanCode() == "SLS"){
            $query = DB::table('md_toko')->where('createdby', Auth::user()->email)->orderBy('id');
        }else{
            $query = DB::table('md_toko')->orderBy('id');
        }
        // $query = DB::table('md_toko')->orderBy('id');
        // // getUserNameByID
        return DataTables::queryBuilder($query)
        ->editColumn('createdby', function ($query){
            return [
                'user' => getUserNameByID($query->createdby)
             ];
        })
        ->toJson();

        // echo "TEST";
    }

    public function save(Request $request){
        DB::beginTransaction();
        try{

            $idOutlet = generateIDoutlet($request['jenisoutlet']);

            $qrcode = Carbon::now()->timestamp;
            $filepath = null;
            if($request->file('pototoko')){
                $companyLogo = $request->file('pototoko');
                $filename    = $companyLogo->getClientOriginalName();
                $filepath    = '/storage/files/toko/'. $filename;  
                $companyLogo->move('storage/files/toko/', $filename);  
            }            

            $insertData = array();
            $data = array(
                'tanggal'        => $request['tanggal'],
                'nama_outlet'    => $request['namaOutlet'],
                'kode_outlet'    => $idOutlet,
                'contact_person' => $request['contactperson'],
                'no_telp'        => $request['telp'],
                'alamat'         => $request['address'],
                'kel_desa'       => $request['kelurahan'],
                'kecamatan'      => $request['kecamatan'],
                'kota'           => $request['kotamadya'],
                
                'kode_pos'       => $request['kodepos'],
                'poto_toko'      => $filepath,
                'createdon'      => date('Y-m-d H:m:s'),
                'createdby'      => Auth::user()->email ?? Auth::user()->username,
                'qrtoko'         => $qrcode
            );
            array_push($insertData, $data);
            insertOrUpdate($insertData,'md_toko');
            DB::commit();
            return Redirect::to("/master/toko")->withSuccess('Master Toko Berhasil dibuat');
        } catch(\Exception $e){
            DB::rollBack();
            return Redirect::to("/master/toko/create")->withError($e->getMessage());
        }
    }

    public function update(Request $request){
        DB::beginTransaction();
        try{

            // $idOutlet = generateIDoutlet($request['jenisoutlet']);
            $insertData = array();

            $qrcode = Carbon::now()->timestamp;
            $filepath = null;
            if($request->file('pototoko')){
                $companyLogo = $request->file('pototoko');
                $filename    = $companyLogo->getClientOriginalName();
                $filepath    = '/storage/files/toko/'. $filename;  
                $companyLogo->move('storage/files/toko/', $filename);  
                DB::table('md_toko')->where('id', $request['idoutlet'])->update([
                    'id'             => $request['idoutlet'],
                    'tanggal'        => $request['tanggal'],
                    'nama_outlet'    => $request['namaOutlet'],
                    'contact_person' => $request['contactperson'],
                    'no_telp'        => $request['telp'],
                    'alamat'         => $request['address'],
                    'kel_desa'       => $request['kelurahan'],
                    'kecamatan'      => $request['kecamatan'],
                    'kota'           => $request['kotamadya'],                
                    'kode_pos'       => $request['kodepos'],
                    'poto_toko'      => $filepath
                ]);
            }  else{
                DB::table('md_toko')->where('id', $request['idoutlet'])->update([
                    'id'             => $request['idoutlet'],
                    'tanggal'        => $request['tanggal'],
                    'nama_outlet'    => $request['namaOutlet'],
                    'contact_person' => $request['contactperson'],
                    'no_telp'        => $request['telp'],
                    'alamat'         => $request['address'],
                    'kel_desa'       => $request['kelurahan'],
                    'kecamatan'      => $request['kecamatan'],
                    'kota'           => $request['kotamadya'],                
                    'kode_pos'       => $request['kodepos']
                ]);
            }          

            // array_push($insertData, $data);
            // insertOrUpdate($insertData,'md_toko');
            DB::commit();
            return Redirect::to("/master/toko")->withSuccess('Master Toko Berhasil diupdate');
        } catch(\Exception $e){
            DB::rollBack();
            return Redirect::to("/master/toko/edit/".$request['idoutlet'])->withError($e->getMessage());
        }
    }

    public function delete($id){
        if(checkAllowedAuth('ALLOW_DELETE_MD_TOKO')){
            DB::beginTransaction();
            try{
                DB::table('md_toko')->where('id', $id)->delete();
                DB::commit();
                return Redirect::to("/master/toko")->withSuccess('Data Toko Berhasil di Hapus');
            } catch(\Exception $e){
                DB::rollBack();
                return Redirect::to("/master/toko")->withError($e->getMessage());
            }            
        }else{
            return Redirect::to("/master/toko")->withError('Tidak di izinkan untuk menghapus master Toko');
        }   
    }

    public function uploadMasterToko(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = $file->hashName();        

        $destinationPath = 'excel/';
        $file->move($destinationPath,$file->getClientOriginalName());

        config(['excel.import.startRow' => 2]);
        // import data
        $import = Excel::import(new PlayerImport(), 'excel/'.$file->getClientOriginalName());

        //remove from server
		unlink('excel/'.$file->getClientOriginalName());

        if($import) {
            return Redirect::to("/master/player")->withSuccess('Data Player Berhasil di Upload');
        } else {
            return Redirect::to("/master/player")->withError('Error');
        }
    }
}
