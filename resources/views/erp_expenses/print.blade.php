<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Expense Voucher') }} - {{ $expense->serial_no }}</title>
    <style>
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #555; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td{ border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        @media only screen and (max-width: 600px) { .invoice-box table tr.top table td { width: 100%; display: block; text-align: center; } .invoice-box table tr.information table td { width: 100%; display: block; text-align: center; } }
    </style>
</head>
<body onload="window.print()">

<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{ asset(Storage::url('uploads/logo/logo-dark.png')) }}" style="width:100%; max-width:150px;">
                        </td>
                        <td style="text-align: right;">
                            {{ __('Serial No') }}: {{ $expense->serial_no }}<br>
                            {{ __('Date') }}: {{ \Auth::user()->dateFormat($expense->date) }}<br>
                            {{ __('Type') }}: {{ ucfirst($type) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="4">
                <table>
                    <tr>
                        <td>
                            <strong>{{ __('Issued To') }}:</strong><br>
                            @if($expense->employee)
                                {{ $expense->employee->name }}<br>
                                {{ $expense->employee->designation->name ?? '-' }}<br>
                                {{ $expense->employee->department->name ?? '-' }}
                            @else
                                {{ __('N/A') }}
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <strong>{{ __('Status') }}:</strong> {{ $expense->status }}<br>
                            <strong>{{ __('Payment Status') }}:</strong> {{ $expense->payment_status ?? 'Unpaid' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <strong>{{ __('Details / Description') }}:</strong><br>
                {{ $expense->description ?? '-' }}<br><br>
                <strong>{{ __('Remarks') }}:</strong> {{ $expense->remarks ?? '-' }}
            </td>
        </tr>

        @if($type == 'salary')
            <tr class="heading">
                <td>{{ __('Salary Component') }}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ __('Amount') }}</td>
            </tr>
            <tr class="item">
                <td>{{ __('Net Salary') }}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ \Auth::user()->priceFormat($expense->net_salary) }}</td>
            </tr>
            <tr class="item">
                <td>{{ __('Deduction Amount') }} ({{ $expense->cause_of_deduction ?? '-' }})</td>
                <td></td>
                <td></td>
                <td style="text-align: right; color: red;">-{{ \Auth::user()->priceFormat($expense->deduction_amount) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ __('Total') }}: {{ \Auth::user()->priceFormat($expense->amount) }}</td>
            </tr>
        @elseif($expense->items->count() > 0)
            <tr class="heading">
                <td>{{ __('Item / Description') }}</td>
                <td>{{ __('Quantity') }}</td>
                <td>{{ __('Rate') }}</td>
                <td style="text-align: right;">{{ __('Amount') }}</td>
            </tr>
            @foreach($expense->items as $item)
                <tr class="item">
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \Auth::user()->priceFormat($item->unit_price) }}</td>
                    <td style="text-align: right;">{{ \Auth::user()->priceFormat($item->amount) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ __('Total') }}: {{ \Auth::user()->priceFormat($expense->amount) }}</td>
            </tr>
        @else
            <tr class="heading">
                <td>{{ __('Description') }}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ __('Amount') }}</td>
            </tr>
            <tr class="item last">
                <td>{{ $expense->category->name ?? 'General' }} {{ __('Expense') }}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ \Auth::user()->priceFormat($expense->amount) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{ __('Total') }}: {{ \Auth::user()->priceFormat($expense->amount) }}</td>
            </tr>
        @endif
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <br><br>
        ___________________________<br>
        <strong>{{ __('Authorized Signature') }}</strong><br>
        @if($expense->status == 'Approved' || $expense->status == 'Paid')
            <small>{{ __('Approved By') }}: {{ $expense->approver->name ?? '-' }}</small>
        @endif
    </div>
</div>

</body>
</html>
