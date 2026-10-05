
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
<div class="box box-default">

  <div class="box-header with-border" style="position: inherit;">
    <h3 id="page-header" class="box-title">Asset Store</h3>

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

  <form method="POST" action="{{ route('assetstore.store') }}" onkeypress = "return event.keyCode != 13;" id="asset_store_form">
    @php $form_type ='create' @endphp
    @include('assetstore._form')
  </form>


@endsection

@section('script')
  <script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script>
    $(document).ready(function($) {

        $(document).on('submit', '#asset_store_form', function(e) {
            e.preventDefault()

            $.ajax({
                type: "POST",
                url: this.action,
                data: new FormData(this),
                cache:false,
                contentType: false,
                processData: false,
                success: function (data) {
                    alert(data.success)
                    window.location.href = "{{ url('assetstore') }}"
                },

                error: function (e) {
                    console.log("ERROR : ", e)
                }
            });
        })

      var table = $('#asset_store_table').DataTable({
        searching: false,
        ordering: false,
        paging: false,
        bInfo: false,

        drawCallback: function(row, data, start, end, display) {

            $asset_list = $('.select2').select2({
                placeholder: "Enter Model",
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: '{{ url("/asset_list_data") }}',
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
                    }
                },
                templateSelection: function (data, container) {
                    // Add custom attributes to the <option> tag for the selected option
                    $(data.element).attr('data-custom-attribute', data.customValue);
                    return data.text;
                },
              cache: true
            }
        });

        $asset_list.on('select2:select', (e) => {
            $('#depreciation_rate').val('')
            $('#depreciation_rate').val(e.params.data.depreciation_rate)
        })
        $asset_list.on('select2:unselect', () => {
            $('#depreciation_rate').val('')
        })

        var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                      i : 0;
          };

          // Total over this page
          pageTotal = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

          // Update footer
          $( api.column( 4 ).footer() ).html(
              'Total QTY = '+pageTotal
          );

          pageTotal = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

          // Update footer
          $( api.column( 5 ).footer() ).html(
              'Total Price = '+pageTotal
          );
        }
      });

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
          if($('#serial').val() == 0) {
            table.row.add([
              asset_list,
              '<input  type="number" name="depreciation_rate[]" class="form-control" style="width: 100%;text-align:center;" value="'+depreciation_rate+'" required>',
              '<input  type="text" name="serial[]" readonly class="form-control price_info" style="width: 100%;text-align:center;"  value="None">',
              price,
              qty,
              price*qty,
              '<button type="button" class="btn btn-info btn-block btn-flat btn-danger" id="row_delete">Delete</button><input name="qty_info[]" value="'+qty+'" class="hidden"><input name="price_info[]" value="'+price+'" class="hidden"><input name="product_id[]" value="'+product_id+'" class="hidden">'
            ]).draw()
          } else {
            for(var i=0; i < qty; i++) {
              table.row.add([
                asset_list,
                '<input  type="number" name="depreciation_rate[]" class="form-control" style="width: 100%;text-align:center;" value="'+depreciation_rate+'" required>',
                '<input type="text" name="serial[]" class="form-control price_info check_serial_no" placeholder="Serial No" style="width: 100%;text-align:center;" value="" required>' +
                '<p style="margin-bottom:0;color:#af4242;" class="check_serial_no_error"></p>',
                price,
                1,
                price,
                '<button type="button" class="btn btn-info btn-block btn-flat btn-danger" id="row_delete">Delete</button><input name="qty_info[]" value="'+1+'" class="hidden"><input name="price_info[]" value="'+price+'" class="hidden"><input name="product_id[]" value="'+product_id+'" class="hidden">'
              ] ).draw()
            }
          }
        }

        $('#qty, #price, #depreciation_rate').val('');
        $('#total').val(0);
        $('#asset_list').val(null).trigger('change');

      });

    $('#asset_store_table tbody').on( 'click', '#row_delete', function () {
        table.row($(this).parents('tr')).remove().draw();
    });

    $('#asset_store_table thead').on( 'keyup mouseup mousewheel', '#price, #qty', function () {
        $('#total').val($('#price').val()*$('#qty').val());
    });

        var serialList = []

        $(document).on('blur', '.check_serial_no', function (e) {
            if ('' === e.target.value) return;

            var existValue = serialList.find(value => value === e.target.value)

            $.get("{{ url('check_unique_serial_no') }}", { serial_no: e.target.value })
                .then(() => {
                    $(this).siblings('.check_serial_no_error').text('')


                    if (existValue) {
                        alert('Duplicate value not allowd..!')
                        serialList.forEach(element => {
                            if(element == e.target.value) {
                                serialList.splice(element, 1);

                                return;
                            }
                        })

                        // console.log('thenif', serialList);

                        $(this).val('').focus()
                    }
                    else {
                        serialList.push(e.target.value)

                        // console.log('thenelse', serialList);
                    }


                })
                .catch(({ responseJSON }) => {
                    $(this).siblings('.check_serial_no_error').text(responseJSON.errors.serial_no[0])

                    if (existValue) {
                        serialList.forEach(element => {
                            if(element == e.target.value) {
                                serialList.splice(element, 1);

                                return;
                            }
                        });

                        console.log('catch', serialList);

                        $(this).val('').focus()
                    }

                })
        });

    });
  </script>
@endsection
