
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
  }

  table.dataTable thead > tr > th {
      padding-right: 25px;
  }
  .table>tbody{
    font-size: small;
  }
  .table>thead{
    font-size: smaller;
  }

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
    <h3 class="box-title"> Employee Wise Salary Process</h3>

    <form action="{{ route('employee_wise_search') }}" id="searchForm">
      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control year onchange" id="year" name="year" style="width: 100%;" required>
                   <option value={{$running_month_year[0]->year_id}} selected>{{$running_month_year[0]->year_id}}</option>
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">

              <select  class="form-control col-lg-12 onchange" id="month_name" name="month_name" style="width: 100%;" >
                   <option value={{$running_month_year[0]->hrm_month_id}} selected>{{$running_month_year[0]->month_name}}</option>
              </select>
          </div>


          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="location" name="location" style="width: 100%;"  required>
                  <option value={{$running_month_year[0]->hrm_location_id}} selected>{{$running_month_year[0]->location_name}}</option>
              </select>
          </div>


         <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select  class="form-control col-lg-12" id="salary_master" name="salary_master" style="width: 100%;"  required>
                    {{-- <option value={{$running_month_year[0]->master_id}} selected>{{$running_month_year[0]->declaration_date}}</option> --}}
              </select>
          </div>


        </div>

      <div class="col-xs-12" style="padding-left: 0px; padding-top: 10px;">

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
               <select class="form-control" id="department" name="department" style="width: 100%;" >
              </select>
          </div>

          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
              <select class="form-control section" id="section" name="section" style="width: 100%;" >
              </select>
          </div>


          {{-- <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
             <select class="form-control category" id="category" name="category" style="width: 100%;" >
              </select>
          </div> --}}
          <div class="col-lg-3 col-md-3 col-xs-12 form-group" style="padding-left: 0px; padding-top: 10px;">
            <select class="form-control employee" id="employee" name="employee" style="width: 100%;" >
             </select>
         </div>

         <div class="col-lg-1">
            <button type="submit" class="btn btn-sm btn-success" id="search" style="margin-top: 10px" >Search</button>
         </div>

        </div>


    </form>

  </div>



</div>

<a href="" data-title="Employee Salary" data-modal-size="xl" modal-center="" footer-none="" class="modalLink btn btn-sm block btn-flat"></a>


@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>




<script>

$(document).on('click', '#search', function(e) {
    e.preventDefault();

    let form = $('#searchForm');
    let actionUrl = form.attr('action');
    let formData = form.serialize();
    let employee_id = $('#employee').val();
    let salary_master = $('#salary_master').val();
    if(!employee_id){
        alert('Please select employee');
        return;
    }
    if(!salary_master){
        alert('This month salary does not');
        return;
    }
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: formData, // 🔥 form data পাঠাতে ভুলে গিয়েছিলেন
        success: function(response) {
            console.log(response)
            if(response.success == true){
                let url = "{{ url('employee_salary_form') }}/" + employee_id +'_'+ $('#year').val() + '_' + $('#month_name').val() + '_' + $('#location').val();
                $('.modalLink').attr('href', url);

                // 🔥 Modal trigger ঠিকভাবে
                $('.modalLink').trigger('click');
            }else if(response.success == false){
                 alert(response.message);
            }

        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
});

    // function get_salary_form(){

    // }

  function format(d) {
    console.log(d);
    return '<table class="table table-bordered table-hover" cellspacing="0" width="100%">'+
          '<tr>'+
            '<td>'+"Salary Head"+'</td>'+
            '<td>'+"Status"+'</td>'+
            '<td>'+"Amount"+'</td>'+
            '<td>'+"Type"+'</td>'+
            '<td>'+"Head Amount"+'</td>'+
          '</tr>'+
          '<tr>'+
            '<td>'+d.salary_head+'</td>'+
            '<td>'+d.Status+'</td>'+
            '<td>'+d.amount+'</td>'+
            '<td>'+d.amount_type+'</td>'+
            '<td>'+d.salary_head_amount+'</td>'+
          '</tr>'+
        '</table>';
  }

  $(document).ready(function($) {

      for (i = new Date().getFullYear(); i > 2018; i--){
           $('.year').append($('<option />').val(i).html(i));
      }

      $(".onchange").change(function(){

      });

     $role= $('#location').select2({
        placeholder: 'Choose Location Mandatory',
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

      $role.on('select2:select', function (e) {

      });


      $role.on('select2:unselect', function (e) {
          $('#location').val(null).trigger("change");

      });


      $department= $('#department').select2({
        placeholder: 'Enter department',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/depertment_list_data',
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

    $department.on('select2:select', function (e) {

     });


    $department.on('select2:unselect', function (e) {
        $('#salary_master').val(null).trigger("change");

     });


    $section= $('#section').select2({
        placeholder: 'Enter Sub-department',
        allowClear: true,
        ajax: {
          dataType: 'json',
          url: '{{URL::to('/')}}/section_list_data',
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

      $employee = $('#employee').select2({
            placeholder: 'Enter Employee',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/location_wise_employeelist',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        location_id: $('#location').val()
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });






      $category=$('#category').select2({
        placeholder: 'Enter Employee Category',
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

      $category.on('select2:select', function (e) {

      });


      $category.on('select2:unselect', function (e) {
          $('#category').val(null).trigger("change");

      });

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



    $(document).on('change', '#month_name, #year, #location', function() {
        setDeclarationDate();
    })

    $(document).on('change', '#salary_master', function() {

    })




    setDeclarationDate()

    function setDeclarationDate() {
        let hrm_month_id = $('#month_name option:selected').val();
        let year_id = $("#year option:selected" ).val();
        let hrm_location_id = $('#location option:selected').val();
        let status = 1;

        $.get("{{ URL::to('/') }}/salary_master_listdata?hrm_month_id="+hrm_month_id+"&year_id="+year_id+"&hrm_location_id="+hrm_location_id+"&status="+status)
            .then((response) => {
                if (response.length == 0) {
                    $('#salary_master option').remove();
                } else {
                    $('#salary_master option').remove();

                    let options;
                    response.forEach(res => {
                        options += `<option value="${res.id}">${res.text}</option>`;
                    })

                    $('#salary_master').append(options);


                }
            })
    }
});
</script>
@endsection

