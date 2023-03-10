@extends('layouts/App')

@section('title', 'Laporan Kunjugan Sales')

@section('additional-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Laporan Waktu Kunjungan Sales
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
                            <form action="{{ url('report/waktukunjungan/export') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-2 col-md-6 col-sm-12">
                                        <label for="">Tanggal Kunjungan</label>
                                        <input type="date" class="form-control" name="datefrom" id="datefrom" value="{{ $_GET['datefrom'] ?? '' }}">
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-12">
                                        <label for="">-</label>
                                        <input type="date" class="form-control" name="dateto" id="dateto" value="{{ $_GET['dateto'] ?? '' }}">
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-12">
                                        <label for="">Salesman</label>
                                        <select name="salesman" id="findSalesman" class="form-control">
                                            <option value="">Tampilkan Semua Sales</option>
                                            @foreach($sales as $key => $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-12" style="text-align:center;">
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
                                    <th>No</th>
                                    <th>Nama Outlet</th>
                                    <th>Tanggal Kunjungan</th>
                                    <th>Nama Sales</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Lama Kunjungan (Menit)</th>
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
<script src="{{ asset('/assets/js/select2.min.js') }}"></script>
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

        // let _token   = $('meta[name="csrf-token"]').attr('content');
        // $(document).on('select2:open', (event) => {
        //     const searchField = document.querySelector(
        //         `.select2-search__field`,
        //     );
        //     if (searchField) {
        //         searchField.focus();
        //     }
        // });

        // $('#findSalesman').select2({ 
        //     placeholder: 'Masukkan Nama Sales',
        //     width: '100%',
        //     minimumInputLength: 0,
        //     ajax: {
        //         url: base_url + '/report/findsales',
        //         dataType: 'json',
        //         delay: 250,
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': _token
        //         },
        //         data: function (params) {
        //             var query = {
        //                 search: params.term,
        //                 // custname: $('#find-customer').val()
        //             }
        //             return query;
        //         },
        //         processResults: function (data) {
        //             // return {
        //             //     results: response
        //             // };
        //             console.log(data)
        //             return {
        //                 results: $.map(data.data, function (item) {
        //                     return {
        //                         text: item.name,
        //                         slug: item.name,
        //                         id: item.id,
        //                         ...item
        //                     }
        //                 })
        //             };
        //         },
        //         cache: true
        //     }
        // });

        $('.btn-search').on('click', function(){
            var salesID = $('#findSalesman').val();
            // alert(salesID)
            var param = '?datefrom='+ $('#datefrom').val() +'&dateto='+ $('#dateto').val() +'&sales='+salesID;
            loadDocument(param);
        });

        loadDocument('');

        function loadDocument(_params){
            $("#tbl-budget-list").DataTable({
                serverSide: true,
                ajax: {
                    url: base_url+'/report/salesvisitdata'+_params,
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
                    { "data": null,"sortable": false, "searchable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }  
                    },
                    {data: "nama_outlet", className: 'uid'},
                    {data: "date", className: 'uid'},
                    {data: "name", className: 'uid'},
                    {data: "checkin"},
                    {data: "checkout"},
                    {data: "totalJamKunjungan" }
                ]  
            });
        }
    });
</script>
@endsection