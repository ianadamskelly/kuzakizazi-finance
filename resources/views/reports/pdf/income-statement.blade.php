<!DOCTYPE html>
<html>
<head>
    <title>Income Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; text-align: center; }
        h2 { font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .item-table td, .item-table th { padding: 5px; }
        .summary-table td { padding: 5px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-green { color: green; }
        .text-red { color: red; }
        .border-top { border-top: 1px solid #333; }
        .border-top-double { border-top: 3px double #333; }
    </style>
</head>
<body>
    <h1>Income Statement</h1>
    <p style="text-align: center;">For the period of {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>

    <h2>Revenue</h2>
    <table class="summary-table">
        <tr>
            <td>Total Payments Received</td>
            <td class="text-right font-bold text-green">${{ number_format($revenue, 2) }}</td>
        </tr>
    </table>

    <h2 style="margin-top: 20px;">Expenses</h2>
    <table class="item-table">
        @forelse($expenses as $expense)
            <tr>
                <td>{{ $expense->category }}</td>
                <td class="text-right">${{ number_format($expense->total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="2">No expenses recorded in this period.</td></tr>
        @endforelse
    </table>
    <table class="summary-table" style="margin-top: 10px;">
        <tr class="font-bold border-top">
            <td>Total Expenses</td>
            <td class="text-right text-red">${{ number_format($totalExpenses, 2) }}</td>
        </tr>
    </table>

    <table class="summary-table" style="margin-top: 20px;">
        <tr class="font-bold text-lg border-top-double">
            <td>Net Profit</td>
            <td class="text-right @if($netProfit < 0) text-red @else text-green @endif">
                ${{ number_format($netProfit, 2) }}
            </td>
        </tr>
    </table>
</body>
</html>