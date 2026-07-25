@extends('layouts.app')
@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h1 m-0"><strong>Assignments</strong></h1>
                <a class="btn btn-success" href="{{route('assignments.create')}}">
                    <i class="align-middle" data-feather="link"></i>
                    <span class="align-middle">Assign</span>
                </a>
            </div>

            <div class="row" id="assignments-container">
                <div class="col-12 col-lg-8 col-xxl-12 d-flex">
                    <div class="card flex-fill">
                        <table class="table table-hover my-0">
                            <thead>
                            <tr>
                                <th>Volunteer Name</th>
                                <th class="d-none d-xl-table-cell">Place</th>
                                <th class="d-none d-xl-table-cell">Task</th>
                            </tr>
                            </thead>
                            <tbody id="assignments-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <nav>
                <ul class="pagination justify-content-start mt-4" id="pagination"></ul>
            </nav>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const token = localStorage.getItem('token');

        function loadAssignments(page = 1) {
            axios.get(`/api/assignments?page=${page}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then(response => {
                    const content = document.getElementById('assignments-body');
                    const pagination = document.getElementById('pagination');

                    content.innerHTML = '';
                    pagination.innerHTML = '';

                    const dataArray = response.data;

                    if (!dataArray || !dataArray.data || dataArray.data.length === 0) {
                        content.innerHTML = '<tr><td colspan="3" class="text-center">No assignments found.</td></tr>';
                        return;
                    }

                    dataArray.data.forEach(assignment => {
                        const table = `
                    <tr>
                        <td>${assignment.volunteer?.first_name ?? ''} ${assignment.volunteer?.last_name ?? ''}</td>
                        <td class="d-none d-xl-table-cell">${assignment.place?.name ?? ''}</td>
                        <td class="d-none d-xl-table-cell">${assignment.task?.name ?? ''}</td>
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
                        if(currentPage > 1) loadAssignments(currentPage - 1);
                    };
                    prevLi.appendChild(prevA);
                    pagination.appendChild(prevLi);


                    for(let i = 1; i <= lastPage; i++) {
                        const li = document.createElement('li');
                        li.className = 'page-item' + (i === currentPage ? ' active' : '');

                        const a = document.createElement('a');
                        a.className = 'page-link';
                        a.href = '#';
                        a.innerText = i;
                        a.onclick = (e) => {
                            e.preventDefault();
                            loadAssignments(i);
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
                        if(currentPage < lastPage) loadAssignments(currentPage + 1);
                    };
                    nextLi.appendChild(nextA);
                    pagination.appendChild(nextLi);

                })
                .catch(error => {
                    console.error('Error fetching assignments:', error);
                    document.getElementById('assignments-container').innerHTML = '<p>Failed to load assignments.</p>';
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadAssignments();
        });
    </script>
@endsection
