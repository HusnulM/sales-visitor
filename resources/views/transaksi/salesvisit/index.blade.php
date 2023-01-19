@extends('layouts/App')

@section('title', 'Check List Kunjungan')

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
                        <a href="{{ url('/master/toko/create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Simpan List Kunjungan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <label for="tgl">Tanggal Kunjugan</label>
                            <input type="date" name="tglKunjungan" class="form-control" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <label for="salesMan">Salesman</label>
                            <input type="text" name="salesMan" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="table-responsive">
                                <table id="tbl-item-master" class="table table-bordered table-hover table-striped table-sm" style="width:100%;">
                                    <thead>
                                        <th>No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th style="text-align:center;"></th>
                                    </thead>
                                    <tbody>
            
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                        <div class="col-lg-8">
                            <div class="table-responsive">
                                <table id="tbl-item-check" class="table table-bordered table-hover table-striped table-sm" style="width:100%;">
                                    <thead>
                                        
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Order</th>
                                        <th>Keterangan</th>
                                        <th style="text-align:center;"></th>
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

<script>
    $(document).ready(function(){
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
                        `+ selected_data.material +`
                        <input type="hidden" name="kode_barang[]" class="form-control" value="`+ selected_data.material +`" readonly/>
                    </td>
                    <td>
                        `+ selected_data.matdesc +`
                        <input type="hidden" name="nama_barang[]" class="form-control" value="`+ selected_data.matdesc +`" readonly/>
                    </td>
                    <td>
                        <select name="status_order[]" class="form-control">
                            <option value="">Order Lagi?</option>
                            <option value="Y">Ya</option>
                            <option value="T">Tidak</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="keterangan[]" class="form-control"/>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-danger btn-sm btnRemove">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            $('.btnRemove').on('click', function(e){
                e.preventDefault();
                $(this).closest("tr").remove();
            });
        });
        
    });
</script>
@endsection