@extends('layouts/App')

@section('title', 'Master Toko')

@section('additional-css')
<style>
    
</style>
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
                        @if(checkAllowedAuth('ALLOW_CREATE_MD_TOKO'))
                        <a href="{{ url('/master/toko/create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah Master Toko
                        </a>
                        @endif
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
                                <th>Created By</th>
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
<div class="modal fade" id="modal-show-qrcode">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">QR Code Toko</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="col-lg-12">
                <div class="row">
                    <div class="qr-code-container">
                        <div id="qrcode" class="qr-code"></div> 
                        <input type="hidden" id="qrcodeid">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary download-qrcode">Download QR Code</button>
        </div>
      </div>
    </div>
</div>

@endsection

@section('additional-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
                {data: "createdby", className: 'uid',
                    render: function (data, type, row){
                        return ``+ row.createdby.user + ``;
                    },
                },
                // createdby
                {"defaultContent": 
                    `
                    @if(checkAllowedAuth('ALLOW_DELETE_MD_TOKO'))
                    <button class='btn btn-danger btn-sm button-delete'> <i class='fa fa-trash'></i> DELETE</button> 
                    @endif
                    @if(checkAllowedAuth('ALLOW_CHANGE_MD_TOKO'))
                    <button class='btn btn-primary btn-sm button-edit'> <i class='fa fa-edit'></i> EDIT</button>
                    @endif
                    @if(checkAllowedAuth('ALLOW_DISPLAY_QR_TOKO'))
                    <button class='btn btn-success btn-sm button-show-qr'> <i class='fa fa-qrcode'></i> VIEW QRCODE</button>
                    @endif
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
        $('#tbl-dept-master tbody').on( 'click', '.button-show-qr', function () {
            var table = $('#tbl-dept-master').DataTable();
            selected_data = [];
            selected_data = table.row($(this).closest('tr')).data();
            $('#qrcode').html('');
            $('#qrcodeid').val(selected_data.qrtoko);
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: selected_data.qrtoko,
                width: 265,
                height: 260,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });

            $('#modal-show-qrcode').modal('show');
        });

        $('.download-qrcode').on('click', function(){
            downloadQRCode();
        });

        function downloadQRCode(){
            setTimeout(
                function ()
                {
                    let dataUrl = document.querySelector('#qrcode').querySelector('img').src;
                    downloadURI(dataUrl, $('#qrcodeid').val()+'.png');
                }
            ,1000);
        }

        function downloadURI(uri, name) {
            var link = document.createElement("a");
            link.download = name;
            link.href = uri;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;
        };
    });
</script>
@endsection