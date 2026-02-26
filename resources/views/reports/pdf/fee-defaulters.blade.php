<!DOCTYPE html>
<html>
<head>
    <title>Fee Defaulters Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: red; }
    </style>
</head>
<body>
    <h1>Fee Defaulters Report</h1>
    <p style="text-align: center;">
        @if(isset($selectedGrade) && $selectedGrade)
            Grade: {{ $selectedGrade->name }} | 
        @endif
        Generated on: {{ date('M d, Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Admission No.</th>
                <th>Grade</th>
                <th>Campus</th>
                <th class="text-center">Overdue Invoices</th>
                <th class="text-right">Total Balance Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($defaulters as $defaulter)
                <tr>
                    <td>{{ $defaulter['student']->first_name }} {{ $defaulter['student']->last_name }}</td>
                    <td>{{ $defaulter['student']->admission_number }}</td>
                    <td>{{ $defaulter['student']->grade->name ?? 'N/A' }}</td>
                    <td>{{ $defaulter['student']->campus->name }}</td>
                    <td class="text-center">{{ $defaulter['invoice_count'] }}</td>
                    <td class="text-right font-bold text-red">${{ number_format($defaulter['total_balance'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center;">No fee defaulters found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>