@extends('layouts/App')

@section('title', 'Laporan Detail Kunjugan Sales')

@section('additional-css')
    <style>
        td.details-control {
            background: url("{{ ('/assets/dist/img/show_detail.png') }}") no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            background: url("{{ ('/assets/dist/img/close_detail.png') }}") no-repeat center center;
        }
    </style>
@endsection

@section('content')        
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Laporan Detail Kunjungan Sales
                    </h3>
                    <div class="card-tools">
                        <!-- <a href="{{ url('transaction/budgeting') }}" class="btn btn-success btn-sm btn-add-dept">
                            <i class="fas fa-plus"></i> Buat Pengajuan Budget
                        </a> -->
                        <!-- <a href="{{ url('/master/department/create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Create Department
                        </a> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="{{ url('report/detailkunjungan/export') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label for="">Tanggal Kunjungan</label>
                                        <input type="date" class="form-control" name="datefrom" id="datefrom" value="{{ $_GET['datefrom'] ?? '' }}">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="">-</label>
                                        <input type="date" class="form-control" name="dateto" id="dateto" value="{{ $_GET['dateto'] ?? '' }}">
                                    </div>
                                    <div class="col-lg-3" style="text-align:center;">
                                        <br>
                                        <button type="button" class="btn btn-default mt-2 btn-search"> 
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <button type="submit" class="btn btn-success mt-2 btn-export"> 
                                            <i class="fa fa-download"></i> Export Data
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl-budget-list" class="table table-bordered table-hover table-striped table-sm" style="width:100%;">
                                <thead>
                                    <th></th>
                                    <!-- <th>No</th> -->
                                    <th>Nomor Kunjungan</th>
                                    <th>Nama Outlet</th>
                                    <th>Tanggal Kunjungan</th>
                                    <th>Nama Sales</th>
                                    <!-- <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    <th>Keterangan</th>
                                    <th>Pesan/Tidak</th> -->
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
</div>
@endsection

@section('additional-modal')

@endsection

@section('additional-js')
<script>
    function validate(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
        // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    $(document).ready(function(){

        $('.btn-search').on('click', function(){
            var param = '?datefrom='+ $('#datefrom').val() +'&dateto='+ $('#dateto').val();
            loadDocument(param);
        });

        loadDocument('');

        function loadDocument(_params){
            $("#tbl-budget-list").DataTable({
                serverSide: true,
                ajax: {
                    url: base_url+'/report/detaildatavisit'+_params,
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
                bDestroy: true,
                columns: [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": '',
                        "width": "30px"
                    },
                    // { "data": null,"sortable": false, "searchable": false,
                    //     render: function (data, type, row, meta) {
                    //         return meta.row + meta.settings._iDisplayStart + 1;
                    //     }  
                    // },
                    {data: "nomorvisit", className: 'uid'},
                    {data: "nama_outlet", className: 'uid'},
                    {data: "tgl_visit", className: 'uid'},
                    {data: "salesman", className: 'uid'},
                    // {data: "material"},
                    // {data: "matdesc"},
                    // {data: "keterangan" },
                    // {data: "status_order" }
                ]  
            });

            $('#tbl-budget-list tbody').on('click', 'tr td.details-control', function () {
                let _token   = $('meta[name="csrf-token"]').attr('content');

                var tabledata = $('#tbl-budget-list').DataTable();
                var tr = $(this).closest('tr');
                var row = tabledata.row( tr );
                var d = row.data();
                console.log(d)
                console.log(row.child.isShown())
                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                }else{
                    $.ajax({
                        url: base_url+'/report/detaildatavisitbyid',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            docid: d.id,
                            _token: _token
                        },
                        dataType: 'json',
                        cache:false,
                        success: function(result){
                        },
                        error: function(err){
                            console.log(err)
                        }
                    }).done(function(data){
                        
                        if ( row.child.isShown() ) {
                            row.child.hide();
                            tr.removeClass('shown');
                        }
                        else {
                            row.child( format(row.data(), data) ).show();
                            tr.addClass('shown');
                        }
                    });
                }
            });
        }

        function format ( d, results ) {
            // console.log(results)
            var tdStyle = '';
            var appStat = '';
            var appNote = '';
            var appDate = '';
            var appBy   = '';
            
            var html = '';
            html = `<table class="table table-bordered table-hover table-striped table-sm">
                   <thead>
                        <th>Line Item</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Keterangan</th>
                        <th>Order/Tidak</th>
                   </thead>
                   <tbody>`;
                for(var i = 0; i < results.length; i++){         
                    if(results[i].status_order === 'T'){
                        html +=`
                        <tr style="background-color:red; color:white;">
                            <td> `+ results[i].lineitem +` </td>
                            <td> `+ results[i].material +` </td>
                            <td> `+ results[i].matdesc +` </td>
                            <td> `+ results[i].keterangan +` </td>
                            <td> `+ results[i].status_order +` </td>
                        </tr>
                        `;
                    }else{
                        html +=`
                        <tr>
                            <td> `+ results[i].lineitem +` </td>
                            <td> `+ results[i].material +` </td>
                            <td> `+ results[i].matdesc +` </td>
                            <td> `+ results[i].keterangan +` </td>
                            <td> `+ results[i].status_order +` </td>
                        </tr>
                        `;
                    }      
                }

            html +=`</tbody>
                    </table>`;
            return html;
        } 
    });
</script>
@endsection