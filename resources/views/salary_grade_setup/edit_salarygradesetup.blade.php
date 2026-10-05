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
    <h3 class="box-title">Edit Salary Grade</h3>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  
  <div class="box-body">

   {!! Form::open(array('route' => array('salarygradesetup.update', $master_data[0]->id), 'onkeypress'=> "return event.keyCode != 13;", 'files'=>true, 'id' => 'top-entrypanel-validation', 'method'=>'PUT')) !!}  
    {{ csrf_field() }}

<div class="row">
 
 <div class="col-md-3">
     <div class="form-group has-feedback {{ $errors->has('salary_type') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                  <label>Salary Type</label>
                     <select class="form-control" id="salary_type" name="salary_type" required autofocus>        
                      @if ($master_data[0]->salary_type_id== 1)
                        <option value="1" selected>Basic</option>
                        <option value="0">Gross</option>
                      @else
                        <option value="1">Basic</option>
                        <option value="0" selected>Gross</option>
                      @endif
        
                     </select>


                @if ($errors->has('salary_type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('salary_type') }}</strong>
                    </span>
                @endif                  
              </div>
      </div>
 </div>
</div>

<div class="row">
   <div class="col-md-3">
      <div class="form-group has-feedback {{ $errors->has('salary_grade') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                  <label>Salary Grade</label>
                     <select class="form-control" id="salary_grade" name="salary_grade" required autofocus locked>    
                      <option value="{{$master_data[0]->grade_id}}">{{$master_data[0]->grade_name}}</option>              
                     </select>

                    @if ($errors->has('salary_grade'))
                        <span class="help-block">
                            <strong>{{ $errors->first('salary_grade') }}</strong>
                        </span>
                    @endif                  
                  </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group has-feedback {{ $errors->has('salary_size') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">  
              <div class="col-lg-12 col-md-12 col-xs-12">  
                  <label>Salary Size(Amount)</label>

                     <input  class="form-control" type="text" name="salary_size" placeholder="Salary Amount" value="{{$master_data[0]->salary_size}}" required>

                @if ($errors->has('salary_size'))
                    <span class="help-block">
                        <strong>{{ $errors->first('salary_size') }}</strong>
                    </span>
                @endif                  
              </div>
      </div>
     </div>

     <input type="text" name="id" value="{{$master_data[0]->id}}" hidden>
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

        @foreach ($edit_data as $value)
              <tr>
                <td>{{$value->group_name}}</td>
                <td>{{$value->salary_head}}</td>
                <td>
                  <input   type="text"  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;"  placeholder="amount"    id="amountaddition"    name="amountaddition[]"  value="{{ $value->amount }}"></td>
                <td>

                 <select  id="typeaddition"    style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;"          name="typeaddition[]">
                    @if ($value->amount_type == 1){
           
                      <option value="2">Tk</option>
                      <option value="1" selected>%</option>                   
                    }@else
                      <option value="2" selected>Tk</option>
                      <option value="1">%</option>
                      @endif
                  </select>

                  <input  style="width:100%;" type="hidden"       id="addition_id"     name="addition_id[]"  value="{{ $value->salary_head_id }}">
                </td>
              </tr>
            @endforeach


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
                    @foreach ($edit_data_deduction as $value)
              <tr>
                <td>{{$value->group_name}}</td>
                <td>{{$value->salary_head}}</td>
                <td>
                  <input  type="text"  style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;" placeholder="amount"    id="amountdeduction"    name="amountdeduction[]"  value="{{ $value->amount }}"></td>
                <td>

                 <select  id="typededuction"     style="width:100%; height: 27px; padding-bottom: 0px; padding-top: 0px; text-align: center;"         name="typededuction[]">
                    @if ($value->amount_type == 1){
           
                      <option value="2">Tk</option>
                      <option value="1" selected>%</option>                   
                    }@else
                      <option value="2" selected>Tk</option>
                      <option value="1">%</option>
                      @endif
                  </select>

                  <input  style="width:100%;" type="hidden"       id="deduction_id"     name="deduction_id[]"  value="{{ $value->salary_head_id }}">
                </td>
              </tr>
            @endforeach
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


    
{!! Form::close() !!} 

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


@endsection