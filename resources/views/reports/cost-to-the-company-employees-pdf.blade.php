<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cost to the Company Employee Details</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #263238;
        }
        .company-name {
            color: #173b57;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .company-address { color: #596b78; font-size: 8px; }
        .report-heading {
            border-bottom: 2px solid #173b57;
            margin: 10px 0 8px;
            padding-bottom: 7px;
        }
        .report-title { font-size: 13px; font-weight: bold; color: #173b57; }
        .report-subtitle { color: #596b78; margin-top: 3px; }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th {
            background: #173b57;
            color: #fff;
            font-size: 7px;
            font-weight: bold;
            padding: 7px 4px;
            text-align: center;
            border: 1px solid #173b57;
        }
        td {
            border: 1px solid #d4dde3;
            padding: 5px 4px;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) { background: #f2f6f8; }
        td.center { text-align: center; }
        td.left { text-align: left; }
        td.amount { text-align: right; white-space: nowrap; }
        tr.total-row td {
            background: #dce9f0;
            border-top: 2px solid #173b57;
            color: #173b57;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="company-name">{{ $company_name }}</div>
    <div class="company-address">{{ $company_address }}</div>

    <div class="report-heading">
        <div class="report-title">Cost to the Company — Employee Details</div>
        <div class="report-subtitle">
            {{ $row_title }} &nbsp; | &nbsp; From: {{ $month_from }} &nbsp; To: {{ $month_to }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:3%">Sl.</th>
                <th style="width:7%">Employee ID</th>
                <th style="width:12%">Employee Name</th>
                <th style="width:12%">Designation</th>
                <th style="width:9%">Gross Salary</th>
                <th style="width:10%">Two Festival Bonus</th>
                <th style="width:8%">PF Contribution</th>
                <th style="width:12%">Out of Pocket &amp; Transport</th>
                <th style="width:8%">House Rent</th>
                <th style="width:8%">Fixed Allowance</th>
                <th style="width:11%">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $index => $employee)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $employee['employee_id'] }}</td>
                    <td class="left">{{ $employee['employee_name'] }}</td>
                    <td class="left">{{ $employee['designation_name'] }}</td>
                    <td class="amount">{{ number_format((float) $employee['gross_salary'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['two_festival_bonus'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['pf_contribution'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['transport_outOfPocket'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['houseRent_allowance'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['fixed_allowance'], 2) }}</td>
                    <td class="amount">{{ number_format((float) $employee['total_cost'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td></td>
                <td></td>
                <td class="left">TOTAL</td>
                <td></td>
                <td class="amount">{{ number_format((float) $totals['gross_salary'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['two_festival_bonus'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['pf_contribution'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['transport_outOfPocket'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['houseRent_allowance'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['fixed_allowance'], 2) }}</td>
                <td class="amount">{{ number_format((float) $totals['total_cost'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
