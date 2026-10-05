<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>daily_task_report</title>

        <link rel="stylesheet" href="{{ asset('/admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@400;600&display=swap" rel="stylesheet">

        <style>
            p {
                margin: 0;
                font-size: 13px;
            }
            .bengoli {
                font-family: 'Noto Serif Bengali', serif;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
            }
            .table-bordered>thead>tr>th,
            .table-bordered>tbody>tr>th,
            .table-bordered>thead>tr>td,
            .table-bordered>tbody>tr>td {
                border: 1px solid #666;
                font-size: 14px;
                text-align: center;
            }
            .no-border-in-print {
                border: 1px solid;
                padding: 50px;
                margin-bottom: 20px;
            }
            @media print {
                .no-border-in-print {
                    border: none;
                    margin-bottom: 0;
                }
                @page {
                    margin: 2.2cm .5cm 2cm 2cm;
                    padding: 0
                }
                .break_section {
                    page-break-after: always !important;
                }
            }
            .amount, .in-words {
                font-weight: 600;
                padding: 0 15px;
                border-bottom: 1px dashed;
                line-height: 1.5;
            }
            .explanation-of-diference span {
                padding: 0 15px;
                border-bottom: 1px dashed;
                line-height: 1.8;
            }
        </style>
    </head>
    <body>

        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 30px">
                <div>
                    <strong>Employee Name :</strong>
                    <span>{{ $employee->employee_name }}</span>
                </div>

                <div>
                    <strong>Department Name :</strong>
                    <span>{{ $employee->depertment_name }}</span>
                </div>

                <div>
                    <strong>Designation Name :</strong>
                    <span>{{ $employee->designation_name }}</span>
                </div>

                <div>
                    <strong>Report for :</strong>
                    <span>{{ $from_date .' To '. $to_date }}</span>
                </div>
            </div>

            <div class="col-xs-12 do-print">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%">SL</th>
                            <th style="width: 35%">Task Name</th>
                            <th style="width: 20%">Assigned Date</th>
                            <th style="width: 20%">Complete Date</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 10%">Priority</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($dailyCompletedTasks as $item)
                            <tr>
                                <td>{{ $loop->index+1 }}</td>
                                <td>{{ $item->task_name }}</td>
                                <td>{{ $item->assign_date }}</td>
                                <td>{{ $item->complete_date }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->priority }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="col-md-2"></div> --}}
        </div>
    </body>
</html>
