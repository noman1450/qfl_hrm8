<!DOCTYPE html>
<html>
<head>
    <title>Individual Leave Ledger</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .page { padding: 30px 36px; }

        /* ── COMPANY ── */
        .company-name  { font-size: 20px; font-weight: bold; }
        .company-address { font-size: 11px; color: #222; margin-top: 2px; }

        /* ── DIVIDER ── */
        .hr { border: none; border-top: 1px solid #222; margin: 5px 0; }

        /* ── DOC TITLE ── */
        .doc-title { font-size: 14px; font-weight: bold; margin-bottom: 6px; }

        /* ── META TABLE ── */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .meta-table td { padding: 1px 0; border: none; vertical-align: top; }
        .meta-label { font-weight: bold; width: 115px; }
        .meta-colon { width: 12px; }

        /* ── EMPLOYEE INFO TABLE ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .info-table td { padding: 2px 0; border: none; vertical-align: top; }
        .info-label { font-weight: bold; width: 110px; }
        .info-sep   { width: 12px; }
        .info-value { width: 160px; }

        /* ── SECTION TITLE ── */
        .section-title { font-weight: bold; font-size: 12.5px; margin: 10px 0 5px 0; }

        /* ══════════════════════════════
           LEAVE SUMMARY TABLE
        ══════════════════════════════ */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .summary-table th {
            font-weight: bold;
            text-align: center;
            padding: 6px 8px;
            border: 1px solid #000;
            background: #efefef;
            font-size: 11.5px;
        }
        .summary-table th.left { text-align: left; }
        .summary-table td {
            padding: 5px 8px;
            border: 1px solid #000;
            font-size: 11.5px;
            vertical-align: top;
        }
        .summary-table td.center { text-align: center; }
        .summary-total td {
            font-weight: bold;
            background: #f5f5f5;
            border-top: 1.5px solid #555;
        }

        /* ══════════════════════════════
           LEAVE DETAILS TABLE
        ══════════════════════════════ */
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table th {
            font-weight: bold;
            text-align: center;
            padding: 6px 8px;
            border: 1px solid #000;
            background: #efefef;
            font-size: 11px;
        }
        .details-table th.left { text-align: left; }
        .details-table td {
            padding: 5px 8px;
            border: 1px solid #000;
            font-size: 11px;
            vertical-align: top;
            text-align: center;
        }
        .details-table td.left { text-align: left; }
        .no-data-td {
            text-align: center;
            color: #c00;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── COMPANY HEADER ── --}}
    <div class="company-name">{{ $company_name }}</div>
    <div class="company-address">{{ $company_address }}</div>

    {{-- ── DOC TITLE ── --}}
    <div class="doc-title">Individual Leave Ledger</div>

    {{-- ── META ── --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Leave Year</td>
            <td class="meta-colon">:</td>
            <td><strong>{{ $hrm_leave_years->date_from ?? '' }} - {{ $hrm_leave_years->date_to ?? '' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Job Location</td>
            <td class="meta-colon">:</td>
            <td>{{ $employee->location_name ?? '' }}</td>
        </tr>
    </table>

    <div class="hr"></div>

    {{-- ── EMPLOYEE INFO ── --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Name</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $employee->employee_name ?? '' }}</td>
            <td class="info-label">Employee Code</td>
            <td class="info-sep">:</td>
            <td>{{ $employee->employee_code ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Department</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $employee->depertment_name ?? '' }}</td>
            <td class="info-label">Joining Date</td>
            <td class="info-sep">:</td>
            <td>{{ $employee->joining_date ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Designation</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $employee->designation_name ?? '' }}</td>
            <td class="info-label">Job Confirm</td>
            <td class="info-sep">:</td>
            <td>{{ $employee->confirmation_date ?? '—' }}</td>
        </tr>
    </table>

    <div class="hr"></div>

    {{-- ══════════════════════════════════════════════ --}}
    {{--              LEAVE SUMMARY TABLE               --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div class="section-title">Leave Summary</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th class="left" style="width:40%">Leave Type</th>
                <th style="width:20%">Total Allocated</th>
                <th style="width:20%">Used</th>
                <th style="width:20%">Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employeeLeaveDetails as $leave)
            <tr>
                <td>{{ $leave->leave_type }}</td>
                <td class="center">{{ number_format($leave->total_leave, 2) }}</td>
                <td class="center">{{ number_format($leave->used, 2) }}</td>
                <td class="center">{{ number_format($leave->balance, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr >
                <td> <strong>Total</strong></td>
                <td class="center"><strong>{{ number_format(collect($employeeLeaveDetails)->sum('total_leave'), 2) }}</strong></td>
                <td class="center"><strong>{{ number_format(collect($employeeLeaveDetails)->sum('used'), 2) }}</strong></td>
                <td class="center"><strong>{{ number_format(collect($employeeLeaveDetails)->sum('balance'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- ══════════════════════════════════════════════ --}}
    {{--              LEAVE DETAILS TABLE               --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div class="section-title">Leave Details</div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Date From</th>
                <th>Date To</th>
                <th>Duration</th>
                <th>Leave Name</th>
                <th class="left">Comment</th>
                <th>Payment Mode</th>
                <th class="left">Boss Comment</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($details as $dLeave)
            <tr>
                <td>{{ $dLeave->date_from }}</td>
                <td>{{ $dLeave->date_to }}</td>
                <td>{{ $dLeave->days }}</td>
                <td>{{ $dLeave->leave_type }}</td>
                <td class="left">{{ $dLeave->comment }}</td>
                <td>{{ $dLeave->payment_mode }}</td>
                <td class="left">{{ $dLeave->boss_note }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="no-data-td">No data found!</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
</body>
</html>
