<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')

<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">




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

  <div >
    <div id="massages"></div>
  </div>
  
  
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Shift Role Wise Employee List</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>



      <div class="box-body">
        <div class="row">
          {!! Form::open(array('url' => 'delete_all', 'id' => 'frm-delete-all-employee')) !!}
          {{ csrf_field() }} 

              <div class="col-lg-6 col-md-6 col-xs-12 form-group">  
                  <select class="form-control" id="shiftrole" name="shiftrole" style="width: 100%;"  required>
                  </select>                 
              </div>

<!--               <div class="col-lg-6 col-md-6 col-xs-12 form-group">
                <input type="button" id="search" value="Search" class=" btn-sm block btn-flat btn" style="margin-right: 15px; padding: 7px 10px;background-color: #EEEEEE; color: black; border:1px solid gray;">                  
              </div>            
 -->
              <div class="col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">   
                <table id="list_table" class=" cell-border table table-bordered table-hover "  cellspacing="0" width="100%">
                  <thead>
                    <tr>

                     <th  style="width: 5%"><input name="select_all" value="1" id="example-select-all" type="checkbox" />_All</th>
                      <th style="width: 20%">Employee Name</th>
                      <th style="width: 10%">Emp. Code</th>
                      <th style="width: 15%">Department</th>
                      <th style="width: 15%">Designation</th>
                      <th style="width: 15%">Shift Name</th>
                      <th style="width: 10%">Shift Time</th>
                      <th style="width: 10%">Current Role</th>
                      <th style="width: 10%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>

              <div class="col-lg-12 col-md-12 col-xs-12">    
                <input type="submit"  value="Submit" class=" btn-sm btn-success block btn-flat btn"> 
              </div>

          {!! Form::close() !!}
        </div>
      </div>

  </div>
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

    function isBlank(str) {
      return (!str || /^\s*$/.test(str));
    }

    $role = $('#shiftrole').select2({
      placeholder: 'Enter a Shift Role Name',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/shiftrole_list_data',
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
      dataLoad();
    });

    $role.on('select2:unselect', function (e) {
        $('#shiftrole').val(null).trigger("change");
        dataLoad();
    });


 
   
    dataLoad = function(){



      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/shiftrolewise_employeelist",
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }, 
        data:   {
                  shiftrole: $("#shiftrole").val(),      
                },         
        dataType: 'json',
        success: function(data) {
            var dataSet = data.data;
            table = $('#list_table').DataTable( {
              destroy:    true,
              paging:     false,
              searching:  true,
              ordering:   true,
              bInfo:      true,  
              "data":     dataSet,

            "columns": [
              { "data": "checkbox",
                "mRender": function (data, type, full) {
                return '<input type="checkbox" name="id[]" value="'+full.id+'">';
              }
              },
              { "data": "employee_name" },
              { "data": "card_code" },
              { "data": "depertment_name" },
              { "data": "designation_name" },
              { "data": "shift_name" },
              { "data": "shift_time" },
              { "data": "shift_role_name" },
              { "data": "Link",
                "mRender": function (data, type, full) {
                  return '<button type="button" class="btn btn-danger btn-block delete-button btn-flat" id="'+full.id+'" >Delete</button>';
                  // return '<a href="{{URL::to('/')}}/shiftroleemployee/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to Remove?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Remove</a>';
                }
              },
              // { "data": "Link",
              //   "mRender": function (data, type, full) {
              //     return '<a href="{{URL::to('/')}}/shiftroleemployee/'+full.id+'/cancel"  onclick="return confirm(\'Do you really want to Remove?\');" class="btn btn-danger btn-sm btn-flat"><span class="glyphicon glyphicon-trash">Remove</a>';
              //   }
              // },
            ],
            "order": [[0,'asc']]
            });
        }
      })
    }

 dataLoad();

    // });


  // $('#delete').on('click', function() {
  //     var selectedRows = table.rows( $('#list_table tr.active') ).data().to$();
  //     alert("NOMAN");
  //     // $.ajax({
  //     //     // url: url_to_delete_rows,
  //     //     url: '{{URL::to('/')}}/url_to_delete_rows',
  //     //     method: 'POST',
  //     //     data: { rows: selectedRows.toArray() },
  //     //     dataType: 'json',
  //     //     success: function( data, status, xhr ) {
  //     //         table.rows( $('#list_table tr.active') ).remove().draw(false);
  //     //     }
  //     // });
  // });

    $('#list_table tbody').on('click','.delete-button',function(){
        var delete_object = $(this).parents('tr');
        $.ajax({
            method: 'GET',
            url: '{{URL::to('/')}}/shiftroleemployee/'+$(this).attr('id')+'/cancel',
            dataType: 'json',
            success: function(data) {
              console.log(data.massages);
              if(data.massages == true){
                table.row( delete_object ).remove().draw();
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

    $('#list_table tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
         }
      }
    });

    $('#example-select-all').on('click', function(){
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });



});

</script>

@endsection