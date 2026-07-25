@extends('layouts.app')
@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h1 m-0"><strong>Tasks</strong></h1>
                <a class="btn btn-success" href="{{route('tasks.create')}}">
                    <i class="align-middle" data-feather="plus-circle"></i>
                    <span class="align-middle">Add</span>
                </a>
            </div>

            <div class="row" id="tasks-container">
                <div class="col-12 col-lg-8 col-xxl-12 d-flex">
                    <div class="card flex-fill">
                        <table class="table table-hover my-0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="d-none d-xl-table-cell">Description</th>
                                <th class="d-none d-xl-table-cell">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tasks-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <nav>
            <ul class="pagination justify-content-start mt-4" id="pagination"></ul>
        </nav>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const token = localStorage.getItem('token');

        function loadTasks(page = 1) {
            axios.get(`/api/tasks?page=${page}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }).then(response => {
                const content = document.getElementById('tasks-body');
                const pagination = document.getElementById('pagination');

                content.innerHTML = '';
                pagination.innerHTML = '';

                const dataArray = response.data;


                if (!dataArray || !dataArray.data || dataArray.data.length === 0) {
                    content.innerHTML = '<tr><td colspan="3" class="text-center">No tasks found.</td></tr>';
                    return;
                }


                dataArray.data.forEach(task => {
                    const table = `
                        <tr>
                            <td>${task.name}</td>
                            <td class="d-none d-xl-table-cell">${task.description || 'No description available.'}</td>
                            <td class="d-none d-xl-table-cell">
                                <a href="/tasks/${task.id}/edit" class="btn btn-warning">EDIT</a>
                                <a class="btn btn-danger" onclick="deleteTask(${task.id})">DELETE</a>
                            </td>
                        </tr>
                    `;
                    content.insertAdjacentHTML('beforeend', table);
                });


                const currentPage = dataArray.current_page;
                const lastPage = dataArray.last_page;


                const prevLi = document.createElement('li');
                prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
                const prevA = document.createElement('a');
                prevA.className = 'page-link';
                prevA.href = '#';
                prevA.innerText = 'Previous';
                prevA.onclick = (e) => {
                    e.preventDefault();
                    if(currentPage > 1) loadTasks(currentPage - 1);
                };
                prevLi.appendChild(prevA);
                pagination.appendChild(prevLi);


                for (let i = 1; i <= lastPage; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (i === currentPage ? ' active' : '');

                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.innerText = i;
                    a.onclick = (e) => {
                        e.preventDefault();
                        loadTasks(i);
                    };

                    li.appendChild(a);
                    pagination.appendChild(li);
                }


                const nextLi = document.createElement('li');
                nextLi.className = 'page-item ' + (currentPage === lastPage ? 'disabled' : '');
                const nextA = document.createElement('a');
                nextA.className = 'page-link';
                nextA.href = '#';
                nextA.innerText = 'Next';
                nextA.onclick = (e) => {
                    e.preventDefault();
                    if(currentPage < lastPage) loadTasks(currentPage + 1);
                };
                nextLi.appendChild(nextA);
                pagination.appendChild(nextLi);

            }).catch(error => {
                console.error('Error fetching tasks:', error);
                document.getElementById('tasks-container').innerHTML = '<p>Failed to load tasks.</p>';
            });
        }


        document.addEventListener('DOMContentLoaded', () => {
            loadTasks();
        });
    </script>

    <script>
        function deleteTask(id) {
            const token = localStorage.getItem('token');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete the task.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/tasks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to delete task.');
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire(
                                'Deleted!',
                                'The task has been deleted.',
                                'success'
                            ).then(() => loadTasks());
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error!',
                                error.message,
                                'error'
                            );
                        });
                }
            });
        }
    </script>

@endsection
