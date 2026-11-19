@extends('admin.layout')
@section('title', 'Locations')
@section('content')
<h1 class="h3 mb-3">Locations</h1>
<div class="table-responsive bg-white border rounded">
    <table class="table table-sm mb-0">
        <thead><tr><th>Display name</th><th>Country</th><th>City</th><th>Active</th><th></th></tr></thead>
        <tbody>
        @foreach ($locations as $location)
            <tr>
                <td>{{ $location->display_name }}</td>
                <td>{{ $location->country }}</td>
                <td>{{ $location->city }}</td>
                <td>{{ $location->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <form method="post" action="{{ route('admin.locations.update', $location) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $location->is_active ? 0 : 1 }}">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $location->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $locations->links() }}
@endsection