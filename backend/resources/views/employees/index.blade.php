@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Employee Directory</h1>
    
    <form action="{{ route('employees.index') }}" method="GET" class="mb-4" id="filterForm">
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
                <label>Salary Min </label>
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

        <div class="mt-3 d-flex align-items-center">
            <div class="mr-3" style="margin-right: 10px;">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="mr-3" style="margin-right: 10px;">
                <a href="{{ route('employees.export.pdf', request()->query()) }}" class="btn btn-success export-btn" data-export-type="PDF">Export PDF</a>
            </div>
            <div class="mr-3" style="margin-right: 15px;">
                <a href="{{ route('employees.export.csv', request()->query()) }}" class="btn btn-info export-btn" data-export-type="CSV">Export CSV</a>
            </div>
            <div class="ml-auto text-light">
                Liczba pracowników: {{ $employees->total() }}
            </div>
        </div>
    </form>
    
    <table class="table table-bordered table-dark mt-4">
        <thead>
            <tr>
                <th style="width: 50px;">
                    <input type="checkbox" id="select-all" checked>
                </th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Title</th>
                <th>Current Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr onclick="if(event.target.type !== 'checkbox') window.location.href='{{ route('employees.show', $employee->emp_no) }}'" style="cursor: pointer;">
                <td>
                    <input type="checkbox" class="employee-checkbox" name="selected_ids[]" value="{{ $employee->emp_no }}" checked onclick="event.stopPropagation();">
                </td>
                <td>{{ $employee->first_name }}</td>
                <td>{{ $employee->last_name }}</td>
                <td>{{ optional($employee->currentDepartment)->dept_name }}</td>
                <td>{{ optional($employee->currentTitle)->title }}</td>
                <td>
                    @if(optional($employee->currentSalary)->salary)
                        {{ $employee->currentSalary->salary }}
                    @elseif($employee->salaries && $employee->salaries->isNotEmpty())
                        @php
                            $lastSalary = $employee->salaries->sortByDesc('to_date')->first();
                        @endphp
                        {{ $lastSalary->salary }} <small>({{ $lastSalary->to_date }})</small>
                    @else
                        -
                    @endif
                </td>
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

<div id="customToast" style="position: fixed; bottom: 1rem; right: 1rem; z-index: 1050; background-color: #343a40; color: #fff; border-radius: 4px; padding: 1rem; display: none; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
    <div id="toastHeader" style="font-weight: bold; margin-bottom: 0.5rem;"></div>
    <div id="toastMessage" style="margin-bottom: 0.5rem;"></div>
    <div style="width: 100%; background-color: #6c757d; border-radius: 4px;">
        <div id="toastProgress" style="width: 0%; background-color: #28a745; color: #fff; text-align: center; padding: 0.2rem 0; border-radius: 4px;">0%</div>
    </div>
    <button id="toastClose" style="margin-top: 0.5rem; background-color: #dc3545; color: #fff; border: none; padding: 0.3rem 0.5rem; border-radius: 4px; cursor: pointer;">Close</button>
</div>
@endsection

@section('scripts')
<style>
@media (max-width: 500px) {
    .row {
        display: none;
    }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function showToast(header, message) {
        const toast = document.getElementById('customToast');
        document.getElementById('toastHeader').textContent = header;
        document.getElementById('toastMessage').textContent = message;
        toast.style.display = 'block';
    }
    function hideToast() {
        document.getElementById('customToast').style.display = 'none';
    }
    document.getElementById('toastClose').addEventListener('click', hideToast);

    function restoreCheckboxState(checkbox) {
        let key = "employee_checkbox_" + checkbox.value;
        let storedValue = localStorage.getItem(key);
        if (storedValue !== null) {
            checkbox.checked = (storedValue === "true");
        } else {
            checkbox.checked = true;
        }
    }

    const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
    employeeCheckboxes.forEach(function(checkbox) {
        restoreCheckboxState(checkbox);
        checkbox.addEventListener('change', function() {
            let key = "employee_checkbox_" + checkbox.value;
            localStorage.setItem(key, checkbox.checked);
            let allOnPage = Array.from(employeeCheckboxes).every(cb => cb.checked);
            document.getElementById('select-all').checked = allOnPage;
        });
    });

    document.getElementById('select-all').addEventListener('change', function(event){
        employeeCheckboxes.forEach(function(chk) {
            chk.checked = event.target.checked;
            localStorage.setItem("employee_checkbox_" + chk.value, event.target.checked);
        });
    });

    const exportButtons = document.querySelectorAll('.export-btn');
    exportButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const exportType = this.getAttribute('data-export-type');
            const exportUrl = this.getAttribute('href');
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);

            if(document.getElementById('select-all').checked) {
                params.set('all_selected', '1');
                let deselected = [];
                for (let i = 0; i < localStorage.length; i++) {
                    let key = localStorage.key(i);
                    if (key.startsWith("employee_checkbox_")) {
                        let value = localStorage.getItem(key);
                        if (value === "false") {
                            let empId = key.replace("employee_checkbox_", "");
                            deselected.push(empId);
                        }
                    }
                }
                deselected.forEach(id => {
                    params.append('deselected_ids[]', id);
                });
            } else {
                let selected = [];
                for (let i = 0; i < localStorage.length; i++) {
                    let key = localStorage.key(i);
                    if (key.startsWith("employee_checkbox_")) {
                        let value = localStorage.getItem(key);
                        if (value === "true") {
                            let empId = key.replace("employee_checkbox_", "");
                            selected.push(empId);
                        }
                    }
                }
                selected.forEach(id => {
                    params.append('selected_ids[]', id);
                });
            }

            showToast(exportType + " Export", exportType + " export initiated.");
            const progressBar = document.getElementById('toastProgress');
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';

            function simulateProgress(callback) {
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 5;
                    if (progress > 100) progress = 100;
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = progress + '%';
                    if (progress >= 100) {
                        clearInterval(interval);
                        callback();
                    }
                }, 100);
            }

            if (exportType === 'PDF') {
                fetch("{{ route('employees.export.pdf.check') }}?" + params.toString())
                    .then(response => {
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        simulateProgress(() => {
                            window.location.href = exportUrl + '?' + params.toString();
                        });
                    })
                    .catch(err => {
                        const errorMsg = err.message || "Błąd podczas sprawdzania możliwości eksportu PDF.";
                        showToast("Error", errorMsg);
                        progressBar.style.width = '0%';
                        progressBar.textContent = '0%';
                    });
            } else { // CSV
                simulateProgress(() => {
                    window.location.href = exportUrl + '?' + params.toString();
                });
            }
        });
    });
});
</script>
@endsection
