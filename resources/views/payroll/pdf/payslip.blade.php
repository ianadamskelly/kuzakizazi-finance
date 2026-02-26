<!DOCTYPE html>
<html>
<head>
    <title>Payslip</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 0; }
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { padding: 5px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 8px; }
        .summary-table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-row td { border-top: 2px solid #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EduFinance Pro</h1>
            <p>Payslip for {{ date('F Y', mktime(0, 0, 0, $payslip->month, 1, $payslip->year)) }}</p>
        </div>

        <table class="details-table">
            <tr>
                <td><strong>Employee:</strong></td>
                <td>{{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</td>
                <td><strong>Job Title:</strong></td>
                <td>{{ $payslip->employee->job_title }}</td>
            </tr>
            <tr>
                <td><strong>Campus:</strong></td>
                <td>{{ $payslip->employee->campus->name ?? 'All Campuses' }}</td>
                <td><strong>Salary Category:</strong></td>
                <td>{{ $payslip->employee->employeeCategory->name }}</td>
            </tr>
        </table>

        <table class="summary-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="2" class="font-bold">Earnings</td></tr>
                <tr>
                    <td>Base Salary</td>
                    <td class="text-right">${{ number_format($payslip->base_salary, 2) }}</td>
                </tr>
                @foreach($payslip->items->where('type', 'earning') as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold">
                    <td>Total Earnings</td>
                    <td class="text-right">${{ number_format($payslip->total_earnings, 2) }}</td>
                </tr>

                <tr><td colspan="2" class="font-bold">Deductions</td></tr>
                @forelse($payslip->items->where('type', 'deduction') as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">-${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td>No deductions</td><td class="text-right">$0.00</td></tr>
                @endforelse
                <tr class="font-bold">
                    <td>Total Deductions</td>
                    <td class="text-right">-${{ number_format($payslip->total_deductions, 2) }}</td>
                </tr>
                
                <tr class="font-bold total-row">
                    <td>Net Pay</td>
                    <td class="text-right">${{ number_format($payslip->net_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
