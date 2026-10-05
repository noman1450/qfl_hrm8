<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
<!-- content -->
@section('content')

  <div >
    <div id="massages"></div>
  </div>


<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Edit Salary Advance Adjust</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>


      <form  method="POST" action="{{url('salaryextrafeaturesupdate')}}">
            {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="id" id="id"  value="{{$master_data[0]->id}}">

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                <label>Month From</label>
                <input type="text" class="form-control"  placeholder="Month From" name="date_from" id="date_from"  data-date-format="dd-mm-yyyy"  value="{{  date('M-Y', strtotime(str_replace('-', '/', $master_data[0]->month_from ))) }}"  readonly required>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                <label>Month To</label>
                <input type="text" class="form-control" placeholder="Month To" name="date_to" id="date_to" data-date-format="dd-mm-yyyy"  value="{{  date('M-Y', strtotime(str_replace('-', '/', $master_data[0]->month_to ))) }}"   readonly required>
            </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                  <label>Purpose</label>
                  <input type="text" class="form-control"   placeholder="Purpose" name="purpose" id="purpose" value="{{$master_data[0]->purpose}}"  >
              </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                  <label>Adjust With Salary Head</label>
                       <select class="form-control" id="salary_head" name="salary_head" style="width: 100%;" >

                        <option value="{{$master_data[0]->salary_head_id}}" >{{$master_data[0]->salary_head}}</option>
                       </select>
              </div>

              <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-2 col-md-12 col-xs-12">
                  <label>Location Name</label>

                    <select class="form-control" id="location" name="location" style="width: 100%;" >
                         <option value={{$master_data[0]->hrm_location_id }}> {{$master_data[0]->location_name}}</option>
                    </select>
              </div>
        </div>

    </div>

      <div class="box-body">
        <div class="row">
           <div class="col-lg-12 col-md-12 col-xs-12" style="margin-top: 10px;">
                  <div class="col-lg-12">
                  <table id="list_table" class="table table-bordered table-hover">


                    <thead>
                        <tr>
                          <th style="width:30%;">Employee Name</th>
                          <th style="width:05%;">Deparment</th>
                          <th style="width:15%;">Designation</th>
                          <th style="width:15%;">Gross Salary(Tk.)</th>
                          <th style="width:15%;">Amount(Tk.)</th>
                          {{-- <th style="width:15%;">Update</th> --}}
                          <th style="width:10%">Delete</th>
                        </tr>
                    </thead>

                      <tbody>
                        @foreach ($edit_data as $keys)
                          <tr>
                            <td>{{$keys->employee_name}}</td>
                            <td>{{$keys->depertment_name}}</td>
                            <td>{{$keys->designation_name}}</td>
                            <td>{{$keys->basic_salary}}</td>
                            <td >
                                <input type="text" onkeypress="return isNumberKey(event)" name="amount[]" id="amount"   value="{{$keys->amount}}" step="any">
                                <input type="hidden" name="details_id[]" id="details_id"  value="{{$keys->details_id}}" >
                                <input type="hidden" name="hrm_employee_job_info_id[]" id="hrm_employee_job_info_id"  value="{{$keys->hrm_employee_job_info_id}}" >
                            </td>
                              {{--<td>
                                <a id="editamount" name="editamount" href="{{URL::to('/')}}/extradetails/update/{{$keys->details_id}}/{{$keys->amount}}"   class="btn btn-info btn-sm btn-flat">
                                <span class="glyphicon glyphicon-edit"> </span> Update</a>
                              </td> --}}
                             <td>
                              <button type="button"  class="btn btn-danger btn-block delete-button btn-flat" id="{{$keys->details_id}}" >Delete</button>

                             </td>

                          </tr>
                        @endforeach
                      </tbody>
                  </table>
              </div>
            </div>

         <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">

        </div>
      </div>

    </form>


</div>

  @endsection


  <!-- script -->
  @section('script')
  <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>

<script>

    $(document).ready(function($) {

       sales_order_table= $("#list_table").DataTable({
            "searching": false,
            "paging": false,
            "ordering": false,
            "autoWidth": false,
            "bInfo": true

        });


      $('#list_table tbody').on( 'keyup', 'tr', function () {

          rate        = $(this).find('td:eq(3)').find('input').val();
          // sales_order_table.cell($(this),4).data(rate);


        });


      $('#list_table tbody').on( 'focusout', 'tr', function () {
          $('#sales_order_table').dataTable().api().rows().invalidate().draw();
        });



    // $('#noman').click(function(event){
    //   event.preventDefault();
    //     console.log("NOMAN");
    //  });


    $('#list_table tbody').on('click','.delete-button',function(){


        var delete_object = $(this).parents('tr');
        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/salaryextradetails/'+$(this).attr('id')+'/delete',
            dataType: 'json',
            success: function(data) {
              console.log(data.massages);
              if(data.massages == true){
                sales_order_table.row( delete_object ).remove().draw();
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>Successfully deleted</li>';
                    erreurs += '</ul></div>';
                $('#massages').html(erreurs);
                $('#massages').show(0).delay(500).hide(0);
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                      erreurs += '<li>Invalid Request,Please Check!!</li>';
                      erreurs += '</ul></div>';
                  $('#massages').html(erreurs);
                  $('#massages').show(0).delay(500).hide(0);
              }
            }
        });
    })

    $('#list_table tbody').on('click','.update-button',function(){

      console.log($(this).attr('id'));

        var delete_object = $(this).parents('tr');
        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/extradetails/update/'+$(this).attr('id')/'',
            dataType: 'json',
            success: function(data) {
              console.log(data.massages);
              if(data.massages == true){
                // sales_order_table.row( delete_object ).remove().draw();
                var erreurs ='<div class="alert alert-success"><ul>';
                    erreurs += '<li>Successfully updated</li>';
                    erreurs += '</ul></div>';
                $('#massages').html(erreurs);
                $('#massages').show(0).delay(500).hide(0);
              }else{
                  var erreurs ='<div class="alert alert-danger"><ul>';
                      erreurs += '<li>Invalid Request,Please Check!!</li>';
                      erreurs += '</ul></div>';
                  $('#massages').html(erreurs);
                  $('#massages').show(0).delay(500).hide(0);
              }
            }
        });
    })


    });

</script>

<script>

    $(document).ready(function($) {

      $('#date_from').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months"
     });

    $('#date_to').datepicker({
        autoclose: true,
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });


         $('#salary_head').select2({
              placeholder: 'Enter a Salary Head',
              allowClear: true,
              ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/salary_head_list',
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



    });

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
            return false;
        return true;
    }

</script>

@endsection
