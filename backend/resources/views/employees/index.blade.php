@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Employee Directory</h1>
    
    <form action="{{ route('employees.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>Current</option>
                    <option value="former" {{ request('status') == 'former' ? 'selected' : '' }}>Former</option>
                </select>
            </div>
            <div class="col">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">All</option>
                    <option value="M" {{ request('gender') == 'M' ? 'selected' : '' }}>Male</option>
                    <option value="F" {{ request('gender') == 'F' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col">
                <label>Salary Min</label>
                <input type="number" name="salary_min" class="form-control" value="{{ request('salary_min') }}" min="0">
            </div>
            <div class="col">
                <label>Salary Max</label>
                <input type="number" name="salary_max" class="form-control" value="{{ request('salary_max') }}" min="0">
            </div>
            <div class="col">
                <label>Department</label>
                <select name="department" class="form-control">
                    <option value="">All</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->dept_no }}" {{ request('department') == $dept->dept_no ? 'selected' : '' }}>
                            {{ $dept->dept_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('employees.export.pdf', request()->query()) }}" class="btn btn-success export-btn" data-export-type="PDF">Export PDF</a>
            <a href="{{ route('employees.export.csv', request()->query()) }}" class="btn btn-info export-btn" data-export-type="CSV">Export CSV</a>
        </div>
    </form>
    
    <table class="table table-bordered table-dark">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Title</th>
                <th>Current Salary</th>
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
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            {{ $employees->onEachSide(1)->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>    
     
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="exportToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.pagination-container {
    max-width: 300px;
    width: 90%;
}
@media (max-width: 500px) {
    .row{
        display: none;
    }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var exportButtons = document.querySelectorAll('.export-btn');
    exportButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            var exportType = e.currentTarget.getAttribute('data-export-type');
            var toastTitle = exportType + " Export";
            var toastBody = exportType + " export initiated.";
            document.getElementById('toastTitle').textContent = toastTitle;
            document.getElementById('toastBody').textContent = toastBody;
            var toastElement = document.getElementById('exportToast');
            var toastInstance = bootstrap.Toast.getInstance(toastElement);
            if (!toastInstance) {
                toastInstance = new bootstrap.Toast(toastElement, { delay: 3000 });
            }
            toastInstance.show();
        });
    });
});
</script>
@endsection
