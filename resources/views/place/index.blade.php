@extends('layouts.app')
@section('main')

    <main class="content">
        <div class="container-fluid p-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h1 m-0"><strong>Places</strong></h1>
                <a class="btn btn-success" href="{{route('places.create')}}">
                    <i class="align-middle" data-feather="plus-circle"></i>
                    <span class="align-middle">Add</span>
                </a>
            </div>

            <div class="row" id="places-container">
                <div class="col-12 col-lg-8 col-xxl-12 d-flex">
                    <div class="card flex-fill">
                        <table class="table table-hover my-0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="d-none d-xl-table-cell">Location</th>
                                <th class="d-none d-xl-table-cell">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="places-body">
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

        function loadPlaces(page = 1) {
            axios.get(`/api/places?page=${page}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }).then(response => {
                const content = document.getElementById('places-body');
                const pagination = document.getElementById('pagination');

                content.innerHTML = '';
                pagination.innerHTML = '';

                const dataArray = response.data;


                if (!dataArray || !dataArray.data || dataArray.data.length === 0) {
                    content.innerHTML = '<tr><td colspan="3" class="text-center">No places found.</td></tr>';
                    return;
                }


                dataArray.data.forEach(place => {
                    const table = `
                        <tr>
                            <td>${place.name}</td>
                            <td class="d-none d-xl-table-cell">${place.location || 'No location available.'}</td>
                            <td class="d-none d-xl-table-cell">
                                <a href="/places/${place.id}/edit" class="btn btn-warning">EDIT</a>
                                <a class="btn btn-danger" onclick="deletePlace(${place.id})">DELETE</a>
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
                    if(currentPage > 1) loadPlaces(currentPage - 1);
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
                        loadPlaces(i);
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
                    if(currentPage < lastPage) loadPlaces(currentPage + 1);
                };
                nextLi.appendChild(nextA);
                pagination.appendChild(nextLi);


            }).catch(error => {
                console.error('Error fetching places:', error);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadPlaces();
        });
    </script>

    <script>
        function deletePlace(id) {
            const token = localStorage.getItem('token');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete the place.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/places/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to delete place.');
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire(
                                'Deleted!',
                                'The place has been deleted.',
                                'success'
                            ).then(() => location.reload());
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
