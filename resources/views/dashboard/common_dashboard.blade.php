<!-- create_employee -->
<!-- create_marital_status -->
@extends('layouts.main')
@section('styles')
@section('styles') <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
 <link rel="stylesheet" href="{{asset('plugins/bootstrapvalidator/bootstrapValidator.min.css')}}"> 
 <style type="text/css"> .header{ /*font-size: 17px;*/ } </style> @endsection
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
                <input type="hidden" id="requestId" value="{{ $requestId }}">
                @if($requestId == 2)
                <ul class="nav nav-tabs sidebar-tabs" id="sidebar" role="tablist">
                    <li class="active"><a href="#tab-1" role="tab" data-toggle="tab" id="department_info">Department Information</a></li>
                    <li><a href="#tab-2" role="tab" data-toggle="tab" id="category_info">Category Information</a></li>
                </ul>
                @endif
                @if($requestId == 4)
                <ul class="nav nav-tabs sidebar-tabs" id="sidebar" role="tablist">
                    <li class="active"><a href="#tab-1" role="tab" data-toggle="tab" id="department_info">Designation Information</a></li>
                    <li><a href="#tab-2" role="tab" data-toggle="tab" id="category_info">Department Wise Information</a></li>
                </ul>
                @endif
                <div style="margin:30px 0;display:flex;justify-content:center">
                    <div class="form-group">
                        <label for="location" style="margin-right: 15px">Location: </label>
                        <select name="location" id="location" class="form-control" style="width:400px;"></select>
                    </div>
                </div>

                <div style="text-align: center;">
                    <h4 class="" style="font-weight:bold;">{{$company_name}}</h4>
                    <h5 class="" style="font-weight:bold;">{{$address}}</h5>
                    <h5 class="" style="font-weight:bold;" id="title">{{$title}}</h5>
                </div>



                    <table id="list_table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead></thead>
                        <tbody></tbody>
              
                
                    </table>
                </div>
		    </div>
	    </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

$(document).ready( function () {

    $(document).on('click','#department_info, #category_info', function (e) {
        var target = $(e.target).attr("href"); // Get the tab ID
       

         if($("#requestId").val() == 2){
            if (target === '#tab-1') {
                dataLoad();
            } else if (target === '#tab-2') {
                dataLoad(7);
            }
        }else{  
            if (target === '#tab-1') {
                dataLoad();
            } else if (target === '#tab-2') {
                dataLoad(8);
            }
        }
        
    });

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

    // get category information data

    $location.on('select2:select', function (e) {
        // var target = $(e.target).attr("href"); // Get the tab ID
        var requestId = $("#requestId").val();
        if($("#requestId").val() == 2){
            var target = $('.nav-tabs .active a').attr("href");
            if (target === '#tab-1') {
                dataLoad();
            } else if (target === '#tab-2') {
                dataLoad(7);
            }
        }else{
           
            var target = $('.nav-tabs .active a').attr("href");
            if (target === '#tab-1') {
                dataLoad();
            } else if (target === '#tab-2') {
                dataLoad(8);
            }
        }

    });


    $location.on('select2:unselect', function (e) {
        $('#location').val(null).trigger("change");
        var target = $(e.target).attr("href"); // Get the tab ID
        if (target === '#tab-1') {
            dataLoad();
        } else if (target === '#tab-2') {
            dataLoad(7);
        }else{
            dataLoad();
        }
    });

    dataLoad();
    
    function dataLoad(id = null) {
    let columnsDef = (id == 8) ? [
        { data: null, title: "#" },
        { data: "department_name", title: "Department Name" },
        { data: "description", title: "Description" },
        { data: "count_employee", title: "Employee(s)" }
    ] : [
        { data: null, title: "#" },
        { data: "description", title: "Description" },
        { data: "count_employee", title: "Employee(s)" }
    ];

    // Destroy previous table
    if ($.fn.DataTable.isDataTable('#list_table')) {
        $('#list_table').DataTable().clear().destroy();
    }

    // Remove old thead/tfoot
    $('#list_table thead').remove();
    $('#list_table tfoot').remove();

    // Create thead dynamically
    let theadHtml = '<thead><tr>';
    columnsDef.forEach(col => { theadHtml += `<th>${col.title}</th>`; });
    theadHtml += '</tr></thead>';
    $('#list_table').prepend(theadHtml);

    // Create tfoot dynamically only if needed
    if (id == 8) {
        let tfootHtml = '<tfoot><tr>';
        columnsDef.forEach(()=> tfootHtml += '<th></th>');
        tfootHtml += '</tr></tfoot>';
        $('#list_table').append(tfootHtml);
    }else{
        let tfootHtml = '<tfoot><tr>';
        columnsDef.forEach(()=> tfootHtml += '<th></th>');
        tfootHtml += '</tr></tfoot>';
        $('#list_table').append(tfootHtml);
    }

    $('#list_table').DataTable({
        destroy: true,
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        scrollX: true,
        columns: columnsDef,
        ajax: {
            url: "{{ route('common_dashboard', request()->segment(2)) }}",
            type: "GET",
            data: function(q){
                q.location = $('#location').val();
                if(id!==null) q.id = id;
            }
        },
        rowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
            $("td:first", nRow).html(iDisplayIndexFull + 1);
        },
         "drawCallback": function (settings) {
                var requestId = $("#requestId").val();
                if ($("#requestId").val() == 2) {
                    var target = $('.nav-tabs .active a').attr("href");
                    if (target === '#tab-1') {
                        $("#title").html("Department Wise Employee List");
                    }
                    if (target === '#tab-2') {
                        $("#title").html("Category wise Employee List");
                    }
                }
            },
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let lastCol = columnsDef.length - 1;

            // যদি tfoot না থাকে, auto create করুন
            if ($('#list_table tfoot').length === 0) {
                let tfoot = $('<tfoot><tr></tr></tfoot>').appendTo('#list_table');
                for (let i = 0; i < columnsDef.length; i++) {
                    tfoot.find('tr').append('<th></th>');
                }
            }

            // Last column sum
            let total = api.column(lastCol, { page: 'current' }).data()
                .reduce((a, b) => (parseInt(a) || 0) + (parseInt(b) || 0), 0);

            // Conditional footer label
            if (id == 8) {
                $(api.column(0).footer()).html('Total:');
                $(api.column(lastCol).footer()).html(total);
            } else {
                $(api.column(0).footer()).html('Total:');
                $(api.column(lastCol).footer()).html(total);
            }
        },

        dom: 'Bfrtip',
       buttons: [
            {
                extend: 'excel',
                exportOptions: {
                    columns: [0, 1, 2], // Include the serial number column (0) and the rest
                    format: {
                        body: function (data, row, column, node) {
                            // For the first column (serial number), return row number instead of data
                            if (column === 0) {
                                return row + 1;
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: [0, 1, 2], // Include the serial number column (0) and the rest
                    format: {
                        body: function (data, row, column, node) {
                            // For the first column (serial number), return row number instead of data
                            if (column === 0) {
                                return row + 1;
                            }
                            return data;
                        }
                    }
                }
            }
        ],

    });
}





})
</script>
@endsection
