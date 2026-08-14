<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <!-- Bootstrap Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-white">
    <div class="container-fluid py-3">
        @if($activities->isEmpty())
            <div class="alert alert-info">
                No activity recorded for this purchase yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Action</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr>
                                <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <span class="badge @if($activity->event == 'created') bg-success @elseif($activity->event == 'updated') bg-warning text-dark @else bg-danger @endif">
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                </td>
                                <td>{{ $activity->causer ? $activity->causer->name : 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
