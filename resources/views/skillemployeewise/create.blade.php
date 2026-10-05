
@extends('layouts.main')
@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

  <style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, 
  .table>tfoot>tr>td, .table>tfoot>tr>th, 
  .table>thead>tr>td, .table>thead>tr>th{
    padding: 5px;
  } 
  </style>
@endsection

@section('content')
<div class="box box-default">
  
  <div class="box-header with-border" style="position: inherit;">
    <h3 id="page-header" class="box-title">Add Skill To Employee</h3>
     <div class="box-tools pull-right">
          <a type="button" href="{{ URL::previous() }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
  </div>

  <form method="POST" action="{{ route('skillemployeewise.store') }}" onkeypress = "return event.keyCode != 13;" id="skillemployeewise_form" enctype="multipart/form-data" file="true">

    @php $form_type ='create' @endphp
    @include('skillemployeewise/_form')

  </form>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>


<script>
		$('#skillemployeewise_form').on('submit',(function(e) {
			e.preventDefault();

			$("#btnSubmit").attr("disabled", true);
			$("#btnSubmit").val('Please wait..');
			
			var formData = new FormData(this);

			$.ajax({
				type:'POST',
				url: $(this).attr('action'),
				data:formData,
				cache:false,
				contentType: false,
				processData: false,
				success:function(data){


					if(data.success == true) {
						$('#btnSubmit').attr("disabled", false);
						$("#btnSubmit").val('Submit');
						alert(data.message)
            window.location.replace("{{ URL::to('skillemployeewise')}}");

					}else{
						$('#btnSubmit').attr("disabled", false);
						$("#btnSubmit").val('Submit');
						alert(data.message)
            window.location.replace("{{ URL::to('skillemployeewise')}}");
					}
				}
			});
		}));
	</script>

  <script>
    $(document).ready(function($) {
      $employee = $('#employee_name').select2({
        placeholder: 'Enter an Employee Name',
        allowClear: true,
          ajax: {
              dataType: 'json',
              url: "{{URL::to('/')}}/join_employee_list",
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

      $employee.on("select2:select", function (e) {
        $("#employee_id").val($(this).select2('data')['0']['id']);
      });
    });
  </script>
  <script>
    $(document).ready(function($) {

      var datac = {!! json_encode($datac) !!};
      num = {!! json_encode($num) !!};
      console.log(datac);
      $employee.on("select2:select", function (e) {
        let datac_s = [
          {
            skill_id: 0,
            employee_id: 0,
            description: ""
          } 
        ];
        employee_id_f = $(this).select2('data')['0']['id'];
        j=0;
        for(i=0; i<datac.length; i++){
          if (datac[i].id == employee_id_f){
            datac_s.push({});
            datac_s[j].skill_id = datac[i].skill_id;
            datac_s[j].employee_id = datac[i].id;
            datac_s[j].description = datac[i].description;
            console.log(datac_s[j]);
            j++;
          }else{

          }
        }
        // console.log(datac_s, datac);
        var hay = [];
        k=0;
        table.clear();
        for (i=0; i<num.length; i++){
          for(j=0; j<datac_s.length; j++){
            if(num[i].id == datac_s[j].skill_id){
              table.row.add( [
                num[i].skill_name,
                '<input id="chk" name="chk['+i+']" type="checkbox" value="'+num[i].id+'" checked>',
                '<input id="des" style="width: 100%" name="description[]" type="txt" placeholder="Write Short Description" class="form-control" value="'+datac_s[j].description+'" >'
              ] ).draw()
              hay[k]=num[i].id;
              k++;
              console.log(num[i].id);
            }
          }
        }
        console.log(hay);
        for(i=0; i<num.length; i++){
          if ($.inArray(num[i].id, hay) == -1){
            console.log($.inArray(num[i].id, hay));
            table.row.add( [
              num[i].skill_name,
              '<input id="chk" name="chk['+i+']" type="checkbox"  value="'+num[i].id+'">',
              '<input id="des" style="width: 100%"  name="description[]" placeholder="Write Short Description"  type="txt" class="form-control" >'
            ] ).draw()
            hay[k]=num[i].id;
            k++;
          }
        }
      });
      $('#chk-box-table tbody').on( 'change', '#chk', function(){
        console.log($(this).val());
        $(this).prop( "disabled", false );
      });

      $.ajax({
        type:   'POST', 
        url :   "{{URL::to('/')}}/skill_list",
        headers:{
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },        
        dataType: 'json',
          success: function(data) {
            var dataSet = data.data;
            table = $('#chk-box-table').DataTable({
              searching: false,
              ordering: true,
              paging: false,
              bInfo: false,
            });
          }
      });

    });
  </script>
@stop
