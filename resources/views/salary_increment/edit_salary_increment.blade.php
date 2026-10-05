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
    <h3 class="box-title">Edit Salary Increment & Promotion</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

      <form  method="POST" action="{{url('salaryincrement')}}">
            {{ csrf_field() }}

    <div class="row">

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Effective Month</label>
                <input type="text" class="form-control"  placeholder="Month From" name="date_from" id="date_from" value="{{  date('d-m-Y', strtotime(str_replace('-', '/', $edit_data[0]->effective_date ))) }}"  readonly required>
              </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Note</label>
                <input type="text" class="form-control"  placeholder="Note" name="note" id="note" value="{{$edit_data[0]->note}}"  readonly>
            </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Apply for</label>
                  <select class="form-control" name="apply_for" id="apply_for">
                  <option value="{{$edit_data[0]->apply_for_id}}">{{$edit_data[0]->apply_for}}</option>
                  </select>
            </div>
            </div>

    </div>

    <div class="row" >

            @php
                $increase_value = $edit_data[0]->amount_type == 2
                ? $edit_data[0]->increase
                : (($edit_data[0]->increase * 100) / $edit_data[0]->salary_amount);
            @endphp
            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Amount</label>
                <input type="text" class="form-control" placeholder="amount" name="amount" id="amount"
                value="{{ $edit_data[0]->increment_amount > 0 ? $edit_data[0]->increment_amount : round($increase_value) }}"  readonly>
              </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('amount_type') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Amount Type</label>
                <select  class="form-control" name="amount_type" id="amount_type" readonly>
                  @if($edit_data[0]->amount_type==1)
                  <option value="1" selected>%</option>
                  <option value="2">Tk.</option>
                  @else
                  <option value="1" >%</option>
                  <option value="2"selected>Tk.</option>
                 @endif

                </select>


              </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Location</label>
                <select class="form-control" name="location" id="location">
                  <option value="{{$edit_data[0]->location_id}}">{{$edit_data[0]->location_name}}</option>
                </select>
              </div>
            </div>



    </div>



  <div class="row" style="margin-left:10px; ">


      </div>




      <div class="box-body">
        <div class="row">
          <div class="form-group col-lg-12 col-md-12 col-xs-12">
            <table id="list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width: 20%">Employee Name</th>
                  <th style="width: 10%">Joining Date</th>
                  <th style="width: 10%">Deparment</th>
                  <th style="width: 10%">Designation</th>
                  <th style="width: 10%">Salary</th>
                  <th style="width: 10%">Increment</th>
                  <th style="width: 10%">New Salary</th>
                  <th style="width: 10%">Grade Name</th>
                  <th style="width: 10%">Last Increment</th>
                  <th style="width: 5%">Update</th>
                  <th style="width: 5%">Delete</th>
                </tr>
              </thead>
              <tbody>
                  @foreach ($edit_data as $keys)
                    <tr>
                      <td>{{$keys->employee_name}}</td>
                      <td>{{$keys->joining_date}}</td>
                      <td>{{$keys->depertment_name}}</td>
                      <td>
                         <select class="form-control designation_name" style="width: 100%;" name="hrm_designation_id[]" >
                          <option value="{{$keys->hrm_designation_id}}">{{$keys->designation_name}}</option>
                        </select>
                      </td>
                      <td>{{$keys->salary_amount}}</td>
                      <td>
                        <input type="hidden" step="any" class="salary"   value="{{ $keys->salary_amount }}">
                        <input type="number" step="any" class="increment"  name="increment[]"  value="{{ $keys->increase_amount }}">
                      </td>

                      <td >
                          <input type="text" onkeypress="return isNumberKey(event)" class="new_salary_amount" name="new_salary_amount[]" readonly value="{{$keys->new_salary_amount}}" >
                          <input type="hidden" name="details_id[]" id="details_id"  value="{{$keys->details_id}}" >
                      </td>

                      <td>
                         <select class="form-control grade_name" style="width: 100%;" name="hrm_salary_grade_id[]" >
                          <option value="{{$keys->hrm_salary_grade_id}}">{{$keys->grade_name}}</option>
                        </select>
                      </td>

                      <td>
                        <p class="last_increment_note" > <b> {{$keys->last_increment_note}}</b></p>
                      </td>

                      <td>
                        <button type="button"  class="btn btn-info btn-block update-button btn-flat" data-id="{{$keys->details_id}}" data-amount="{{$keys->new_salary_amount}}" >Update</button>

                      </td>


                       <td>
                        <button type="button"  class="btn btn-danger btn-block delete-button btn-flat" id="{{$keys->details_id}}" >Delete</button>
                       </td>

                    </tr>
                  @endforeach
              </tbody>
            </table>
          </div>


         <!-- <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;" >  -->

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

     $('#date_from').datepicker({
          autoclose: true,
          minViewMode: 1,
          format: 'dd-mm-yyyy'
     });

     $(document).on('input', '.increment', function () {
        let $input = $(this);
        let $tr = $input.closest('tr');
        let increment = $input.val();
        let salary = $tr.find('.salary').val();
        let new_salary = Number(salary) + Number(increment);
        $tr.find('.new_salary_amount').val(new_salary)

    });

    $('.grade_name').select2({
        placeholder: 'Enter Grade',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{ URL::to('/') }}/salarygrade_list?status=1",
            delay: 250,
            data: function(params) {
                return {
                    term: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });


    $('.designation_name').select2({
      placeholder: 'Enter designation',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/designation_list_data',
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


</script>


  <script>

 sales_order_table= $("#list_table").DataTable({
            "searching": true,
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
            url: '{{URL::to('/')}}/salaryincrementdetails/'+$(this).attr('id')+'/delete',
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


        var amount = $(this).parents('tr').find('.new_salary_amount').val();
        var hrm_salary_grade_id = $(this).parents('tr').find('.grade_name').val();
        var hrm_designation_id = $(this).parents('tr').find('.designation_name').val();


        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/salaryincrementdetails/update/'+$(this).data('id') ,
            dataType: 'json',
            data: {
                hrm_salary_grade_id: hrm_salary_grade_id,
                amount: amount,
                hrm_designation_id: hrm_designation_id
            },
            success: function(data) {
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






    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
            return false;
        return true;
    }

</script>
@endsection
