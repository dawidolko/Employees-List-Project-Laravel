<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Export</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h2 class="text-center">Employee Export</h2>
    <table class="table">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Title</th>
                <th>Current Salary</th>
                <th>Total Salaries</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->first_name }}</td>
                <td>{{ $employee->last_name }}</td>
                <td>{{ optional($employee->currentDepartment)->dept_name }}</td>
                <td>{{ optional($employee->currentTitle)->title }}</td>
                <td>{{ optional($employee->currentSalary)->salary }}</td>
                <td>{{ $employee->salaries->sum('salary') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
