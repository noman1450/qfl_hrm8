<!-- category_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style>
    table.dataTable tbody tr.group-row td {
        font-weight: bold;
        background: #eef2fb;
    }
    table.dataTable tbody tr.total-row td {
        font-weight: bold;
        background: #fff2cc;
    }
    #designation_list_table th.nowrap,
    #designation_list_table td.nowrap {
        white-space: nowrap;
    }
</style>

@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Cost to the Company Location</h3>
    <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">
    </div>
    <div class="box-tools pull-right">
      <button type="button" id="export_excel_btn" class="btn btn-sm btn-success btn-flat" style="margin-right: 5px;"><i class="fa fa-file-excel-o"></i> Excel</button>
      <button type="button" id="export_pdf_btn" class="btn btn-sm btn-danger btn-flat" style="margin-right: 5px;"><i class="fa fa-file-pdf-o"></i> PDF</button>
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                  <label>Month From</label>
                  <input type="text" class="form-control Month_date"  placeholder="Month From" name="month_from" id="month_from" value="{{ request()->has('year_month') ? date('M-Y', strtotime(request('year_month'))) : date('M-Y', strtotime('-1 month')) }}"  readonly required>
                </div>
              </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                  <label>Month To</label>
                  <input type="text" class="form-control date" placeholder="Month To" name="month_to" id="month_to" value="{{ request()->has('year_month') ? date('M-Y', strtotime(request('year_month'))) : date('M-Y', strtotime('-1 month')) }}" readonly required>
                </div>
              </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                  <label>Location</label>
                  <select class="form-control col-lg-12" id="location" name="location" style="width: 100%;">
                  </select>
                </div>
              </div>


  


<div class="box-body">
    <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
        <thead style="font-size: 12px;">
        </thead>
        <tbody style="font-size: 12px;">
        </tbody>

        <tfoot>
        </tfoot>
    </table>
</div>
</div>
<!--  Create New Salary Grade Modal-->
<div class="modal fade" id="modal_create_salary_grade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="groupAddLabel">Create New Salary Grade</h4>
      </div>
      
      <form method="POST" action="{{url('salarygrade')}}">
        {{ csrf_field() }}
        <div class="modal-body">
          
          <div class="row">
            
            <div class="form-group">
              <div class="col-md-12">
                <label>Grade Name</label>
                <input class="form-control" type="text" placeholder="Salary Grade Name" name="grade_name" required>
              </div>
            </div>
               <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <label>Location Name</label>
            <select  class="form-control col-lg-12 onchange" id="location" name="location" style="width: 100%;"  required>
            </select>
         </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group">
            <label>Category Name</label>
            <select  class="form-control col-lg-12 onchange" id="employee_category" name="employee_category" style="width: 100%;"  required>
            </select>
         </div>
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button_sunmit" class="btn btn-success btn-flat">Submit</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End of Create New Salary Grade Modal-->
</div>

@endsection

<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<script>

