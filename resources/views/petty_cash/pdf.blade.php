<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Petty Cash Report') }} - {{ $allocation->month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .summary-box th, .summary-box td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .summary-box th {
            background-color: #f5f5f5;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>{{ env('APP_NAME', 'Application') }}</h2>
    <h3>{{ __('Petty Cash Report') }} - {{ date('F Y', strtotime($allocation->month . '-01')) }}</h3>
    <p>{{ __('Allocated By') }}: {{ !empty($allocation->admin) ? $allocation->admin->name : '-' }}</p>
</div>

<table class="summary-box">
    <tr>
        <th>{{ __('Allocated Amount') }}</th>
        <th>{{ __('Rollover Amount') }}</th>
        <th>{{ __('Total Fund') }}</th>
        <th>{{ __('Used Amount') }}</th>
        <th>{{ __('Remaining Balance') }}</th>
    </tr>
    <tr>
        <td>{{ number_format($allocation->allocated_amount, 2) }}</td>
        <td>{{ number_format($allocation->rollover_amount, 2) }}</td>
        <td>{{ number_format($allocation->total_amount, 2) }}</td>
        <td>{{ number_format($allocation->used_amount, 2) }}</td>
        <td>{{ number_format($allocation->total_amount - $allocation->used_amount, 2) }}</td>
    </tr>
</table>

<h4>{{ __('Usage Details') }}</h4>
@if(count($allocation->usages) > 0)
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Purpose') }}</th>
            <th>{{ __('Used By') }}</th>
            <th class="text-right">{{ __('Amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @php $total = 0; @endphp
        @foreach($allocation->usages as $index => $usage)
            @php $total += $usage->amount; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('Y-m-d', strtotime($usage->date)) }}</td>
                <td>{{ $usage->purpose }}</td>
                <td>{{ !empty($usage->user) ? $usage->user->name : '-' }}</td>
                <td class="text-right">{{ number_format($usage->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th colspan="4" class="text-right">{{ __('Total Used') }}</th>
            <th class="text-right">{{ number_format($total, 2) }}</th>
        </tr>
        </tfoot>
    </table>
@else
    <p>{{ __('No usages recorded for this month.') }}</p>
@endif

<div class="footer">
    {{ __('Generated on') }} {{ date('Y-m-d H:i:s') }}
</div>

</body>
</html>
