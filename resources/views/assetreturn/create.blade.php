
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
    <h3 id="page-header" class="box-title">Asset Return</h3>
     <div class="box-tools pull-right">
          <a type="button" href="{{ URL::previous() }}" class="btn btn-box-tool"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
  </div>

  <form method="POST" action="{{ route('assetreturn.store') }}" onkeypress = "return event.keyCode != 13;" id="asset_return_form">

    @php $form_type ='create' @endphp
    @include('assetreturn._form')

  </form>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>
    $(document).ready(function($) {

        $('#return_date').datepicker({
            autoclose: true
        });

        if ($('[data-id=choose_employee]').is(':checked')) {
            $('#choose_employee').show();
            $('#choose_department').hide();
        }

        $('input[type=radio]').click(function(e) {
            let dataId = $(this).data('id')

            if (dataId === 'choose_employee') {
                $('#hrm_depertments_id').val('')
                $('#hrm_depertments_id').text('')

                $('#asset_id').val('')
                $('#asset_id').text('')

                $('#choose_employee').show();
                $('#choose_department').hide();
            }
            else if (dataId === 'choose_department') {
                $('#employee_name').val('')
                $('#employee_name').text('')

                $('#asset_id').val('')
                $('#asset_id').text('')

                $('#choose_department').show();
                $('#choose_employee').hide();
            }
        })


        $employee = $('#employee_name').select2({
            placeholder: 'Enter an Employee Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('/assigned_list_employee') }}",
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
                    }
                },
                cache: true
            }
        });


        $employee.on("select2:select", function (e) {
            $('#asset_id').select2({
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: "{{ url('/asset_assign_list_get') }}",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term,
                            employee_id: e.params.data.id,
                        }
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        }
                    },
                    cache: true
                }
            });
        });

        $employee.on("select2:unselect", function (e) {
            $('#asset_id').val('')
            $('#asset_id').text('')
        });

        $department = $('#hrm_depertments_id').select2({
            placeholder: 'Enter an Department Name',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{ url('/assigned_list_department') }}",
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
                    }
                },
                cache: true
            }
        });

        $department.on("select2:select", function (e) {
            $('#asset_id').select2({
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    url: "{{ url('/asset_assign_list_get') }}",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term,
                            department_id: e.params.data.id,
                        }
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        }
                    },
                    cache: true
                }
            });
        });

        $department.on("select2:unselect", function (e) {
            $('#asset_id').val('')
            $('#asset_id').text('')
        });

        $asset_return = $('#asset_return_type_id').select2({
            placeholder: 'Enter Return Type',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{URL::to('/')}}/return_type_list_data',
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
                    }
                },
                cache: true
            }
        });

        $asset = $('#asset_id').select2({
            placeholder: 'Select Asset'
        });

        $(document).on('change','#employee_name', function () {
            var employee_id = $(this).val();

            if(employee_id) {
                $('#asset_id').select2({
                    allowClear: true,
                    ajax: {
                        dataType: 'json',
                        url: "{{ url('/asset_assign_list_get') }}",
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term,
                                employee_id: employee_id
                            }
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            }
                        },
                        cache: true
                    }
                });
            } else {
                return false;
            }
        });
    });
</script>
@stop
