@extends('layouts.main')

@section('styles')
	<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
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
		body {font-family: Arial;}

		/* Style the tab */
		.tab {
			overflow: hidden;
			border: 1px solid #ccc;
			background-color: #f1f1f1;
		}

		/* Style the buttons inside the tab */
		.tab button {
			background-color: inherit;
			float: left;
			border: none;
			outline: none;
			cursor: pointer;
			padding: 14px 16px;
			transition: 0.3s;
			font-size: 17px;
		}

		/* Change background color of buttons on hover */
		.tab button:hover {
			background-color: #ddd;
		}

		/* Create an active/current tablink class */
		.tab button.active {
			background-color: #ccc;
		}

		/* Style the tab content */
		.tabcontent {
			display: none;
			padding: 6px 12px;
			border: 1px solid #ccc;
			border-top: none;
		}
		.kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
			margin: 0;
			padding: 0;
			border: none;
			box-shadow: none;
			text-align: center;
		}
		.kv-avatar {
			display: inline-block;
		}
		.kv-avatar .file-input {
			display: table-cell;
			width: 213px;
		}
		.kv-reqd {
			color: red;
			font-family: monospace;
			font-weight: normal;
		}
		.btn-secondary {
			margin-top: 5px;
		}
		.btn-file{
			margin-top: 5px;	
		}
  	</style>
@stop

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
			<h3 class="box-title">Asset Assigned List</h3>
			
			<div class="box-tools pull-right">
				<a type="button" href="{{ URL('/') }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
			</div>
		</div>

		<div class="box-body">

		</div>

		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#Active_List" data-toggle="tab" aria-expanded="true">Active List</a></li>
				<li class=""><a href="#Inactive_Type" data-toggle="tab" aria-expanded="false">Reject/Reuse List</a></li>
				<li class=""><a href="{{ route('assetassign.create')}}"><input type="button" value="Assign Asset" class="btn-success btn btn-sm button pull-left" ></a></li>

			</ul>

		

			<div class="tab-content">



				<div class="tab-pane active" id="Active_List">
					<div class="box box-default">
						<div class="box-body">
							<div class="col-md-12">

				

								<div class="form-group col-lg-12 col-md-12 col-xs-12">
									<table id="data-table" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th style="width: 10%">Img</th>
												<th style="width: 25%">Employee</th>
												<th style="width: 25%">Asset</th>
												<th style="width: 10%">#Assign No</th>
												<th style="width: 10%">Assign Date</th>
												<th style="width: 20%">Actions</th>
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

				<div class="tab-pane" id="Inactive_Type">
					<div class="box box-default">
						<div class="box-body">
							<div class="col-md-12">
								<div class="form-group col-lg-12 col-md-12 col-xs-12">
									<table id="data-table-inactive" class="table table-bordered table-hover">
										<thead>
											<tr>
											    <th style="width: 10%">Img</th>
												<th style="width: 25%">Employee</th>
												<th style="width: 25%">Asset</th>
												<th style="width: 10%">#Assign No</th>
												<th style="width: 10%">Assign Date</th>
												<th style="width: 20%">Status</th>
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
			</div>
		</div>	
	</div>
@endsection


@section('script')
	<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

	<script type="text/javascript">
		$(document).ready(function($) {
			$.ajax({
			type:   'POST', 
			url :   "{{URL::to('/')}}/asset_assign_list_inactive",
			headers:{
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},        
			dataType: 'json',
				success: function(data) {
					var dataSet = data.data;
					table = $('#data-table-inactive').DataTable( {
						destroy:    true,
						paging:     true,
						searching:  true,
						ordering:   true,
						bInfo:      true,
						"data":     dataSet,

					"columns": [

						{ "data": "Link",
							"mRender": function(data, type, full) {
								return '<img style="max-width:100%" src="/employee_image/'+full.Images+'"></img>';
							}
                 		},
						{ "data": "employee" },
						{ "data": "asset" },
						{ "data": "asset" },
						{ "data": "created_at" },
						{ "data": "statustext" }
					],
					"order": [[0,'asc']]
					});
				}
			});

			$.ajax({
			type:   'POST', 
			url :   "{{URL::to('/')}}/asset_assign_list",
			headers:{
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},        
			dataType: 'json',
				success: function(data) {
					var dataSet = data.data;
					table = $('#data-table').DataTable( {
						destroy:    true,
						paging:     true,
						searching:  true,
						ordering:   true,
						bInfo:      true,
						"data":     dataSet,

					"columns": [

						{ "data": "Link",
							"mRender": function(data, type, full) {
								return '<img style="max-width:100%" src="/employee_image/'+full.Images+'"></img>';
							}
                 		},
						{ "data": "employee" },
						{ "data": "asset" },
						{ "data": "store_no" },
						{ "data": "created_at" },
						{ "data": "Link",
							"mRender": function(data, type, full) {
								return '<a  href="{{URL::to('/')}}/assetassign/'+full.id+'/edit" data-asset_type_id="' + full.id + '" data-description="' + full.description + '" data-model="' + full.model + '" class="btn btn-primary btn-flat btn-sm showme2"> <span class="glyphicon glyphicon-edit">Edit</a>';
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