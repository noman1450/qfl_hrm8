<!-- location_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection
<!-- content -->
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Designation Wise Fringe Benefit Create</h3>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">

 <form  method="POST" action="{{url('otherfacilityconfiqsubmit')}}">
        {{ csrf_field() }}


      <div class="row">

      <div class="col-md-5">
            <div class="form-group has-feedback {{ $errors->has('designation') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
                    <div class="col-lg-12 col-md-12 col-xs-12">  
                        <label>Designation</label>
                           <select class="form-control" id="designation" name="designation" required autofocus>                   
                           </select>

                      @if ($errors->has('designation'))
                          <span class="help-block">
                              <strong>{{ $errors->first('designation') }}</strong>
                          </span>
                      @endif                  
                    </div>
            </div>

      </div>


      </div>
    


    <div class="row">
      <div class="form-group col-lg-6 col-md-6 col-xs-12">
        <label class="form-control" style="color: black; background-color: gray">Addition List</label>
        <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <!-- <th style="width: 0%" hidden></th> -->
              <th style="width: 30%">Group Name</th>
              <th style="width: 30%">Salary Head</th>
              <th style="width: 20%">Amount</th>
              <th style="width: 20%">Type</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <div class="form-group col-lg-6 col-md-6 col-xs-12">
        <label class="form-control" style="color: black; background-color: gray">Deduction List</label>

        <table id="list_table2" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <!-- <th style="width: 0%" hidden></th> -->
              <th style="width: 30%">Group Name</th>
              <th style="width: 30%">Salary Head</th>
              <th style="width: 20%">Amount</th>
              <th style="width: 20%">Type</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>



    <div class="form-group">
        <label class="col-md-3 control-label"></label>
        <div class="col-md-8">
          <input type="submit" class="btn btn-success block btn-flat " style="width: 170px; margin-left:640px; " value="Submit">
          <span></span>
          <!-- <input type="reset" class="btn block btn-flat btn-default" value="Cancel"> -->
        </div>
      </div>


    

</form>

  </div>
</div>

  @endsection

  
  <!-- script -->
  @section('script')
  <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>


  <script>

$(document).ready(function($) {


   $('#designation').select2({
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




      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/salaryhead_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  generate_type: 1,                
                  apply_for: 2,                  
                },        
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   false,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              // { "data": "id" },
              { "data": "group_name" },
              { "data": "salary_head" },
              { "data": "text",
                  "mRender": function (data, type, full) {
                  return '<input type="text"  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"   placeholder="Input Amount"  name="amountaddition[]" >'+
                         '<input type="hidden"  name="addition_id[]" value="'+full.id+'">';
                }
              },
              { "data": "option",
                  "mRender": function (data, type, full) {
                  return '<select   name="typeaddition[]"   style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"> <option value="1">%</option> <option value="2">TK</option> </select>  ';
                }
              }

            ],
            "order": [[0,'asc']]
            });
        }
      }); 



 $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/salaryhead_list",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        data:   {
                  generate_type: 2,
                  apply_for: 2,                  
                },         
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table2').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  false,
              ordering:   false,
              bInfo:      false,  
              "data":     dataSet,

            "columns": [
              { "data": "group_name" },
              { "data": "salary_head" },
                              
                    { "data": "text",
                      "mRender": function (data, type, full) {
                      return '<input type="text"  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"  placeholder="Input Amount" name="amountdeduction[]" >'+
                             '<input type="hidden"  name="deduction_id[]" value="'+full.id+'">';
                    }
                  },
                    { "data": "option",
                      "mRender": function (data, type, full) {
                      return '<select name="typededuction[]"  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px;"> <option value="1">%</option> <option value="2">TK</option> </select>  ';
                    }
                  }
                    

            ],
            "order": [[0,'asc']]
            });
        }
      }); 

});

</script>

@endsection