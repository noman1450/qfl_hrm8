@extends('layouts.main')

@section('styles')
	<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  	<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
			<h3 class="box-title">Skill List</h3>

			<div class="box-tools pull-right">
				<a type="button" href="{{ URL('/') }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
			</div>
		</div>

		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="box-body">
						<div class="row">
							<div class="box-header with-border">
								<button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#modal_skill" data-whatever="@mdo" style="font-size: 12px; font-weight: bold">Add Skill</button>
							</div>

							<div class="modal fade" id="modal_skill" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
									@include('skill.create')
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group col-lg-6 col-md-6 col-xs-12" style="overflow: auto;">
									<table id="data-table" class="table table-bordered table-hover" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th style="width: 10%">ID</th>
												<th style="width: 70%">Skill</th>
												<th style="width: 10%">Edit</th>
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
			</div>
		</div>
	</div>
@endsection


@section('script')
	<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

	<script>
		$(document).ready(function($) {

            function LoadSkillData(){

				$.ajax({
				type:   'POST',
				url :   "{{URL::to('/')}}/skill_list",
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
							{ "data": "id" },
							{ "data": "skill_name" },
							{ "data": "Link",
								"mRender": function(data, type, full) {
									return '<a  data-skill_id="' + full.id + '" data-skill_name="' + full.skill_name + '"  class="btn btn-primary btn-flat btn-sm showme"> <span class="glyphicon glyphicon-edit">Edit</a>';
								}
							},
							{ "data": "Link",
								"mRender": function (data, type, full) {
								return '<form action="{{URL::to('/')}}/skill/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="block btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
								}
							}
						],
						"order": [[0,'asc']]
						});
					}
				});



				$('#data-table').on('click', '.showme', function() {
					// console.log($(this).data(''))
					$("#skill_id").attr("value", $(this).data('skill_id'));
					$('#skill_id').val($(this).data('skill_id'));
					$('#skill_name').val($(this).data('skill_name'));
					$('#modal_skill').modal('show');

				});

            };

            LoadSkillData();

			$('#modal_skill').on('hidden.bs.modal', function() {
				$("#skill_id").val('');
				$("#skill_name").val('');
			});

			$('#skill_form').on('submit',(function(e) {
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
							alert(data.message);
							LoadSkillData();
							$("#skill_id").val('');
							$("#skill_name").val('');
							$("#modal_skill").modal('hide');



							// window.location.replace('/skill');

						}else{
							$('#btnSubmit').attr("disabled", false);
							$("#btnSubmit").val('Submit');
							alert(data.message);
							// window.location.replace('/skill');
						}
					}
				});
			}));


		});


	</script>

@endsection
