@extends('layouts.admin')

@section('title', 'System Activity Logs')
@section('page_title', 'Activity Audit Trail')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Activity Audit Trail</h4>
            <small class="text-muted">Security and action audit logs of user activities across modules</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted small">{{ $log->created_at->format('d M Y, h:i:s A') }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $log->user?->name ?? 'System' }}</span>
                            <small class="text-muted d-block">{{ $log->user?->role ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $log->action }}</span></td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $log->module }}</span></td>
                        <td>{{ $log->description ?: '-' }}</td>
                        <td><small class="font-monospace text-muted">{{ $log->ip_address ?: '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No activity logs recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
