
@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style>
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
    }
    table.dataTable thead > tr > th {
      padding-right: 25px;
    }
    .table>tbody{
      font-size: small;
    }
    /* .table>thead{
      font-size: smaller;
    } */
    td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
      cursor: pointer;
    }
    tr.shown td.details-control {
      background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
    }
</style>
@endsection

@section('content')
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Acc Head Wise Employee List</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <div id="alert-danger"></div>
          </div>
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <div id="alert-success"></div>
          </div>

      {!! Form::open(array('route'=>'acc_processed_data.store', 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id'=>'frm_process')) !!}


            <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">


               <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                  <select  class="form-control col-lg-12 year onchange" id="year" name="year" style="width: 100%;"  >
                    <option value="{{$cyear}}">{{$cyear}}</option>
                  </select>
                </div>



                <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                  <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;"  >
                    <option value="{{$cmonth->id}}">{{$cmonth->month_name}}</option>
                  </select>
                </div>



                <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                  <select  class="form-control col-lg-12 onchange" id="hrm_acc_journal_type_id" name="hrm_acc_journal_type_id" style="width: 100%;"  required>
                  </select>
                </div>


                <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                  <select  class="form-control col-lg-12 onchange" id="location" name="location" style="width: 100%;"  required>
                           <!-- <option value="999">-All Depot-</option> -->
                            <option value="0">-ALL-</option>
                            <option value="999">-Only Depot-</option>
                            @foreach ($location as $keys)
                            <option value={{$keys->id}}>{{$keys->location_name}}</option>
                            @endforeach


                  </select>
                </div>

                <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                    <input type="radio" id="reprocess" name="submit_action" value="reprocess" checked>
                    <label for="reprocess">Reprocess</label><br>
                    <input type="radio" id="locked" name="submit_action" value="locked">
                    <label for="locked">Locked & Transfer To A/C</label><br>
                </div>


                <div class="col-lg-2 col-md-2 col-xs-12 form-group">
                    <input type="submit" class="btn btn-primary" id="submitbtn" value="Re-process">
                </div>

            </div>

        {!! Form::close() !!}






    </div>


    <div class="box-body">
        <div class="row">


            <div class="form-group col-md-10" style="overflow: auto;">
                <table id="headWiseEmpDatatable" class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Year-Month</th>
                            <th>Journal Type</th>
                            <th>Head Name</th>
                            <th>Status</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
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
                        </tr>
                      </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {


        $(document).on('click', '#reprocess', function() {
            $('#submitbtn').val('Re-process')
            $('#submitbtn').addClass('btn-primary').removeClass('btn-warning')
        })


        $(document).on('click', '#locked', function() {
            $('#submitbtn').val('Locked & Transfer To A/C')
            $('#submitbtn').addClass('btn-warning').removeClass('btn-primary')
        })




        for (i = new Date().getFullYear(); i >= 2018; i--){
            $('.year').append($('<option />').val(i).html(i));
        }



        $('#month_name').select2({
            placeholder: 'Enter Month  Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/monthlist',
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

        $(".onchange").change(function() {
            dataLoad();
        });


        // $location= $('#location').select2({
        //     placeholder: 'Choose Location',
        //     allowClear: true,
        //     ajax: {
        //         dataType: 'json',
        //         url: '{{URL::to('/')}}/location_list_data',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 term: params.term
        //             }
        //         },
        //         processResults: function (data, params) {
        //             params.page = params.page || 1;
        //             return {
        //                 results: data
        //             };
        //         },
        //         cache: true
        //     }
        // });

        // $location.on('select2:select', function (e) {
        //     dataLoad();
        // });

        // $location.on('select2:unselect', function (e) {
        //     $('#location').val(null).trigger("change");
        //     dataLoad();
        // });

        $("#hrm_acc_journal_type_id").select2({
            placeholder: "Search Journal Type",
            width: '100%',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ route('account_journal_type.dropdown') }}",
                delay: 100,
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
            },
        });




        function format(d) {
            console.log(d);
            return `
                <table class="table table-bordered table-hover" cellspacing="0" width="100%">
                    <tr>
                        <td>Location Name</td>
                        <td>Category Name</td>
                        <td>Salary Head</td>
                        <td>Debit Amount</td>
                        <td>Credit Amount</td>
                    </tr>
                    <tr>
                        <td>${d.location_name}</td>
                        <td>${d.category_name}</td>
                        <td>${d.salary_head}</td>
                        <td>${d.debit_amount}</td>
                        <td>${d.credit_amount}</td>


                    </tr>
                </table>
            `
        }

        function dataLoad() {
            table = $('#headWiseEmpDatatable').DataTable({
                "destroy":    true,
                "processing": true,
                "serverSide": true,
                "searching":  true,
                "ordering":   true,
                "bInfo":      true,
                "paging":     false,
                "aoColumnDefs": [ { "bVisible": false, "aTargets": [0] } ],

                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    debit = api
                        .column(6)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                    credit = api
                        .column(7)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);


                    $( api.column( 5 ).footer() ).html('Total:');
                    $( api.column( 6 ).footer() ).html(debit);
                    $( api.column( 7 ).footer() ).html(credit);
                },


                ajax: {
                    url: "{{ route('acc_processed_data.index') }}",
                    type: "GET",
                    data: function (query) {
                        query.hrm_location_id = $("#location").val()
                        query.year_id = $("#year").val()
                        query.hrm_month_id = $("#month_name").val()
                        query.hrm_acc_journal_type_id = $("#hrm_acc_journal_type_id").val()
                    }
                },
                columns: [
                    { "data": "id" },
                    {
                        "className":      'details-control',
                        "orderable":      true,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "year_month" },
                    { "data": "journal_type_name" },
                    { "data": "head_name" },
                    { "data": "status" },
                    { "data": "debit_total" },
                    { "data": "credit_total" },
                    // { "data": "Link", orderable: false, searchable: false },
                ],

                "order": [[0, 'desc']]
            });
        }

        dataLoad();

        $('#headWiseEmpDatatable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        });

        confirmationWithAjaxReload({
            selector: '.deleteHeadWiseEmployeeSetup',
            refreshTable: dataLoad
        })




    $( "#frm_process" ).submit(function(event){
        event.preventDefault();

        if(confirm('Do you want to submit?')){

          $("#submitbtn").attr("disabled", true);
          $("#submitbtn").val('Please wait..');


          var $form   = $( this ),
          url         = $form.attr( "action" );
          token       = $("[name='_token']").val();
          $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : url, // the url where we want to POST
            data        : $form.serialize(),
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true,
            _token      : token
          })
          .done(function(data) {
              console.log(data);
              if(data['success']) {
                var erreurs ='<div class="alert alert-success"><ul>';

                    erreurs += '<li>'+data.messages+'</li>';
                    erreurs += '</ul></div>';
                $('#alert-success').html(erreurs);
                $('#alert-success').show(0).delay(4000).hide(0);

                $('#submitbtn').attr("disabled", false);
                $("#submitbtn").val('Submit');

                dataLoad();

              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                  erreurs += '<li>'+data.messages+'</li>';
                  erreurs += '</ul></div>';
                  $('#alert-danger').html(erreurs);
                  $('#alert-danger').show(0).delay(4000).hide(0);

                  $('#submitbtn').attr("disabled", false);
                  $("#submitbtn").val('Submit');

                  dataLoad();
              }
          });
        }else{
          return;
        }
    });










    });
</script>
@endsection
