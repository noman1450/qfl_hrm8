<!-- create_location -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
 
@endsection
@section('content')
<div class="row">
  <div class="col-md-6 col-lg-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Default Salary Head Setup</h3>
        <span style="color: red;">(*Secure Confiq..effect on whole system)</span>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <form  method="POST" action="{{url('setdefaultsalaryhead')}}">
        {{ csrf_field() }}
        <div class="box-body">
          <div class="row">
            <div class="has-feedback {{ $errors->has('salary_head') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
              
              @foreach($data as $data)
              <div class="col-lg-12 form-group">
                <div class="col-lg-4">
                  <label>{{$data->description}}</label>
                </div>
                <div class="col-lg-8">
                  <select class="form-control salaryhead"  name="{{$data->id}}"  required autofocus>
                    <option value="{{$data->hrm_salary_head_id}}">{{$data->salary_head}}</option>
                  </select>
                  {{-- <input type="text" name="id[]" value="{{$data->id}}" hidden> --}}
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="box-footer" style="border-top: 0px solid #f4f4f4;">
          <div class="row">
            <div class="form-group col-lg-12 col-md-12 col-xs-12">
              <div class="col-lg-6">
                <button type="submit" class="btn btn-success block btn-flat btn pull-center" >Submit</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>




  <div class="col-md-6 col-lg-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Provident Fund Confiquration</h3>
        <span style="color: red;">(*Secure Confiq..effect on whole system)</span>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>



      <div class="box-body">
        <table id="designation_list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="width: 20%">Own Contribution</th>
              <th style="width: 20%">Company Contribution</th>
              <th style="width: 20%">Start Date</th>
              <th style="width: 20%">End Date</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>




    </div>
  </div>
  


</div>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>

  $(document).ready(function($) {

      $('.salaryhead').select2({
        placeholder: 'Select a Salary Head',
        allowClear: true,
          ajax: {
              dataType: 'json',
              url: "{{URL::to('/')}}/salary_head_list",
              delay: 250,         
            data: function(params) {
                return {
                  term: params.term
                }
            },
              processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                  results: data,
                  pagination: {
                    more: (params.page * 30) < data.total_count
                  }
                };
              },
              cache: true         
          }
      });



      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/providentfundconfiqu_data",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },        
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#designation_list_table').DataTable( {
              destroy:    true,
              paging:     true,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              { "data": "amount_own" },
              { "data": "amount_company" },
              { "data": "date_from" },
              { "data": "end_date" },
            ],
            "order": [[0,'asc']]
            });
        }
      }); 

















  });


  $('#start_date').datepicker({
    autoclose: true
  });  

</script>

@endsection