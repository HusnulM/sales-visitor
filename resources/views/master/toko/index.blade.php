@extends('layouts/App')

@section('title', 'Master Toko')

@section('additional-css')
@endsection

@section('content')        
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <div class="card-tools">
                        <!-- <a type="button" class="btn btn-success btn-sm btn-add-dept">
                            <i class="fas fa-plus"></i> Tambah Master Toko
                        </a> -->
                        <a href="{{ url('/master/toko/create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah Master Toko
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl-dept-master" class="table table-bordered table-hover table-striped table-sm" style="width:100%;">
                            <thead>
                                <th>No</th>
                                <th>ID Toko</th>
                                <th>Nama Toko</th>
                                <th>Tanggal</th>
                                <th>Pemilik Toko</th>
                                <th>Alamat</th>
                                <th style="text-align:center;"></th>
                            </thead>
                            <tbody>
    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-modal')

@endsection

@section('additional-js')
<script>
    $(document).ready(function(){
        $("#tbl-dept-master").DataTable({
            serverSide: true,
            ajax: {
                url: base_url+'/master/toko/listtoko',
                data: function (data) {
                    data.params = {
                        sac: "sac"
                    }
                }
            },
            buttons: false,
            searching: true,
            scrollY: 500,
            scrollX: true,
            scrollCollapse: true,
            columns: [
                { "data": null,"sortable": false, "searchable": false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }  
                },
                {data: "kode_outlet", className: 'uid'},
                {data: "nama_outlet", className: 'uid'},
                {data: "tanggal", className: 'uid'},
                {data: "contact_person", className: 'uid'},
                {data: "alamat", className: 'uid'},
                {"defaultContent": 
                    `<button class='btn btn-danger btn-sm button-delete'> <i class='fa fa-trash'></i> DELETE</button> 
                    <button class='btn btn-primary btn-sm button-edit'> <i class='fa fa-edit'></i> EDIT</button>
                    `,
                    "className": "text-center",
                    "width": "20%"
                }
            ]  
        });

        $('#tbl-dept-master tbody').on( 'click', '.button-delete', function () {
            var table = $('#tbl-dept-master').DataTable();
            selected_data = [];
            selected_data = table.row($(this).closest('tr')).data();
            window.location = base_url+"/master/toko/delete/"+selected_data.id;
        });
        $('#tbl-dept-master tbody').on( 'click', '.button-edit', function () {
            var table = $('#tbl-dept-master').DataTable();
            selected_data = [];
            selected_data = table.row($(this).closest('tr')).data();
            window.location = base_url+"/master/toko/edit/"+selected_data.id;
        });
    });
</script>
@endsection