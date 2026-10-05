<!-- attandance_list -->
@extends('layouts.main')
<!-- styles -->
@section('styles')
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
<link rel="stylesheet" href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      padding: 5px;
    }

    .table {
        width: 100%;          /* Ensure table spans full width */
        border-collapse: collapse; /* Merge borders */
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
    .table-responsive {
        overflow-y: auto; /* Enables vertical scrolling for the table */
        max-height: 50px;
        position: relative;
    }
    .table-container {
        max-height: 600px; /* Set table height */
        overflow-y: auto;  /* Enable vertical scrolling */
    }

    .table-container::-webkit-scrollbar {
        width: 8px; /* Customize scrollbar width */
    }

    .table-container::-webkit-scrollbar-thumb {
        background-color: #ccc; /* Customize scrollbar color */
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-track {
        background-color: #f1f1f1; /* Customize scrollbar track */
    }
    .table thead th {
        position: sticky;    /* Keep the header sticky at the top */
        top: 0;
        background-color: #f8f9fa;
        z-index: 1;         /* Ensure header is above other content */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Header shadow effect */
    }

</style>

@endsection
@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Pay Register Details List</h3>
    <button type="button" class="btn btn-sm btn-default btn-outline-default mb-1 pull-right" id="printBtn">
        <i class="fa-solid fa-file-export"></i> Export
    </button>
  </div>

<div class="col-xs-12" style="margin-bottom: 10px;">
    <form action="{{ url('payregister/searchDetails') }}" method="GET" class="form-inline">
        <div class="col-xs-12 mb-5" style="padding-left: 0px; padding-top: 10px;">
            <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                <input type="month" class="form-control col-lg-12 filter" id="month_year" name="month_year"  value="{{ date("Y-m") }}" />
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                 <select class="form-control filter" id="employee_id" name="employee_id" style="width: 100%;" > </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                 <select class="form-control filter" id="department" name="department" style="width: 100%;" > </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                <select class="form-control filter" id="section" name="section" style="width: 100%;" >  </select>
           </div>

           <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                <select class="form-control filter" id="category" name="category" style="width: 100%;" >  </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" >
                <select  class="form-control col-lg-12 filter" id="location" name="location" style="width: 100%;">  </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="margin-top: 5px; padding-top: 5px; ">
                <select  class="form-control col-lg-12 filter" id="payment_mode" name="payment_mode" style="width: 100%;">
                    <option value="">Payment Mode</option>
                    <option value="1">Cash</option>
                    <option value="2">Bank</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-2 col-xs-12 form-group" style="margin-top: 5px; padding-top: 5px; display: none" id="bank_div">
                <select  class="form-control col-lg-12 filter" id="bank_id" name="bank_id" style="width: 100%;">
                </select>
            </div>
        </div>
    </form>
</div>


  <div class="box-body mt-5">
    <div class="row ">
      <div class="form-group col-lg-12 col-md-12 col-xs-12 table-container" style="overflow: auto;">
            <table id="result-container" class="table table-bordered table-hover ">
                <thead>
                    <tr>
                        @if(!empty($dataset) && isset($dataset[0]))
                        <th style="width: 3px;">SL</th>
                        @endif
                        @if(!empty($dataset) && isset($dataset[0]))
                            <?php
                                $count = [
                                    '1' => 0,
                                    '2' => 0,
                                ];

                                foreach ($dataset[0] as $key=>$val) {
                                    $parts = explode('|', $key);
                                        if (count($parts) > 1) {
                                            $secondPart = trim($parts[1]);
                                            if (array_key_exists($secondPart, $count)) {
                                                $count[$secondPart]++;
                                            }
                                        }
                                }

                            ?>
                            @foreach($dataset[0] as $key => $value)
                                <?php
                                    $parts = explode('|', $key);
                                    $status = count($parts) > 1 ? true :false;
                                ?>
                                {{-- <th style="width: 100px;" class="{{ $status ? 'text-right' : '' }}">{{ ucfirst(str_replace('|', ' ', $key)) }} </th> --}}
                                <th style="width: 100px;" class="{{ $status ? 'text-right' : '' }}">{{ ucfirst(trim(explode('|', $key)[0])) }}</th>
                                <?php

                                if (count($parts) > 1 && trim($parts[1]) == '1' && --$count['1'] == 0) {
                                    $class = 'text-right'; // Define the class variable
                                    echo '<th style="width: 120px;" class="' . $class . '">Total Addition</th>';
                                }

                                if (count($parts) > 1 && trim($parts[1]) == '2' && --$count['2'] == 0) {
                                    $class = 'text-right';
                                    echo '<th style="width: 120px;" class="' . $class . '">Total Deduction</th>';
                                }
                                ?>
                            @endforeach
                            <th style="width: 100px;" class="text-right">Net Amount</th>
                        @endif
                    </tr>
                </thead>
                <tbody>

                    <?php
                        $grandAdditionTotal = [];
                        $grandDeductionTotal = [];
                        $grandKeyWiseTotal = [];
                    ?>

                    @foreach ($dataset as $data)
                        <?php

                            $count = [
                                '1' => 0,
                                '2' => 0,
                            ];

                            foreach ($dataset[0] as $key=>$val) {
                                $parts = explode('|', $key);
                                    if (count($parts) > 1) {
                                        $secondPart = trim($parts[1]);
                                        if (array_key_exists($secondPart, $count)) {
                                            $count[$secondPart]++;
                                        }
                                    }
                            }

                            // dd($count);
                            $totalAddition = 0;
                            $totalDeduction = 0;

                            // Track the last column indexes for |1 and |2
                            $lastAdditionIndex = -1;
                            $lastDeductionIndex = -1;

                            // Identify the last index positions for |1 and |2
                            $index = 0;
                            foreach ($data as $key=>$val) {
                                $parts = explode('|', $key);
                                if (count($parts) > 1) {
                                    if (trim($parts[1]) == '1') {
                                        $lastAdditionIndex = $index;
                                    } elseif (trim($parts[1]) == '2') {
                                        $lastDeductionIndex = $index;
                                    }
                                }
                                $index++;
                            }
                        ?>

                        <tr>
                            <?php
                            $index = 0;
                            $totalAddition = 0;
                            $totalDeduction = 0;
                            ?>
                            <td>{{ $loop->iteration  }}</td>
                            @foreach ($data as $key =>$value)
                                <?php
                                    $parts = explode('|', $key);
                                    $status = count($parts) > 1 ? true :false;
                                ?>

                                <td class="{{ $status ? 'text-right' : '' }}">{{ $value }}</td>

                                {{-- Calculate totals for additions and deductions --}}
                                <?php
                                $parts = explode('|', $key);


                                if (count($parts) > 1 && trim($parts[1]) == '1') {
                                    $totalAddition += (float) $value;
                                    $grandKeyWiseTotal[1][$parts[0]][] = (float) $value;
                                }

                                if (count($parts) > 1 && trim($parts[1]) == '2') {
                                    $totalDeduction += (float) $value;
                                    $grandKeyWiseTotal[2][$parts[0]][] =  (float) $value;
                                }
                                ?>

                                {{-- Insert Total Addition after the last |1 column --}}
                                @if ($index === $lastAdditionIndex)
                                    <td class="text-right"><strong>{{  $grandAdditionTotal[] = $totalAddition }}</strong></td>
                                @endif

                                {{-- Insert Total Deduction after the last |2 column --}}
                                @if ($index === $lastDeductionIndex)
                                    <td class="text-right"><strong>{{ $grandDeductionTotal[] = $totalDeduction }}</strong></td>
                                @endif

                                <?php $index++; ?>
                            @endforeach

                            <td class="text-right"><strong>{{ $totalAddition - $totalDeduction }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <footer>
                    @if(!empty($dataset) && isset($dataset[0]))
                            <?php
                                $_grandAdditionTotal = array_sum($grandAdditionTotal);
                                $_grandDeductionTotal = array_sum($grandDeductionTotal);
                                $count = [
                                    '1' => 0,
                                    '2' => 0,
                                ];
                                foreach ($dataset[0] as $key => $val){
                                    $parts = explode('|', $key);
                                        if (count($parts) > 1) {
                                            $secondPart = trim($parts[1]);
                                            if (array_key_exists($secondPart, $count)) {
                                                $count[$secondPart]++;
                                            }
                                        }
                                }

                            ?>
                              <th class="text-center">Total</th>
                            @foreach($dataset[0] as $key => $value)
                                {{-- <th style="width: 100px;"></th> --}}
                                <?php


                                $parts = explode('|', $key);
                                $status1 =(count($parts) > 1 && trim($parts[1]) == '1') ? true : false;
                                $status2 =(count($parts) > 1 && trim($parts[1]) == '2') ? true : false;

                                if (!$status1 && !$status2) {
                                    echo "<th style='width: 120px;'></th>";
                                }
                                if (count($parts) > 1) {
                                    $value = 0;

                                    $value = array_sum($grandKeyWiseTotal[$parts[1]][$parts[0]]);
                                    $class = 'text-right'; // Define the class variable
                                    echo "<th style='width: 120px;' class='$class'>$value</th>";
                                }

                                if (count($parts) > 1 && trim($parts[1]) == '1' && --$count['1'] == 0) {
                                    $class = 'text-right'; // Define the class variable
                                    echo "<th style='width: 120px;' class='$class'>$_grandAdditionTotal</th>";
                                }

                                if (count($parts) > 1 && trim($parts[1]) == '2' && --$count['2'] == 0) {
                                    $class = 'text-right';
                                    echo "<th style='width: 120px;'  class='$class'> $_grandDeductionTotal</th>";
                                }
                                ?>
                            @endforeach
                            <th style="width: 100px;" class="text-right">{{ $_grandAdditionTotal -  $_grandDeductionTotal}}</th>
                        @endif
                </footer>

            </table>
        </div>
    </div>
  </div>
</div>
@endsection


@section('script')
<script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('dist/js/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('dist/js/tableToExcel.js')}}"></script>
<script>

    $(document).ready(function($) {

        $(document).on('change','#payment_mode',function(){
            if($(this).val() == 2){
                $("#bank_div").toggle(true)
            }else{
                appendSelect();
                $("#bank_div").toggle(false)
            }
        })

        const appendSelect= () => $("#bank_id").empty();

        $('#department').select2({
            placeholder: 'Enter department',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/depertment_list_data',
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

        $('#location').select2({
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

        $('#section').select2({
            placeholder: 'Enter Sub-department',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/section_list_data',
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

        $('#category').select2({
            placeholder: 'Enter Employee Category',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/category_list_data',
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

        $('#bank_id').select2({
            placeholder: 'Enter Bank',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/get_bank_list',
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

        $('#employee_id').select2({
            placeholder: 'Enter Employee',
            allowClear: true,
            ajax: {
            dataType: 'json',
            url: '{{URL::to('/')}}/employee_list_data',
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


        $(document).on('change',".filter",function(){
            // var month_year = $(this).val();
            var month_year = $("#month_year").val();
            var location = $("#location").val();
            var employee_id = $("#employee_id").val();
            var department = $("#department").val();
            var category = $("#category").val();
            var section = $("#section").val();
            var payment_mode = $("#payment_mode").val();
            var bank_id = $("#bank_id").val();
            $.ajax({
                url:"{{ url('payregister/searchDetails') }}",
                method:"GET",
                data:{
                    month_year : month_year,
                    employee_id : employee_id,
                    department_id : department,
                    location_id : location,
                    section_id : section,
                    category_id : category,
                    payment_mode : payment_mode,
                    bank_id : bank_id,
                },
                success:function(response){
                    $('#result-container').html(response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText); // Log error to console
                }
            });
        })

        $("#printBtn").click(function () {
            let table = document.getElementsByTagName("table");
            TableToExcel.convert(table[0], {
                name: `payRegisterDetails.xlsx`,
                sheet: {
                    name: 'payRegisterDetails'
                }
            });
        });


    });
</script>
@endsection
