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


    <form  method="POST" action="{{ url('salary_deduction_post')}}">
        @csrf

        <div class="row">
            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Month From</label>
                    <input type="text" class="form-control"  placeholder="Month From" name="month_from" id="date_from" value=""  readonly required>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Month To</label>
                    <input type="text" class="form-control" placeholder="Month To" name="month_to" id="date_to" readonly required>
                </div>
            </div>

        </div>

        <div class="row" >
            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Purpose</label>
                    <input type="text" class="form-control"  placeholder="Purpose" name="purpose" id="purpose" value=""  >
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Amount</label>
                    <input type="text" class="form-control" placeholder="amount" name="amount" id="amount"  >
                </div>
            </div>
        </div>

        <div class="row">

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Employee</label>
                    <select class="form-control" id="employee_name" name="employee_name" style="width: 100%;" >
                    </select>
                </div>
            </div>

            <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-3 col-md-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <label>Adjust With Salary Head</label>
                    <select class="form-control" id="salary_head" name="salary_head" style="width: 100%;" >
                    </select>
                </div>
            </div>
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

    $('#date_from').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'M-yyyy'
    });

    $('#date_to').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'M-yyyy'
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

    $('#employee_name').select2({
      placeholder: 'Enter Employee Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/join_employee_list',
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
