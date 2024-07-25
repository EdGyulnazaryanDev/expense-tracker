@extends('layouts.app')
@section('content')
    <div class="container mt-3">
        <p class="h2">Manage Categories</p>

        @if(isset($errors) && !is_null($errors))
            <div class="d-flex flex-column align-items-center">
                @foreach($errors as $key => $error)
                    <span class="badge text-bg-danger mt-1 text-lg">{{ $error[0] }}</span>
                @endforeach
            </div>
        @endif
        <!-- Add Expense Form -->
        <form id="category-form" method="POST" action="{{ route('category.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control w-25" id="name" name="name" />
            </div>

            <button type="submit" class="btn btn-outline-primary mt-3">Add Category name</button>
        </form>

        <h3 class="mt-4">Categories List</h3>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th colspan="2">Name</th>
            </tr>
            </thead>
            <tbody id="categories-list">
            @if(isset($categories) && !is_null($categories))
                @foreach($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td>{{ $category->id }}</td>
                        <td class="category-name">{{ $category->name }}</td>
                        <td>
                            <button class="btn btn-outline-warning btn-sm" onclick="editCategory({{ $category->id }})">Edit</button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteCategory({{ $category->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        <input type="hidden" id="categoryId">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to handle expense deletion
        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this expense?')) {
                fetch(`/categories/${id}`, {
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
                            alert('Failed to delete expense');
                        }
                    });
            }
        }

        // Function to handle expense editing
        function editCategory(id) {
            const categoryName = document.querySelector(`tr[data-id="${id}"] .category-name`).textContent;
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = categoryName;
            var myModal = new bootstrap.Modal(document.getElementById('editCategoryModal'), {
                keyboard: false
            });
            myModal.show();
            document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const id = document.getElementById('categoryId').value;
                const newName = document.getElementById('categoryName').value;

                fetch(`/categories/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name: newName })
                }).then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.querySelector(`tr[data-id="${id}"] .category-name`).textContent = newName;
                            var myModalEl = document.getElementById('editCategoryModal');
                            var modal = bootstrap.Modal.getInstance(myModalEl);
                            modal.hide();
                        } else {
                            alert('Failed to update category');
                        }
                    });
            });

        }
    </script>


@endsection
