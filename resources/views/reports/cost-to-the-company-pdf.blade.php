<!DOCTYPE html>
<html>
<head>
    <title>Cost to the Company Location</title>
    <style>
        @page {
            margin: 20mm 15mm 0.5in 15mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #111;
        }

        .page { padding: 10px 20px 0.4in; }

        .company-name   { font-size: 14px; font-weight: bold; }
        .company-address { font-size: 9px; color: #222; margin-top: 2px; }
        .doc-title      { font-size: 12px; font-weight: bold; text-align: center; margin: 8px 0 2px 0; }
        .doc-subtitle   { font-size: 9px; text-align: center; margin-bottom: 2px; }
        .hr { border: none; border-top: 1px solid #222; margin: 2px 0; }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.report-table thead tr.page-top td {
            border: none;
            padding-top: 3px;
            background: transparent;
        }
        table.report-table th {
            font-weight: bold;
            text-align: center;
            padding: 1.5px 5px;
            border: 1px solid #000;
            background: #ededed;
            font-size: 8px;
        }
        table.report-table th.left { text-align: left; }
        table.report-table td {
            padding: 1.5px 6px;
            border: 1px solid #000;
            font-size: 8px;
            vertical-align: top;
        }
        table.report-table td.left { text-align: left; }
        table.report-table td.center { text-align: center; }
        table.report-table td.right { text-align: right; }

        table.report-table tr.total-row td {
            font-weight: bold;
            background: #f5f5f5;
            padding: 1.5px 4px;
        }
        table.report-table tr.group-row td {
            font-weight: bold;
            background: #eef2fb;
        }
    </style>
</head>
<body>
<div class="page">

    <div class="company-name">{{ $company_name }}</div>
    <div class="company-address">{{ $company_address }}</div>

    <div class="hr"></div>

    <div class="doc-title">Cost to the Company (Employee Segment Wise)</div>
    <div class="doc-subtitle">From: {{ $month_from }} &nbsp;&nbsp;To: {{ $month_to }}</div>

    @php
        $groups = [];
        $order = [];
        $totalEmployees = 0;
        $totalGross     = 0;
        $totalBonus     = 0;
        $totalPF        = 0;
        $totalTransport = 0;
        $totalHouseRent = 0;
        $totalFixed     = 0;
        $totalCost      = 0;

        foreach ($data as $row) {
            if (!isset($groups[$row->level_name])) {
                $groups[$row->level_name] = [
                    'rows' => [],
                    'employees' => 0, 'gross' => 0, 'bonus' => 0, 'pf' => 0,
                    'transport' => 0, 'house_rent' => 0, 'fixed' => 0, 'cost' => 0,
                ];
                $order[] = $row->level_name;
            }

            $groups[$row->level_name]['rows'][] = $row;
            $groups[$row->level_name]['employees']  += $row->NoOfEmployee;
            $groups[$row->level_name]['gross']      += $row->gross_salary;
            $groups[$row->level_name]['bonus']      += $row->two_festival_bonus;
            $groups[$row->level_name]['pf']         += $row->pf_contribution;
            $groups[$row->level_name]['transport']  += $row->transport_outOfPocket;
            $groups[$row->level_name]['house_rent'] += $row->houseRent_allowance;
            $groups[$row->level_name]['fixed']      += $row->fixed_allowance;
            $groups[$row->level_name]['cost']       += $row->existing_cost;

            $totalEmployees += $row->NoOfEmployee;
            $totalGross     += $row->gross_salary;
            $totalBonus     += $row->two_festival_bonus;
            $totalPF        += $row->pf_contribution;
            $totalTransport += $row->transport_outOfPocket;
            $totalHouseRent += $row->houseRent_allowance;
            $totalFixed     += $row->fixed_allowance;
            $totalCost      += $row->existing_cost;
        }
        $sl = 0;
    @endphp

    <table class="report-table">
        <thead>
            <tr class="page-top">
                <td colspan="10">&nbsp;</td>
            </tr>
            <tr>
                <th style="width:5%">Sl.</th>
                <th class="left" style="width:24%">Details</th>
                <th style="width:9%">No. of Employees</th>
                <th style="width:10%">Gross Salary</th>
                <th style="width:10%">Two Festival Bonus</th>
                <th style="width:9%">PF Com. Contribution</th>
                <th style="width:11%">Out of Pocket &amp; Transport</th>
                <th style="width:9%">House Rent</th>
                <th style="width:9%">Fixed Allowance</th>
                <th style="width:11%">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order as $levelName)
                @php
                    $sl++;
                    $g = $groups[$levelName];
                @endphp
                <tr class="group-row">
                    <td class="center">{{ $sl }}</td>
                    <td class="left">{{ $levelName }}</td>
                    <td class="center">{{ number_format($g['employees']) }}</td>
                    <td class="right">{{ number_format($g['gross']) }}</td>
                    <td class="right">{{ number_format($g['bonus']) }}</td>
                    <td class="right">{{ number_format($g['pf']) }}</td>
                    <td class="right">{{ number_format($g['transport']) }}</td>
                    <td class="right">{{ number_format($g['house_rent']) }}</td>
                    <td class="right">{{ number_format($g['fixed']) }}</td>
                    <td class="right">{{ number_format($g['cost']) }}</td>
                </tr>

                @foreach ($g['rows'] as $row)
                <tr>
                    <td class="center"></td>
                    <td class="left">{{ $row->location_name }}</td>
                    <td class="center">{{ number_format($row->NoOfEmployee) }}</td>
                    <td class="right">{{ number_format($row->gross_salary) }}</td>
                    <td class="right">{{ number_format($row->two_festival_bonus) }}</td>
                    <td class="right">{{ number_format($row->pf_contribution) }}</td>
                    <td class="right">{{ number_format($row->transport_outOfPocket) }}</td>
                    <td class="right">{{ number_format($row->houseRent_allowance) }}</td>
                    <td class="right">{{ number_format($row->fixed_allowance) }}</td>
                    <td class="right">{{ number_format($row->existing_cost) }}</td>
                </tr>
                @endforeach
            @endforeach

            <tr class="total-row">
                <td class="center"></td>
                <td class="left">TOTAL</td>
                <td class="center">{{ number_format($totalEmployees) }}</td>
                <td class="right">{{ number_format($totalGross) }}</td>
                <td class="right">{{ number_format($totalBonus) }}</td>
                <td class="right">{{ number_format($totalPF) }}</td>
                <td class="right">{{ number_format($totalTransport) }}</td>
                <td class="right">{{ number_format($totalHouseRent) }}</td>
                <td class="right">{{ number_format($totalFixed) }}</td>
                <td class="right">{{ number_format($totalCost) }}</td>
            </tr>
        </tbody>
    </table>

</div>
</body>
</html>