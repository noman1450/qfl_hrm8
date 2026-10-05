@extends('layouts.main')
@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

  <style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th,
  .table>tfoot>tr>td, .table>tfoot>tr>th,
  .table>thead>tr>td, .table>thead>tr>th{
    padding: 5px;
  }
  </style>

@endsection
@section('content')
<!-- SELECT2 EXAMPLE -->
<div class="box box-default">
  <div class="box-header with-border" style="position: inherit;">
    <h3 id="page-header" class="box-title">Edit Asset Storage</h3>
    @if ($errors->any())
        <div class="alert alert-danger" alert-dismissible>
            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


   <div class="box-tools pull-right">
      <a type="button" href="{{ URL::previous() }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
    </div>
  </div>
  <!-- /.box-header -->

        <form method="POST" action="{{ route('assetstore.update', $hrmAssetPurchaseMaster->asset_store_master_id) }}" onkeypress = "return event.keyCode != 13;"  id="assetstore_form" enctype="multipart/form-data">
          {{ method_field('put') }}
          @php $form_type='edit' @endphp
          @include('assetstore._form')
        </form>

    <!-- /.box -->
  @endsection
  @section('script')
    <!-- InputMask -->

    <script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
    <script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
    <script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script>
    $(document).ready(function($) {

        jQuery.fn.dataTable.Api.register( 'sum()', function() {
            return this.flatten().reduce(function(a, b) {
                // console.log(b);
                var x = parseFloat(a) || 0;
                var y = parseFloat($(b).attr('data')) || 0;
                return x + y
            }, 0);
        });

      var table = $('#asset_store_table').DataTable({
        searching: false,
        ordering: false,
        paging: false,
        bInfo: false,

        drawCallback: function() {

          $('.select2').select2({
            placeholder: "Enter Model",
            allowClear: true,
            ajax: {
              dataType: 'json',
              url: '{{URL::to('/')}}/asset_list_data',
              headers:{
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
              delay: 250,
              data: function(params) {
                return {
                  term: params.term
                }
              },
              processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                  results: data
                };
              }
            }
          });
          var api = this.api();
          $('#quantityf').attr('value', api.column( 4, {page:'current'} ).nodes().sum());

          var api2 = this.api();
          $('#totalf').attr('value', api2.column( 5, {page:'current'} ).nodes().sum());
        }
      });
      $('#asset_store_table tbody').on( 'click', '#row_delete', function () {
        table.row (
            $(this).parents('tr')).remove().draw();
      });

      function calcexec(){
        var qty2 = new Array(200);
        var price2 = new Array(200);

        var i = 0;
        $(".qty2").each(function() {
          qty2[i] = $(this).val();

          console.log(qty2[i]);
          i++;
        });

        var i = 0;
        $(".price2").each(function() {
          price2[i] = $(this).val();
          i++;
        });

        var i = 0;
        var j = 0;
        var k = 0;
        $(".tot").each(function() {
          $(this).val(qty2[i]*price2[i]);
          j += qty2[i]*price2[i]
          k += parseFloat(qty2[i])
          i++;
        });

        $('#totalf').attr('value', j);
        $('#quantityf').attr('value', k);

      }

      $('#asset_store_table tbody').on( 'keyup mouseup', '#qty2, #price2', function(){
        calcexec();
      });

      // $('#asset_store_table tbody').on( 'keyup mouseup', '#price2', function(){
      //   calcexec();
      // });

      $('#asset_store_table thead').on( 'click', '#add', function(){
        var asset_list = $('#asset_list').find(":selected").text();
        var qty = $('#qty').val();
        var price = $('#price').val();
        var product_id = $('#asset_list').val();
        var depreciation_rate = $('#depreciation_rate').val()

        if ($('#asset_list').val() == null) {
            alert('Enter A model')

            $('#asset_list').focus()

            return;
        }

        if (depreciation_rate === '') {
            alert('Depreciation Rate can not be empty')

            $('#depreciation_rate').focus()

            return;
        }

        if (price === '') {
            alert('Price can not be empty')

            $('#price').focus()

            return;
        }

        if (qty === '') {
            alert('Quantity can not be empty')

            $('#qty').focus()

            return;
        }

        if($('#qty').val() != '' && $('#price').val() != '' && $('#asset_list').val() != null && $('#qty').val() != '0'){
          if($('#serial').val()==0){
            table.row.add([
              '<input name="ledger_id[]" class="hidden" value="new"><select class="form-control select2" name="product_id[]" style="width: 100%;"><option value="'+product_id+'" selected>'+asset_list+'</option></select>',
              '<input  type="number" name="depreciation_rate[]" class="form-control" style="width: 100%;text-align:center;" value="'+depreciation_rate+'" required>',
              '<input  type="text"    name="serial_no[]"  class="form-control price_info"   style="width: 100%;text-align:center;"  value="None" readonly>',
              '<input  type="text"  id="price2"  name="price_info[]"  class="form-control price2"    style="width: 100%;text-align:center;" value="'+price+'" focusin="this.select();" onclick="this.select();">',
              '<input  type="text"  id="qty2"  name="qty_info[]"   class="form-control qty2"     style="width: 100%;text-align:center;"  value="'+qty+'" focusin="this.select();" onclick="this.select();">',
              '<input  type="text"   class="form-control tot"  id="tot"    style="width: 100%;text-align:center;"  value="'+qty*price+'" focusin="this.select();" onclick="this.select();" readonly>',
              '<button type="button" class="btn btn-info btn-block btn-flat btn-danger" id="row_delete">Delete</button>'
            ] ).draw()
          } else{
            for(var i=0; i<qty; i++){
              table.row.add([
                '<input name="ledger_id[]" class="hidden" value="new"><select class="form-control select2" name="product_id[]" style="width: 100%;"><option value="'+product_id+'" selected>'+asset_list+'</option></select>',
                '<input  type="number" name="depreciation_rate[]" class="form-control" style="width: 100%;text-align:center;" value="'+depreciation_rate+'" required>',
                '<input  type="text"    name="serial_no[]"     class="form-control price_info check_serial_no"   placeholder="Serial No" style="width: 100%;text-align:center;"  value="" required>' +
                '<p style="margin-bottom:0;color:#af4242;" class="check_serial_no_error"></p>',
                '<input  type="text"  id="price2"  name="price_info[]"  class="form-control price2"    style="width: 100%;text-align:center;" value="'+price+'" focusin="this.select();" onclick="this.select();">',
                '<input  type="text"  id="qty2"  name="qty_info[]"   class="form-control qty2"     style="width: 100%;text-align:center;"  value="'+1+'" focusin="this.select();" onclick="this.select();" readonly>',
                '<input  type="text"   class="form-control tot"  id="tot"    style="width: 100%;text-align:center;"  value="'+1*price+'" focusin="this.select();" onclick="this.select();" readonly>',
                '<button type="button" class="btn btn-info btn-block btn-flat btn-danger" id="row_delete">Delete</button>'
              ] ).draw()
            }
          }
        }

        $('#qty, #price, #depreciation_rate').val('');
        $('#total').val(0);
        $('#asset_list').val(null).trigger('change');

        calcexec();
      });

      $('#asset_store_table thead').on( 'keyup mouseup mousewheel', '#price, #qty', function () {
        $('#total').val($('#price').val()*$('#qty').val());
      });

        var serialList = []

        $(document).on('blur', '.check_serial_no', function (e) {
            if ('' === e.target.value) return;

            var existValue = serialList.find(value => value === e.target.value)

            var id = $(this).siblings('.ledger_id').val()

            $.get("{{ url('check_unique_serial_no') }}", { serial_no: e.target.value, id: id })
                .then(() => {
                    $(this).siblings('.check_serial_no_error').text('')


                    // if (existValue) {
                    //     alert('Duplicate value not allowd..!')
                    //     serialList.forEach(element => {
                    //         if(element == e.target.value) {
                    //             serialList.splice(element, 1);

                    //             return;
                    //         }
                    //     })

                    //     $(this).val('').focus()
                    // }
                    // else {
                    //     serialList.push(e.target.value)
                    // }
                })
                .catch(({ responseJSON }) => {
                    $(this).siblings('.check_serial_no_error').text(responseJSON.errors.serial_no[0])

                    // if (existValue) {
                    //     serialList.forEach(element => {
                    //         if(element == e.target.value) {
                    //             serialList.splice(element, 1);

                    //             return;
                    //         }
                    //     });

                    //     $(this).val('').focus()
                    // }
                    // else {
                    //     serialList.push(e.target.value)
                    // }
                })
        });
    });
  </script>

@endsection
