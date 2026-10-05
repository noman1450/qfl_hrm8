@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Create Salary Deduction</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form  method="POST" action="{{ url('salary_deduction_update', $salary_deduction->id)}}">
        @csrf
        @method('patch')

        <div class="row">
            <div class="form-group col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Month</label>
                    <input type="text" class="form-control"  placeholder="Month From" name="month_from" id="date_from" value="{{ date('M-Y', strtotime($salary_deduction->month_from)) }}" disabled>
                </div>
            </div>

            <div class="form-group col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Purpose</label>
                    <input type="text" class="form-control"  placeholder="Purpose" name="purpose" id="purpose" value="{{ $salary_deduction->purpose }}"  >
                </div>
            </div>
        </div>

        <div class="row" >
            <div class="form-group has-feedback col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Amount</label>
                    <input type="text" class="form-control" placeholder="amount" name="amount" id="amount" value="{{ $salary_deduction->amount }}">
                </div>
            </div>

            <div class="form-group has-feedback col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Employee</label>
                    <select class="form-control" style="width: 100%;" disabled>
                        <option value="{{ $salary_deduction->hrm_employee_id }}">{{ $salary_deduction->employee_name }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group has-feedback col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Adjust With Salary Head</label>
                    <select class="form-control" id="salary_head" name="salary_head" style="width: 100%;">
                        <option value="{{ $salary_deduction->hrm_salary_head_id }}">{{ $salary_deduction->salary_head }}</option>
                    </select>
                </div>
            </div>
<!-- 
            <div class="form-group col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Process</label>
                    <select class="form-control" id="is_active" name="is_active" style="width: 100%;">
                        <option value="1" {{ $salary_deduction->is_active == 1 ? 'selected' : null }}>Pending</option>
                        <option value="2" {{ $salary_deduction->is_active == 2 ? 'selected' : null }}>Process</option>
                    </select>
                </div>
            </div> -->
        </div>

        <div class="box-body">
            <div class="row">
                <input type="submit" value="Submit" class="btn-sm btn-success block btn-flat btn" style="margin-left: 15px; padding: 7px 10px; color: black; border:1px solid gray;">
            </div>
        </div>
    </form>
</div>
@endsection


@section('script')
  <script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>


<script>

$(document).ready(function($) {

    // $('#date_from').datepicker({
    //     autoclose: true,
    //     minViewMode: 1,
    //     format: 'M-yyyy'
    // });

    // $('#date_to').datepicker({
    //     autoclose: true,
    //     minViewMode: 1,
    //     format: 'M-yyyy'
    // });

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

</script>

@endsection
