<!-- active_employee_list -->
<!-- employee_list -->
@extends('layouts.main')

<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">

.noflag
{
      background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid green;
      opacity: 0.1;

}

.noflag:hover
{
  /*background: url('{{URL::to('/')}}/dist/img/details_open.png') no-repeat center center;*/
  background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid red;
      opacity: 1;


}

.redflag{
      background: url('{{URL::to('/')}}/dist/img/redflag.jpg') no-repeat center center;
      cursor: pointer;
      width: 80px;
      height: 25px;
      border:1px solid white;
}

</style>


@endsection



<!-- content -->
@section('content')

    <div>
      <div id="alert-danger"></div>
      <div id="alert-success"></div>
    </div>



<div class="box box-default">
	<div class="box-header with-border">
    	<h3 class="box-title">Active Employee List</h3>
	</div>


	<div class="box-body">
        <form action="{{ route('reporting_boss_change.store') }}" id="submitForm" >
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <select class="form-control changed_value" id="location" style="width: 100%;"  required>
                            @foreach ($default_user_location as $keys)
                                <option value="{{ $keys->id }}" selected>{{$keys->location_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="manage_by">Managed By</label>
                        <select class="form-control changed_value" id="manage_by" style="width: 100%;"  required>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="new_manage_by">New Managed By</label>
                        <select class="form-control" id="new_manage_by" style="width: 100%;"  required>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2" style="margin-top: 25px;">
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm submitBtn cab">
                        <span class="ladda-label">Transfer</span>
                        <i class="fa-solid fa-bolt"></i>
                    </button>
                </div>

            </div>

            <div class="row">
            <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                    <div id="partial_blade"></div>
                </div>
            </div>
        </form>
	</div>
</div>
@endsection



<!-- script -->
@section('script')
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>


$(document).ready(function($) {

    $('#checked_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.checkbox-item').prop('checked', isChecked);
    });

    // If any checkbox is unchecked, uncheck the "Select All" checkbox
    $('.checkbox-item').on('change', function() {
        if (!$(this).is(':checked')) {
            $('#checked_all').prop('checked', false);
        }
        // If all checkboxes are checked, check the "Select All" checkbox
        if ($('.checkbox-item:checked').length === $('.checkbox-item').length) {
            $('#checked_all').prop('checked', true);
        }
    });


    $('#submitForm').on('submit', function(e) {
        e.preventDefault();

        var selectedEmployees = $('input[name="employee_ids[]"]:checked').length;
        if (selectedEmployees === 0) {
            alert('Please select at least one employee.');
            return;  // Stop form submission
        }

        var manage_by =$("#manage_by").val();
        var new_manage_by =$("#new_manage_by").val();

        if (manage_by === new_manage_by) {
            alert('Manage by and new manage by can not same.');
            return;  // Stop form submission
        }

        var formData = {
            location: $('#location').val(),
            manage_by: $('#manage_by').val(),
            new_manage_by: $('#new_manage_by').val(),
            employee_ids: $('input[name="employee_ids[]"]:checked').map(function() {
                return this.value;
            }).get(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('reporting_boss_change.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                alert('Data submitted successfully!');
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error, e.g., display error messages
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    });

    $role= $('#location').select2({
        placeholder: 'Choose Location',
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

    // $managedBy = select2Dropdown( "#manage_by",  "{{URL::to('/')}}/employee_list_data", "Search manage by" );

    // $newManagedBy = select2Dropdown(
    //     "#new_manage_by",
    //     "{{URL::to('/')}}/employee_list_data",
    //     "Search manage by"
    // );

    $("#location").on('change', function() {
        $("#manage_by").val(null).trigger('change');
        $("#new_manage_by").val(null).trigger('change');
    });

    // $('#location').on('change', function(e) {
    //     $('.manage_by').select2({
    //         placeholder:'Enter an Employee Name',
    //         allowClear: true,
    //         ajax: {
    //             dataType: 'json',
    //             url: "{{URL::to('/')}}/join_employee_list",
    //             delay: 250,
    //             data: function(params) {
    //                 return {
    //                     term: params.term,
    //                     apply_old_info:$('input[name=apply_old_info]:checked').val(),
    //                     location: e.target.value
    //                 }
    //             },
    //             processResults: function (data, params) {
    //                 params.page = params.page || 1;
    //                 return {
    //                     results: data,
    //                     pagination: {
    //                         more: (params.page * 30) < data.total_count
    //                     }
    //                 };
    //             },
    //             cache: true
    //         }
    //     });
    // })


    $(document).on('change', '.changed_value', function() {
        $.ajax({
            url: "{{URL::to('/')}}/reportingboss_employeelist",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                location: $("#location").val(),
                manage_by_id: $("#manage_by").val()
            },
            success: function(response) {
                $('#partial_blade').html(response);
            }
        });
    });


    $('#manage_by').select2({
        placeholder:'Enter Manage From Employee',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/join_employee_list",
            delay: 250,
            data: function(params) {
                return {
                    term: params.term,
                    location: $('#location option:selected').val()
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

    $('#new_manage_by').select2({
        placeholder:'Enter an Employee Name',
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/join_employee_list",
            delay: 250,
            data: function(params) {
                return {
                    term: params.term,
                    location: $('#location option:selected').val()
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


});
</script>

@endsection
