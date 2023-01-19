<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables, Auth, DB;
use Validator,Redirect,Response;

class PegawaiController extends Controller
{
    public function index(){
        $jabatan = DB::table('t_jabatan')->get();
        return view('master.pegawai.index',['jabatan' => $jabatan]);
    }

    public function findPegawai(Request $request){
        $query['data'] = DB::table('md_pegawai')->where('nama', 'like', '%'. $request->search . '%')->get();

        // return \Response::json($query);
        return $query;
    }

    public function listPegawai(Request $request){
        $params = $request->params;        
        $whereClause = $params['sac'];
        $query = DB::table('md_pegawai')->orderBy('id');
        return DataTables::queryBuilder($query)->toJson();
    }

    public function save(Request $req){
        // return $req;
        DB::beginTransaction();
        try{
            $nama = $req['nama'];
            $jbtn = $req['jabatan'];

            $insertData = array();
            for($i = 0; $i < sizeof($nama); $i++){
                $data = array(
                    'nama'          => $nama[$i],
                    'jabatan'       => $jbtn[$i],
                    'createdon'     => date('Y-m-d H:m:s'),
                    'createdby'     => Auth::user()->email ?? Auth::user()->username
                );
                array_push($insertData, $data);
            }
            insertOrUpdate($insertData,'md_pegawai');
            DB::commit();
            return Redirect::to("/master/pegawai")->withSuccess('Master pegawai Berhasil dibuat');
        } catch(\Exception $e){
            DB::rollBack();
            return Redirect::to("/master/pegawai")->withError($e->getMessage());
        }
    }

    public function update(Request $req){
        DB::beginTransaction();
        try{
            if($req['jabatan']){
                DB::table('md_pegawai')->where('id', $req['mkid'])->update([
                    'nama'      => $req['nama'],
                    'jabatan'   => $req['jabatan']
                ]);
            }else{
                DB::table('md_pegawai')->where('id', $req['mkid'])->update([
                    'nama' => $req['nama']
                ]);
            }
            DB::commit();
            return Redirect::to("/master/pegawai")->withSuccess('Data pegawai Berhasil di update');
        } catch(\Exception $e){
            DB::rollBack();
            return Redirect::to("/master/pegawai")->withError($e->getMessage());
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try{
            DB::table('md_pegawai')->where('id', $id)->delete();
            DB::commit();
            return Redirect::to("/master/pegawai")->withSuccess('Data pegawai Berhasil di Hapus');
        } catch(\Exception $e){
            DB::rollBack();
            return Redirect::to("/master/pegawai")->withError($e->getMessage());
        }
    }
}
