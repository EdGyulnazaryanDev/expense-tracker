@extends('layouts.app')

@section('content')
    <div class="container">
        <a class="btn btn-outline-info mt-3" id="toggleFormButton">Open Revenues Form</a>
        <style>
            .slide-form {
                display: none;
                transition: all 0.5s ease;
            }

            .slide-form.show {
                display: block;
                max-height: 1000px; /* Ensure enough height for slide effect */
                overflow: hidden;
            }
        </style>

        @if(isset($errors) && !is_null($errors))
            <div class="d-flex flex-column align-items-center">
                @foreach($errors as $key => $error)
                    <span class="badge text-bg-danger mt-1 text-lg">{{ $error[0] }}</span>
                @endforeach
            </div>
        @endif
        <div id="revenueFormContainer" class="slide-form">
            <form id="revenue-form" method="POST" action="{{ route('revenues.store') }}">
                @csrf
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" />
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" />
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" />
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category" name="category_id">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Add Revenue</button>
            </form>
        </div>
        <h3 class="mt-4">Revenues List</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="revenue-list">
            @if(isset($revenues) && !is_null($revenues))
                @foreach($revenues as $revenue)
                    <tr data-id="{{ $revenue->id }}">
                        <td>{{ $revenue->category->name }}</td>
                        <td>{{ $revenue->description }}</td>
                        <td>${{ number_format($revenue->amount, 2) }}</td>
                        <td>{{ $revenue->date }}</td>
                        <td>{{ $revenue->category->name }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editRevenue({{ $revenue->id }})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRevenue({{ $revenue->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editRevenueModal" tabindex="-1" aria-labelledby="editRevenueModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRevenueModalLabel">Edit Revenue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editRevenueForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="revenueId" name="revenueId">
                        <div class="form-group">
                            <label for="editDescription">Description</label>
                            <input type="text" class="form-control" id="editDescription" name="description" />
                        </div>
                        <div class="form-group">
                            <label for="editAmount">Amount</label>
                            <input type="number" class="form-control" id="editAmount" name="amount" step="0.01" />
                        </div>
                        <div class="form-group">
                            <label for="editDate">Date</label>
                            <input type="date" class="form-control" id="editDate" name="date" />
                        </div>
                        <div class="form-group">
                            <label for="editCategory">Category</label>
                            <select class="form-control" id="editCategory" name="category_id">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Revenue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggleFormButton').addEventListener('click', function() {
            var formContainer = document.getElementById('revenueFormContainer');
            var button = document.getElementById('toggleFormButton');

            if (formContainer.classList.contains('show')) {
                formContainer.classList.remove('show');
                formContainer.style.maxHeight = null;
                button.textContent = 'Open Revenues Form';
            } else {
                formContainer.classList.add('show');
                formContainer.style.maxHeight = formContainer.scrollHeight + "px";
                button.textContent = 'Close Revenues Form';
            }
        });

        function deleteRevenue(id) {
            if (confirm('Are you sure you want to delete this revenue?')) {
                fetch(`/revenues/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.querySelector(`tr[data-id="${id}"]`).remove();
                        } else {
                            alert('Failed to delete revenue');
                        }
                    });
            }
        }

        function editRevenue(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const description = row.children[0].textContent;
            const amount = row.children[1].textContent.replace('$', '');
            const date = row.children[2].textContent;
            const category = row.children[3].textContent;

            document.getElementById('revenueId').value = id;
            document.getElementById('editDescription').value = description;
            document.getElementById('editAmount').value = amount;
            document.getElementById('editDate').value = date;
            const editCategorySelect = document.getElementById('editCategory');
            for (let option of editCategorySelect.options) {
                if (option.text === category) {
                    option.selected = true;
                    break;
                }
            }

            var myModal = new bootstrap.Modal(document.getElementById('editRevenueModal'), {
                keyboard: false
            });
            myModal.show();

            document.getElementById('editRevenueForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const id = document.getElementById('revenueId').value;
                const updatedDescription = document.getElementById('editDescription').value;
                const updatedAmount = document.getElementById('editAmount').value;
                const updatedDate = document.getElementById('editDate').value;
                const updatedCategoryId = document.getElementById('editCategory').value;

                fetch(`/revenues/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        description: updatedDescription,
                        amount: updatedAmount,
                        date: updatedDate,
                        category_id: updatedCategoryId
                    })
                }).then(response => response.json())
                    .then(data => {
                        if (data) {
                            row.children[0].textContent = updatedDescription;
                            row.children[1].textContent = `$${parseFloat(updatedAmount).toFixed(2)}`;
                            row.children[2].textContent = updatedDate;
                            row.children[3].textContent = editCategorySelect.options[editCategorySelect.selectedIndex].text;
                            var myModalEl = document.getElementById('editRevenueModal');
                            var modal = bootstrap.Modal.getInstance(myModalEl);
                            modal.hide();
                        } else {
                            alert('Failed to update revenue');
                        }
                    });
            });
        }

        // Set the default date to today
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;
            document.getElementById('editDate').value = today; // Set default date for edit form as well
        });
    </script>
@endsection
