<!-- category_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">


@endsection

<!-- content -->
@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Cost to the Company Category</h3>
        <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
        </div>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Month From</label>
                <input type="text" class="form-control Month_date"  placeholder="Month From" name="month_from" id="month_from" value="{{ request()->has('from') ? date('M-Y', strtotime(request('from'))) : date('M-Y', strtotime('-1 month')) }}"  readonly required>
              </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Month To</label>
                <input type="text" class="form-control date" placeholder="Month To" name="month_to" id="month_to" value="{{ request()->has('to') ? date('M-Y', strtotime(request('to'))) : date('M-Y', strtotime('-1 month')) }}" readonly required>
              </div>
            </div>


           <div class="col-lg-2 col-md-3 col-xs-12 form-group">
                <label><span style="color:red;">Filter</span> </label>
                <select  class="form-control col-lg-12 onchange" id="filter_name_id" name="filter_name_id" style="width: 100%;"  required>

                    @if (request()->filter_name_id != 'null')
                        <option value="{{ request('filter_name_id') }}">{{ request('filter_name') }}</option>
                    @endif

                </select>
            </div>



          <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <label>Location Name</label>
            <select  class="form-control col-lg-12 onchange" id="location" name="location" style="width: 100%;" required>
                @if (request()->has('location'))
                    <option value="{{ request('location') }}">{{ request('location_name') }}</option>
                @endif
            </select>
         </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <label>Category Name</label>
            <select  class="form-control col-lg-12 onchange" id="employee_category" name="employee_category" style="width: 100%;"  required>
            </select>
         </div>



    <div class="box-body">
        <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
            <thead style="font-size: 12px;">
                <tr>
                    <th style="width: 10%">Location Name</th>
                    <th style="width: 10%">Category Name</th>
                    <th style="width: 10%">CountEmp</th>
                    <th style="width: 05%">Gross Salary</th>
                    <th style="width: 05%">Two Festival Bonus</th>
                    <th style="width: 05%">PF Com. Contribution</th>
                    <th style="width: 05%">OutofPocker & Transport</th>
                    <th style="width: 05%">House Rent</th>
                    <th style="width: 05%">Fixed Allowance</th>
                    <th style="width: 05%">Total Cost</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
            </tbody>

            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>

        </table>
    </div>
</div>

@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>

     $('#month_from').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months", 
        minViewMode: "months",
        }).on('hide', function(ev) { 
            console.log("try");
            dataLoad();
        });

    $('#month_to').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months", 
        minViewMode: "months",
        onClose: function () {
            dataLoad();
        }        
    }).on('hide', function(ev){
        dataLoad();
    });







$(document).ready(function($) {


    $custom_filter = select2Dropdown("#filter_name_id", "{{ url('/filter_name_list') }}", "Enter Filter Name");

    $custom_filter.on('select2:close', function (e) {
        dataLoad();
    });


    $custom_filter.on('select2:unselect', function (e) {
        dataLoad();
    });





    $location = $('#location').select2({
        placeholder: 'Enter a Location',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/location_list_data',
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
            },
            cache: true
        }
    });

    $location.on('select2:close', function (e) {
        dataLoad();
    });


    $location.on('select2:unselect', function (e) {
        // $('#location').val(null).trigger("change");
        dataLoad();
    });



    $employee_category = $('#employee_category').select2({
        placeholder: 'Enter a Category',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/category_list_data',
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
            },
            cache: true
        }
    });

    $employee_category.on('select2:close', function (e) {
        dataLoad();
    });


    $employee_category.on('select2:unselect', function (e) {
        // $('#employee_category').val(null).trigger("change");
        dataLoad();
    });



    dataLoad = function(){

        var table = $('#designation_list_table').DataTable( {
                "destroy":    true,
                "processing": true,
                "serverSide": false,
                "searching":  true,
                "ordering":   false,
                "bInfo":      true,
                "paging":     false,
                "scrollX": true,
                "pageLength":100,    
                "width":'100%',                         
                "scrollY":'calc(100vh - 340px)',
                "dom": 'Bfrtip',
                      buttons: [
                          'excel','pdf'
                      ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                 total_amount8 = api
                        .column(2)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);



                    total_amount9 = api
                        .column(3)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);


                    total_amount10 = api
                        .column(4)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    total_amount11 = api
                        .column(5)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    total_amount12 = api
                        .column(6)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    total_amount13 = api
                        .column(7)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    total_amount14 = api
                        .column(8)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    total_amount15 = api
                        .column(9)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);


                    $( api.column( 2 ).footer() ).html(total_amount8);
                    $( api.column( 3 ).footer() ).html(total_amount9);
                    $( api.column( 4 ).footer() ).html(total_amount10);
                    $( api.column( 5 ).footer() ).html(total_amount11);
                    $( api.column( 6 ).footer() ).html(total_amount12);
                    $( api.column( 7 ).footer() ).html(total_amount13);
                    $( api.column( 8 ).footer() ).html(total_amount14);
                    $( api.column( 9 ).footer() ).html(total_amount15);
                },





                "ajax": {
                    "url": "{{URL::to('/')}}/cost_to_the_company_category_data",
                    "type": "POST",
                    "headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    "data":   {month_from: $("#month_from").val(),
                         month_to:$("#month_to").val(), 
                         hrm_location_id : $("#location option:selected").val(),
                         hrm_category_id : $("#employee_category option:selected").val(),
                         filter_name_id: $('#filter_name_id option:selected').val()


                  }
                },

                "columns": [

                  { "data": "location_name" },
                  { "data": "Link",
                    "mRender": function (data, type, full) {
                        return '<a   href="{{URL::to('/')}}/cost_to_the_company?location='+full.hrm_location_id+'&location_name='+full.location_name+'&from='+$('#month_from').val()+'&to='+$('#month_to').val()+'&category='+full.id+'&category_name='+full.category_name+'&filter_name_id='+$('#filter_name_id').val()+'&filter_name='+$('#filter_name_id option:selected').text()+  '">'+full.category_name+'</a>';
                    }
                  },
                    
                  { "data": "NoOfEmployee" },
                  { "data": "gross_salary"},
                  { "data": "two_festival_bonus"},
                  { "data": "pf_contribution"},
                  { "data": "transport_outOfPocket"},
                  { "data": "houseRent_allowance"},
                  { "data": "fixed_allowance"},
                  { "data": "existing_cost"}
                ]
        });

    };
   dataLoad();
});

</script>

@endsection
