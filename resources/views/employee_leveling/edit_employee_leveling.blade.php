<!-- edit_employee_leveling -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
<!-- content -->
@section('content')
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Edit Employee Leveling</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>

  <form class="form-horizontal" method="POST" action="{{action('EmployeeLevelingController@update', $master->id)}}">
    {{ csrf_field() }}
    <input name="_method" type="hidden" value="PATCH">

    <div class="box-body">
      <div class="row">

        <div class="form-group has-feedback {{ $errors->has('Level_name') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <div class="col-lg-6">
            <label>Level Name</label>
            <input type="text" class="form-control" name="Level_name" placeholder="Employee Level Name" value="{{ $master->Level_name }}" required autofocus>
            @if ($errors->has('Level_name'))
              <span class="help-block">
                <strong>{{ $errors->first('Level_name') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('salary_grade') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <div class="col-lg-6">
            <table class="table table-bordered table-hover" id="list_table" cellspacing="0" width="100%">
              <thead>
                <tr style="background: #DCDCDC;">
                  <td style="width: 10%"><input name="select_all" value="1" id="example-select-all" type="checkbox" /> All</td>
                  <td style="width: 90%">Salary Grade Name</td>
                </tr>
              </thead>
              <tbody>
                @foreach($salary_grades as $key)
                  <tr>
                    @if(in_array($key->id, $selected_ids))
                      <td><input type="checkbox" name="salary_grade[]" value="{{$key->id}}" checked></td>
                    @else
                      <td><input type="checkbox" name="salary_grade[]" value="{{$key->id}}"></td>
                    @endif
                    <td>{{$key->grade_name}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            @if ($errors->has('salary_grade'))
              <span class="help-block">
                <strong>{{ $errors->first('salary_grade') }}</strong>
              </span>
            @endif
          </div>
        </div>

      </div>
    </div>

    <div class="box-footer" style="border-top: 0px solid #f4f4f4;">
      <div class="row">
        <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <div class="col-lg-6">
            <button type="submit" class="btn btn-success block btn-flat btn pull-right">Submit</button>
          </div>
        </div>
      </div>
    </div>

  </form>
</div>
@endsection


@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript">
  $(document).ready(function($) {

    table = $('#list_table').DataTable({
      destroy:    true,
      paging:     false,
      searching:  false,
      ordering:   false,
      bInfo:      false,
    });

    $('#example-select-all').on('click', function(){
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#list_table tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
        var el = $('#example-select-all').get(0);
        if(el && el.checked && ('indeterminate' in el)){
          el.indeterminate = true;
        }
      }
    });

  });
</script>
@endsection
