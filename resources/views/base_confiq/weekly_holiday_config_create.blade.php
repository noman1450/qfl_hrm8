@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

    <style>
        table#requsitionDetailsTable > tbody > tr:not(:first-child) > td:not(:first-child) {
            text-align: center;
        }
        .select2-container .select2-search--inline .select2-search__field {
            padding: 0 10px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3c8dbc;
            border-color: #367fa9;
            padding: 1px 10px;
            color: #fff;
        }
    
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            margin-right: 5px;
            color: rgba(255,255,255,0.7);
        }
    </style>
@endsection

@section('content')
    @if (session('warning'))
        <div class='alert alert-warning alert-dismissible'>
            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
            <strong>Warning!</strong> {{ session('warning') }}
        </div>    
    @endif

    <div class="box box-default">
        <div class="box-body">
        <h4>Create Weekly Holiday</h4>
        
        <div class="row">
            <form action="{{ URL::to('weekly_holiday_config_store') }}" method="post">
                {{ csrf_field() }}
            
                {{-- @if(!is_null($jobRequsition)) {{ method_field('patch') }} @endif --}}
            
            
                <div class="box-body" style="padding-left: 20px;padding-right: 20px">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start From <span class="text-danger">*</span></label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                    
                                            <input type="text" class="form-control required datepicker" id="start_date" name="start_date"  value="{{ old('start_date') ?? date('d-m-Y') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hrm_location_id">Location Name <span class="text-danger">*</span></label>
                                        <select id="hrm_location_id" name="hrm_location_id" class="form-control required hrm_location_id" style="width: 100%">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Days Name <span class="text-danger">*</span></label>
                                        
                                        <div id="daysName"></div>
                                    </div>

                                    <div class="box-footer" style="padding-right: 0">
                                        <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div> <!-- /.box-body -->
            </form>
        </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<script>

    $(document).ready(function() {

        $('#start_date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            orientation: "auto",
        });

        var $location = $('#hrm_location_id').select2({
            placeholder: 'Select a Location',
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: '{{ URL::to('/') }}/location_list_data_all',
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

        $location.on("select2:select", function (e) {
            $.ajax({
                type: 'POST',
                url : '{{ URL::to('/')}}/weekly_holiday_config_change_location',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { location_id: $("#hrm_location_id").val() },
                dataType: 'json',
                success: function(data) {

                    var $html = ""

                    data.forEach(function (day) {
                       $html += `
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hrm_days_name_id[]" ${day.id !== null ? 'checked' : ''} value="${day.hrm_days_name_id}"> ${day.days_name}
                                </label>
                            </div> 
                        `
                    })

                    $('#daysName').html($html)
                }
            })
        })
    });
</script>
@endsection