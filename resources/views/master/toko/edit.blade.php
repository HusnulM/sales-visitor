@extends('layouts/App')

@section('title', 'Update Master Toko')

@section('additional-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        .select2-container {
            display: block
        }

        .select2-container .select2-selection--single {
            height: 36px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ url('/master/toko/update') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Master Toko</h3>
                        <div class="card-tools">
                            <a href="{{ url('/master/toko') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-save"></i> SAVE
                            </button>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="kodeOutlet">Kode Outlet</label>
                                            <input type="text" name="kodeOutlet" id="kodeOutlet" class="form-control" value="{{ $dataToko->kode_outlet }}" readonly>
                                            <input type="hidden" name="idoutlet" value="{{ $dataToko->id }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $dataToko->tanggal }}" required>
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="jenisoutlet">Jenis Outlet</label>
                                            <select name="jenisoutlet" id="jenisoutlet" class="form-control" required>
                                                <option value="">Pilih Jenis Outlet</option>
                                                <option value="TK">Toko</option>
                                                <option value="GR">Grosir</option>
                                                <option value="WR">Warung</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-group">
                                            <label for="namaOutlet">Nama Outlet</label>
                                            <input type="text" name="namaOutlet" id="namaOutlet" class="form-control" autocomplete="off" value="{{ $dataToko->nama_outlet }}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-group">
                                            <label for="contactperson">Contact Person</label>
                                            <input type="text" name="contactperson" id="contactperson" class="form-control" autocomplete="off" value="{{ $dataToko->contact_person }}">
                                        </div>
                                    </div>  
                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-group">
                                            <label for="telp">No. Telp</label>
                                            <input type="text" name="telp" id="telp" class="form-control" autocomplete="off" value="{{ $dataToko->no_telp }}">
                                        </div>                                        
                                    </div>  
                                </div>                                
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 mb-0">
                                        <div class="form-group">
                                            <label for="address">Alamat Lengkap</label>
                                            <textarea name="address" cols="30" rows="5" class="form-control" style="height: 124px;">{!! $dataToko->alamat !!}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="desa">Kel/Desa</label>
                                            <input type="text" name="kelurahan" id="kelurahan" class="form-control" autocomplete="off" value="{{ $dataToko->kel_desa }}">
                                        </div>
                                    </div>  
                                    <div class="col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="kecamtan">Kecamatan</label>
                                            <input type="text" name="kecamatan" id="kecamatan" class="form-control" autocomplete="off" value="{{ $dataToko->kecamatan }}">
                                        </div>
                                    </div>  
                                    <div class="col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="kotamadya">Kab / Kotamadya</label>
                                            <input type="text" name="kotamadya" id="kotamadya" class="form-control" autocomplete="off" value="{{ $dataToko->kota }}">
                                        </div>
                                    </div>  
                                    <div class="col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="kodepos">Kode Pos</label>
                                            <input type="text" name="kodepos" id="kodepos" class="form-control" autocomplete="off" value="{{ $dataToko->kode_pos }}">
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="pototoko">Poto Toko</label>
                                    <input type="file" name="pototoko" id="pototoko" class="form-control" autocomplete="off">
                                    <img src="{{ $dataToko->poto_toko }}" class="img-fluid" alt="image" style="width:600px; height: 500px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('additional-js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        
    });
</script>
@endsection