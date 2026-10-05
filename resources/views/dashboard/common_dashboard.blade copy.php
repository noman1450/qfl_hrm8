<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.css')}}">
<style type="text/css">
	.header{
		/*font-size: 17px;*/
	}
</style>
@endsection
@section('content')
<div class="box box-primary">
	<!-- <div class="box-header with-border"> -->
<!-- 		<h3 class="box-title">{{$company_name}}</h3> <br>
		<h3 class="box-title">{{$address}}</h3><br>
		<h3 class="box-title">{{$title}}</h3> -->
	<!-- 	<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div> -->
	<!-- </div> -->

    {{-- @dd(request()->segment(2)) --}}

	@php
	$count_employee = 0
	@endphp

	<div class="box-body">
		<div class="row">

            <div class="col-md-6" >
                <ul class="nav nav-tabs sidebar-tabs" id="sidebar" role="tablist">
                    <li class="active"><a href="#tab-1" role="tab" data-toggle="tab">Department Information</a></li>
                    <li><a href="#tab-2" role="tab" data-toggle="tab">Category Information</a></li>
                </ul>

                <div style="margin:30px 0;display:flex;justify-content:center">
                    <div class="form-group">
                        <label for="location" style="margin-right: 15px">Location: </label>
                        <select name="location" id="location" class="form-control" style="width:400px;"></select>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab-1">
                        <!-- <p>Recent content</p> -->
                       <div class="box-body tabcontent" id="basicinfo">
                           <div class="row">
                               <div class="col-lg-8 col-md-8 col-xs-12 personal-info">
                                <table id="list_table" class="table table-bordered table-hover " cellspacing="0" width="100%" >
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th style="width: 50%">Description</th>
                                            <th style="width: 40%">Employee(s)</th>

                                        </tr>
                                    </thead>
                                        <tbody>
                                            <?php $count_employee += $data->count_employee ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                </table>

                               </div>
                               <div class="col-lg-4 col-md-4 col-xs-12 text-center">
                                   <img  id="blah" src="{{asset('employee_image/'.$employee->Images)}}" alt="your image" />
                               </div>
                           </div>
                       </div>
                     </div>
                </div>

                <div style="text-align: center;">
                    <h4 class="" style="font-weight:bold;">{{$company_name}}</h4>
                    <h5 class="" style="font-weight:bold;">{{$address}}</h5>
                    <h5 class="" style="font-weight:bold;" >{{$title}}</h5>
                </div>



                    <table id="list_table" class="table table-bordered table-hover " cellspacing="0" width="100%" >
                        <thead>
                            <tr>
                                <th style="width: 10%">#</th>
                                <th style="width: 50%">Description</th>
                                <th style="width: 40%">Employee(s)</th>

                            </tr>
                        </thead>
                            <tbody>
                                {{-- @foreach ($data as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->description }}</td>
                                        <td>{{ $data->count_employee }}</td>
                                    </tr>
                                    <?php $count_employee += $data->count_employee ?>
                                @endforeach --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                    </table>
                </div>
		    </div>
	    </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>

$(document).ready( function () {
    $location = $('#location').select2({
        placeholder: 'Enter a location',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/location_list_data',
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

    $location.on('select2:select', function (e) {
        dataLoad();
    });


    $location.on('select2:unselect', function (e) {
        $('#location').val(null).trigger("change");
        dataLoad();
    });

    dataLoad();

    function dataLoad() {
        var table = $('#list_table').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            ordering: false,
            bInfo: false,
                scrollX: true,

            ajax: {
                url: "{{ route('common_dashboard', request()->segment(2)) }}",
                type: "GET",
                dataType: "json",
                data: function (query) {
                    query.location = $('#location').val()
                }
            },
            columns: [
                { data: "description" },
                { data: "description" },
                { data: "count_employee" },
            ],

            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                //debugger;
                var index = iDisplayIndexFull + 1;
                $("td:first", nRow).html(index);

                return nRow;
            },

            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // console.log(data);

                total_quantity = api
                    .column(2)
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $( api.column(1).footer() ).html('      Total :');
                $( api.column(2).footer() ).html(total_quantity);
            }
        });
    }
})
</script>
@endsection
