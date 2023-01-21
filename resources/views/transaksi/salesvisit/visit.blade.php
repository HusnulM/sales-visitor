@extends('layouts/App')

@section('title', 'Check List Kunjungan')

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
    <form action="{{ url('transaksi/salesvisit/save') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-default btn-sm btn-scan-toko">
                                <i class="fas fa-qrcode"></i> Scan Qr Toko    
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Simpan List Kunjungan    
                            </button>
                        </div>
                    </div>
                    <div class="card-body">                    
                        <div class="row">
                            <div class="col-lg-2 col-md-12">
                                <div class="form-group">
                                    <label for="tgl">Tanggal Kunjugan</label>
                                    <input type="date" name="tglKunjungan" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label for="salesMan">Salesman</label>
                                    <input type="text" name="salesMan" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-12">
                                <div class="form-group">
                                    <label for="salesMan">Nama Toko</label>
                                    <input type="text" name="namaToko" id="namaToko" class="form-control" readonly>
                                    <input type="hidden" name="qrtoko" id="qrtoko">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <select name="material" id="find-material" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive">
                                    <table id="tbl-item-check" class="table table-bordered table-hover table-striped table-sm" style="width:100%;">
                                        <thead>                                        
                                            <th>Detail Barang</th>
                                            <!-- <th>Nama Barang</th>
                                            <th>Order</th>
                                            <th>Keterangan</th> -->
                                            <!-- <th style="text-align:center;"></th> -->
                                        </thead>
                                        <tbody id="tbl-item-check-body">
                
                                        </tbody>
                                    </table>        
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

<div class="modal fade" id="modalScanQRCodeToko" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Scan QR-Code Toko</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-lg-12">
          <div id="qrreader" width="600px" height="600px"></div>
        </div>
      </div>      
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default btn-stop-scan-qr">Close</button>
      </div>        
    </div>
  </div>
</div>

@endsection