$(document).ready(function($) {
    $('#month_from').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months",
        }).on('hide', function(ev) {

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
                data.unshift({
                    id: '999',
                    text: '-- All Depot --'
                });
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


    exportReport = function(type){
        var url = '{{URL::to('/')}}/cost-to-the-company/' + type + '-export';
        var $form = $('<form method="POST" action="' + url + '" style="display:none;">'
            + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
            + '<input type="hidden" name="month_from" value="' + $('#month_from').val() + '">'
            + '<input type="hidden" name="month_to" value="' + $('#month_to').val() + '">'
            + '<input type="hidden" name="hrm_location_id" value="' + ($('#location option:selected').val() || '') + '">'
            + '</form>');
        $('body').append($form);
        $form.submit();
        $form.remove();
    };

    $('#export_excel_btn').on('click', function(e){
        e.preventDefault();
        exportReport('excel');
    });

    $('#export_pdf_btn').on('click', function(e){
        e.preventDefault();
        exportReport('pdf');
    });

    dataLoad = function(){

        $.ajax({
            "url": "{{URL::to('/')}}/cost-to-the-company",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            "data":   {month_from: $("#month_from").val(),
                     month_to:$("#month_to").val(),
                     hrm_location_id : $("#location option:selected").val(),
                     hrm_category_id : $("#employee_category option:selected").val(),
                     filter_name_id: $('#filter_name_id option:selected').val()
                  },
            "dataType": "json",
            "success": function (response) {

                var rows = response.data || [];

                var derivedColumns = function (rows) {
                    var skip = { row_type: true, hrm_location_id: true, location_name: true };
                    var cols = [];
                    var first = rows[0] || {};
                    $.each(first, function (key) {
                        if (skip[key]) { return; }
                        var title = key.split('_').map(function (w) {
                            return w.charAt(0).toUpperCase() + w.slice(1);
                        }).join(' ');
                        cols.push({ data: key, title: title });
                    });
                    return cols;
                };

                var columnsMeta = response.columns;
                if (!columnsMeta || !columnsMeta.length) {
                    columnsMeta = derivedColumns(rows);
                }

                var $table = $('#designation_list_table');

                if ($.fn.DataTable.isDataTable('#designation_list_table')) {
                    $table.DataTable().destroy();
                }

                if (!columnsMeta.length) {
                    $table.find('thead').html('');
                    $table.find('tfoot').html('');
                    return;
                }

                var idx = {};
                var theadHtml = '<tr>';
                var tfootHtml = '<tr>';

                $.each(columnsMeta, function (i, col) {
                    theadHtml += '<th>' + col.title + '</th>';
                    tfootHtml += '<th></th>';
                    idx[col.data] = i;
                });
                theadHtml += '</tr>';
                tfootHtml += '</tr>';

                $table.find('thead').html(theadHtml);
                $table.find('tfoot').html(tfootHtml);

                var columns = $.map(columnsMeta, function (col) {
                    return { "data": col.data };
                });

                $.each(columns, function (i, col) {
                    if (col.data === 'details') {
                        col.mRender = function (data, type, full) {
                            if (full.row_type === 'location') {
                                return full.location_name;
                            }
                            return data;
                        };
                        col.className = 'nowrap';
                    }
                    if (col.data === 'no_of_employees') {
                        col.className = 'text-center nowrap';
                        col.width = '1%';
                    }
                });

                var table = $table.DataTable( {
                        "processing": true,
                        "serverSide": false,
                        "searching":  true,
                        "ordering":   false,
                        "bInfo":      true,
                        "paging":     false,
                        "scrollX": true,
                        "pageLength":100,
                        "width":"100%",
                        "autoWidth": true,
                        "scrollY":'calc(100vh - 340px)',
                        "dom": 'frtip',
                        "data": rows,
                        "createdRow": function (row, data, dataIndex) {
                            if (data.row_type === 'subtotal') {
                                $(row).addClass('group-row');
                            } else if (data.row_type === 'total') {
                                $(row).addClass('total-row');
                            }
                        },
                        "footerCallback": function ( row, data, start, end, display ) {
                            var api = this.api();
                            if (typeof idx.no_of_employees === 'undefined') { return; }
                            var locationRows = api.rows().data().toArray().filter(function(r){
                                return r.row_type === 'location';
                            });
                            var intVal = function ( i ) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '')*1 :
                                    typeof i === 'number' ?
                                        i : 0;
                            };

                            var total_employees = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.no_of_employees);
                            }, 0);

                            var total_gross = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.gross_salary);
                            }, 0);

                            var total_bonus = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.two_festival_bonus);
                            }, 0);

                            var total_pf = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.pf_contribution);
                            }, 0);

                            var total_transport = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.transport_outOfPocket);
                            }, 0);

                            var total_house = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.houseRent_allowance);
                            }, 0);

                            var total_fixed = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.fixed_allowance);
                            }, 0);

                            var total_cost = locationRows.reduce(function (a, b) {
                                return intVal(a) + intVal(b.total_cost);
                            }, 0);

                            $( api.column( idx.details ).footer() ).html('Total:');
                            $( api.column( idx.no_of_employees ).footer() ).html(total_employees);
                            $( api.column( idx.gross_salary ).footer() ).html(total_gross);
                            $( api.column( idx.two_festival_bonus ).footer() ).html(total_bonus);
                            $( api.column( idx.pf_contribution ).footer() ).html(total_pf);
                            $( api.column( idx.transport_outOfPocket ).footer() ).html(total_transport);
                            $( api.column( idx.houseRent_allowance ).footer() ).html(total_house);
                            $( api.column( idx.fixed_allowance ).footer() ).html(total_fixed);
                            $( api.column( idx.total_cost ).footer() ).html(total_cost);
                        },

                        "columns": columns
                });
            }
        });
    };
    dataLoad();
});
</script>
@endsection