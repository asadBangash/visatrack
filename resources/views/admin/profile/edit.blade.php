@extends('admin.layout')
@section('title', 'Profile')
@section('content')
<h1 class="h3 mb-3">Profile</h1>
<form method="post" action="{{ route('admin.profile.update') }}" class="card card-body shadow-sm" style="max-width:480px">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">New password</label>
        <input class="form-control" type="password" name="password" id="password">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password_confirmation">Confirm password</label>
        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation">
    </motion-div>
    <button class="btn btn-primary" type="submit">Save</button>
</form>
@endsection