@section('additional-js')
<script src="{{ asset('/assets/js/select2.min.js') }}"></script>
<script src="{{ asset('/assets/js/html5-qrcode.min.js') }}"></script>
<script>
    $(document).ready(function(){
        const html5QrCode = new Html5Qrcode("qrreader");
        // find-material
        let _token   = $('meta[name="csrf-token"]').attr('content');
        $(document).on('select2:open', (event) => {
            const searchField = document.querySelector(
                `.select2-search__field`,
            );
            if (searchField) {
                searchField.focus();
            }
        });

        $('#find-material').select2({ 
            placeholder: 'Masukkan Nama Barang',
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: base_url + '/master/item/findpartnumber',
                dataType: 'json',
                delay: 250,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                data: function (params) {
                    var query = {
                        search: params.term,
                        // custname: $('#find-customer').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // return {
                    //     results: response
                    // };
                    console.log(data)
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.material + ' - ' + item.matdesc,
                                slug: item.matdesc,
                                id: item.material,
                                ...item
                            }
                        })
                    };
                },
                cache: true
            }
        });
            
        $('#find-material').on('change', function(){
            var selected_data = $('#find-material').select2('data')
            console.log(selected_data);
            $('#tbl-item-check-body').append(`
                <tr>
                    <td>
                        <a>
                         <h4> `+ selected_data[0].matdesc +` </h4>
                        </a>
                        <input type="hidden" name="kode_barang[]" class="form-control form-sm" value="`+ selected_data[0].material +`" readonly/>
                        <input type="hidden" name="nama_barang[]" class="form-control form-sm" value="`+ selected_data[0].matdesc +`" readonly/>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <select name="status_order[]" class="form-control form-sm">
                                    <option value="">Order Lagi?</option>
                                    <option value="Y">Ya</option>
                                    <option value="T">Tidak</option>
                                </select>                                
                            </div>
                            <input type="text" name="keterangan[]" class="form-control form-sm"/>
                            <button type="button" class="btn btn-danger btn-sm btnRemove">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);

            $('.btnRemove').on('click', function(e){
                e.preventDefault();
                $(this).closest("tr").remove();
            });
        });

        $("#tbl-item-master").DataTable({
            serverSide: true,
            ajax: {
                url: base_url+'/master/item/itemlist',
                data: function (data) {
                    data.params = {
                        sac: "sac"
                    }
                }
            },
            buttons: false,
            searching: true,
            // scrollY: 500,
            // scrollX: true,
            // scrollCollapse: true,
            columns: [
                { "data": null,"sortable": false, "searchable": false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }  
                },
                {data: "material", className: 'uid'},
                {data: "matdesc", className: 'fname'},
                {"defaultContent": 
                    `<button class='btn btn-success btn-sm button-add-barang'> <i class='fa fa-add'></i> Check</button> 
                    `,
                    "className": "text-center",
                    "width": "20%"
                }
            ]  
        }).columns.adjust();

        $('#tbl-item-master tbody').on( 'click', '.button-add-barang', function () {
            var table = $('#tbl-item-master').DataTable();
            selected_data = [];
            selected_data = table.row($(this).closest('tr')).data();

            $('#tbl-item-check-body').append(`
                <tr>
                    <td>
                        <a>
                         <h4> `+ selected_data.matdesc +` </h4>
                        </a>
                        <input type="hidden" name="kode_barang[]" class="form-control form-sm" value="`+ selected_data.material +`" readonly/>
                        <input type="hidden" name="nama_barang[]" class="form-control form-sm" value="`+ selected_data.matdesc +`" readonly/>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <select name="status_order[]" class="form-control form-sm">
                                    <option value="">Order Lagi?</option>
                                    <option value="Y">Ya</option>
                                    <option value="T">Tidak</option>
                                </select>                                
                            </div>
                            <input type="text" name="keterangan[]" class="form-control form-sm"/>
                            <button type="button" class="btn btn-danger btn-sm btnRemove">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);

            $('.btnRemove').on('click', function(e){
                e.preventDefault();
                $(this).closest("tr").remove();
            });
        });
        
        $('.btn-scan-toko').on('click', function(){
            $('#modalScanQRCodeToko').modal('show');
            initialCamera()
        });            

        $('.btn-stop-scan-qr').on('click', function(){
            stopCamera()
            $('#modalScanQRCodeToko').modal('hide');
        });

        async function stopCamera() {
            html5QrCode.stop().then(ignore => {
            // QR Code scanning is stopped. 
                console.log("QR Code scanning stopped.");
                html5QrCode.clear();
            }).catch(err => { 
                // Stop failed, handle it. 
                console.log("Unable to stop scanning.");
            });
        }
        async function initialCamera() {
          var devices = await Html5Qrcode.getCameras();
          // const html5QrCode = new Html5Qrcode("reader");
          const qrCodeSuccessCallback = message => {
              // readWosData(message);
              // alert(message)
              console.log(message);
              html5QrCode.stop().then(ignore => {
                  // document.getElementById("reffid").focus();
                  $.ajax({
                    url: base_url+'/saleschecklog',
                    type:"POST",
                    data:{
                        qrtoko:message,
                        _token: _token
                    },
                    success:function(response){
                        console.log(response);
                        if(response.success){
                            alert(response.success);
                            $('#namaToko').val(response.datatoko.nama_outlet);
                            $('#qrtoko').val(message);
                        }else{
                            alert(response.error);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                        alert(error.error);
                    }
                  });
                  $('#modalScanQRCodeToko').modal('hide');
                  html5QrCode.clear();
                  setTimeout(function() { 
                      // $('#reffid').focus();
                  }, 1000);
              }).catch(err => {});
              
          }
          const qrErrorCallback = error => {}
          const config = {
              fps: 10,
              qrbox: 250
          };
    
          html5QrCode.start({
              deviceId: {
                  exact: (devices.length > 1) ? devices[devices.length - 1].id : devices[0].id
              }
          }, config, qrCodeSuccessCallback, qrErrorCallback);
        }
    });
</script>
@endsection