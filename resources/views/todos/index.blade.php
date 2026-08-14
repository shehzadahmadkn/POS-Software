<x-app-layout>
    @push('styles')
    <style>
        .todo-item.completed .todo-text {
            text-decoration: line-through;
            color: #adb5bd;
        }
        .todo-item {
            transition: all 0.3s ease;
        }
        .todo-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    @endpush

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Todo List</h5>
                        </div>
                        <div class="col-sm-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="ri-add-line align-bottom me-1"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Tasks List -->
                    <div class="mt-2">
                        @if($todos->isEmpty())
                            <div class="text-center p-5">
                                <div class="avatar-lg mx-auto mb-4">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                </div>
                                <h5>No tasks found!</h5>
                                <p class="text-muted">Create a new task to get started.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($todos as $todo)
                                    <div class="list-group-item todo-item border-start-0 border-end-0 py-3 {{ $todo->is_completed ? 'completed' : '' }}" id="todo-{{ $todo->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="form-check fs-16">
                                                    <input class="form-check-input todo-checkbox" type="checkbox" data-id="{{ $todo->id }}" {{ $todo->is_completed ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="todo-text mb-1 fs-15">{{ $todo->task }}</h6>
                                                <div class="d-flex flex-wrap gap-2 align-items-center fs-12 text-muted">
                                                    @if($todo->due_date)
                                                        @php
                                                            $isOverdue = !$todo->is_completed && \Carbon\Carbon::parse($todo->due_date)->isPast() && !\Carbon\Carbon::parse($todo->due_date)->isToday();
                                                        @endphp
                                                        <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                                            <i class="ri-calendar-event-line align-bottom me-1"></i>
                                                            {{ \Carbon\Carbon::parse($todo->due_date)->format('d M, Y') }}
                                                        </span>
                                                        <span class="text-muted">|</span>
                                                    @endif
                                                    
                                                    @if($todo->priority == 'high')
                                                        <span class="badge bg-danger-subtle text-danger text-uppercase">High</span>
                                                    @elseif($todo->priority == 'medium')
                                                        <span class="badge bg-warning-subtle text-warning text-uppercase">Medium</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success text-uppercase">Low</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-3">
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-secondary btn-icon btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle fs-16"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @can('edit-todo')
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item edit-btn" 
                                                                data-id="{{ $todo->id }}"
                                                                data-task="{{ $todo->task }}"
                                                                data-priority="{{ $todo->priority }}"
                                                                data-due_date="{{ $todo->due_date }}">
                                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                            </a>
                                                        </li>
                                                        @endcan
                                                        @can('delete-todo')
                                                        <li>
                                                            <form action="{{ route('todos.destroy', $todo->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this task?')">
                                                                    <i class="ri-delete-bin-line align-bottom me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <form action="{{ route('todos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="createModalLabel">Create Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            <input type="text" name="task" class="form-control" required placeholder="e.g. Call client for payments">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            <input type="text" name="task" id="editTask" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" id="editPriority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="editDueDate" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Edit modal setup
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $('#editForm').attr('action', '/todos/' + id);
                $('#editTask').val($(this).data('task'));
                $('#editPriority').val($(this).data('priority'));
                $('#editDueDate').val($(this).data('due_date'));
                $('#editModal').modal('show');
            });

            // Toggle checkbox status
            $('.todo-checkbox').on('change', function() {
                let id = $(this).data('id');
                let checkbox = $(this);
                let item = $('#todo-' + id);
                
                $.ajax({
                    url: '/todos/' + id + '/toggle',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (checkbox.is(':checked')) {
                                item.addClass('completed');
                            } else {
                                item.removeClass('completed');
                            }
                        }
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                        checkbox.prop('checked', !checkbox.is(':checked'));
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
