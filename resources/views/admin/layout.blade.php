<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Visa Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .demo-banner{background:#fff3cd;border-bottom:1px solid #eadca3;font-size:.85rem}
        .sidebar{min-height:calc(100vh - 56px)}
        .nav-link.active{font-weight:600}
    </style>
</head>
<body class="bg-light">
<div class="demo-banner text-center py-1">UNOFFICIAL DEMO — NOT VALID FOR TRAVEL</div>
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Visa Tracking Admin</a>
        <span class="navbar-text text-white-50 d-none d-md-inline">Demonstration only</span>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 border-end bg-white sidebar py-3">
            <nav class="nav flex-column gap-1 px-2">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}">Visa Applications</a>
                <a class="nav-link" href="{{ route('admin.applications.create') }}">Add Visa Application</a>
                <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">Locations</a>
                <a class="nav-link {{ request()->routeIs('admin.status-histories.*') ? 'active' : '' }}" href="{{ route('admin.status-histories.index') }}">Application Status History</a>
                <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}">Profile</a>
                <form method="post" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Logout</button>
                </form>
            </nav>
        </aside>
        <main class="col-md-9 col-lg-10 py-4 px-3">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>