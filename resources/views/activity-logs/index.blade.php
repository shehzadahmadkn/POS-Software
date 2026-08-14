<x-app-layout>
    <x-slot name="header">
        <h4 class="mb-sm-0">System Activity Log</h4>
    </x-slot>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Module</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            @if($activity->causer)
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                {{ substr($activity->causer->name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2 name">
                                                        {{ $activity->causer->name }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge @if($activity->event == 'created') bg-success @elseif($activity->event == 'updated') bg-warning text-dark @elseif($activity->event == 'deleted') bg-danger @else bg-info @endif">
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $modelName = class_basename($activity->subject_type);
                                                $modelId = $activity->subject_id;
                                            @endphp
                                            {{ $modelName }} #{{ $modelId }}
                                        </td>
                                        <td>
                                            @if($activity->event == 'updated')
                                                <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="collapse" data-bs-target="#details-{{ $activity->id }}">
                                                    View Changes
                                                </button>
                                                <div class="collapse mt-2" id="details-{{ $activity->id }}">
                                                    <pre class="bg-light p-2 rounded border" style="max-height: 150px; overflow-y: auto;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No activity recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
