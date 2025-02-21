<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand ms-5" href="{{ url('/') }}">Employee Management System</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto me-5">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('employees.index') }}">Employees</a>
      </li>
    </ul>
  </div>
</nav>
<style>
  .navbar-brand:hover, .nav-link:hover {
      color: yellow;
  }
  .nav-link{
    margin-left: 50px;
  }
</style>
