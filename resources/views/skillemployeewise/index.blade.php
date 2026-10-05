@extends('layouts.main')

@section('styles')
	<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  	<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
  	<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
	<style>
		td.details-control {
		background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;
		cursor: pointer;
		}
		tr.shown td.details-control {
		background: url('{{URL::to('/')}}/dist/img/details_close.png') no-repeat center center;
		}
		.img {
		height: 100px
		}
		h1 {
		text-align: center
		}
	</style>
@stop

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
			<h3 class="box-title">Employee Skill List</h3>
			
			<div class="box-tools pull-right">
				<a type="button" href="{{ URL('/') }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
			</div>
		</div>

		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group col-lg-12 col-md-12 col-xs-12">
						<a href="{{ route('skillemployeewise.create')}}"><input type="button" value="Add/Update Skill of Employees" class="btn-success btn btn-sm button pull-left" style="font-size: 12px; font-weight: bold;"></a>
					</div>
				</div>

				<div class="row" style="padding: 20px;">
					<div class="col-lg-4" style="padding: 20px;">
						<select style="width: 80%;" class="form-control select2" id="skill_id" autofocus>
						</select>
					</div>
					<div class="col-lg-4" style="padding: 20px">
						<select style="width: 80%;" class="form-control select2" id="location" autofocus>
						</select>
					</div>
					<div class="col-lg-4" style="padding: 20px">
						<select style="width: 80%;" class="form-control select2" id="department" autofocus>
						</select>
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group col-lg-12 col-md-12 col-xs-12">
						<table id="data-table" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th style="width: 5%">Details</th>
									<th style="width: 5%">Img</th>
									<th style="width: 50%">Employee</th>
									<th style="width: 30%">Skills</th>
									<th style="width: 10%">Delete</th>
								</tr>
							</thead>

							<tbody>
							</tbody>
						</table>					
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


@section('script')
	<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

	<script>
		$(document).ready(function($) {
			$skill = $('#skill_id').select2({
				placeholder: 'Enter Skill Name',
				allowClear: true,
				ajax: {
				dataType: 'json',
				url: '{{URL::to('/')}}/skill_list_select',
				delay: 250,
				data: function(params) {
					return {
					term: params.term,
					type: 1
					}
				},
				processResults: function (data, params) {
					console.log(data);
					params.page = params.page || 1;
					return {
					results: data
					};
				},
				cache: true
				}
			});
			$location = $('#location').select2({
				placeholder: 'Enter Location Name',
				allowClear: true,
				ajax: {
				dataType: 'json',
				url: '{{URL::to('/')}}/location_list_data_all',
				delay: 250,
				data: function(params) {
					return {
					term: params.term,
					type: 1
					}
				},
				processResults: function (data, params) {
					console.log(data);
					params.page = params.page || 1;
					return {
					results: data
					};
				},
				cache: true
				}
			});
			$department = $('#department').select2({
				placeholder: 'Enter Department Name',
				allowClear: true,
				ajax: {
				dataType: 'json',
				url: '{{URL::to('/')}}/depertment_list_data',
				delay: 250,
				data: function(params) {
					return {
					term: params.term,
					type: 1
					}
				},
				processResults: function (data, params) {
					console.log(data);
					params.page = params.page || 1;
					return {
					results: data
					};
				},
				cache: true
				}
			});
			dataLoad = function(){
				table = $('#data-table').DataTable( {
					destroy:    true,
					paging:     true,
					searching:  true,
					ordering:   true,
					bInfo:      true,
					"ajax": {
						"url": "{{URL::to('/')}}/skill_employeewise_list",
						"type": "POST",
						"headers":{'X-CSRF-TOKEN': '{{ csrf_token() }}'},  
						"data":
						{
							skill_id: $("#skill_id").val(),
							location: $("#location").val(),
							department: $("#department").val(),
						}                
					},
				"columnDefs": [
					{
						"targets": [ 3 ],
						"visible": true,
						"searchable": true
					}
				],
				"columns": [
					{
						"className":      'details-control',
						"orderable":      true,
						"data":           null,
						"defaultContent": ''
					},				
					{ "data": "Link",
						"mRender": function(data, type, full) {
							return '<img style="max-width:100%" src="/employee_image/'+full.imgpath+'" </img>';
						}
					},
					{ "data": "employee" },
					{ "data": "skill_name2" },
					{ "data": "Link",
						"mRender": function (data, type, full) {
						return '<form action="{{URL::to('/')}}/skillemployeewise/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="block btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
						}
					}
				],
				"order": [[0,'asc']]
				});
			}
			dataLoad();
			$skill.on("select2:select", function (e) {
				dataLoad();
			});
			$skill.on("select2:unselect", function (e) {
				$('#skill_id').val(null).trigger("change");
				dataLoad();
			});
			$location.on("select2:select", function (e) {
				dataLoad();
			});
			$location.on("select2:unselect", function (e) {
				$('#location').val(null).trigger("change");
				dataLoad();
			});
			$department.on("select2:select", function (e) {
				dataLoad();
			});
			$department.on("select2:unselect", function (e) {
				$('#department').val(null).trigger("change");
				dataLoad();
			});
		});
	</script>

	<script>
		function format(d) {

			return '<div class="col-lg-3 col-md-6 col-sm-12">'+
					'<table class="table table-bordered table-hover">'+
					'<tr>'+
					'<td>'+"Skills"+'</td>'+
					'<td>'+"Description"+'</td>'+
					'</tr>'+
					'<tr>'+
					'<td>'+d.skill_name+'</td>'+
					'<td>'+d.description+'</td>'+
					'</tr>'+  
					'</table>'+
					'</div>';
		}

		$('#data-table tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format(row.data()) ).show();
				tr.addClass('shown');
			}
		}); 
	</script>

@endsection