<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Expense Report</h1>
    <p style="text-align: center;">For the period of {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Vendor</th>
                <th>Campus</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->vendor ?? 'N/A' }}</td>
                    <td>{{ $expense->campus->name }}</td>
                    <td class="text-right">${{ number_format($expense->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center;">No expenses found in this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td colspan="4" class="text-right">Total Expenses:</td>
                <td class="text-right">${{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>