@extends('admin.layout')
@section('title', 'Status History')
@section('content')
<h1 class="h3 mb-3">Application Status History</h1>
<div class="table-responsive bg-white border rounded">
    <table class="table table-sm mb-0">
        <thead><tr><th>Time</th><th>Application</th><th>Previous</th><th>New</th><th>Administrator</th></tr></thead>
        <tbody>
        @foreach ($histories as $history)
            <tr>
                <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $history->application?->confirmation_number }}</td>
                <td>{{ $history->previous_status ?? '—' }}</td>
                <td>{{ $history->new_status }}</td>
                <td>{{ $history->changer?->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $histories->links() }}
@endsection