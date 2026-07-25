@extends('layouts.app')

@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <form id="editForm">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 d-inline align-middle">Edit Place</h1>
                    </div>
                    <div class="card-body">
                        <label class="card-title mb-2">Name</label>
                        <input type="text" class="form-control mb-4" id="name" name="name" placeholder="Name">

                        <label class="card-title mb-2">Location</label>
                        <input type="text" class="form-control mb-4" id="location" name="location" placeholder="Location">

                        <button type="submit" class="btn btn-success btn-lg">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const pathParts = window.location.pathname.split('/');
        const id = pathParts[2];
        const token = localStorage.getItem('token');


        axios.get(`/api/places/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
            .then(response => {
                console.log(id);

                const place = response.data.data;


                if (!place) {
                    Swal.fire('Not Found', 'Place not found in the database.', 'warning');
                    return;
                }
                document.getElementById('name').value = place.name;
                document.getElementById('location').value = place.location;
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Failed to load place data.', 'error');
            });


        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const location = document.getElementById('location').value;

            axios.put(`/api/places/${id}`, {
                name: name,
                location: location
            }, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Successful!',
                        text: 'The place details have been updated.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            title: 'fs-4',
                            confirmButton: 'btn btn-primary'
                        }
                    }).then(() => {
                        window.location.href = '/places';
                    });
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        let messages = '';
                        Object.values(errors).forEach(errorArray => {
                            errorArray.forEach(msg => {
                                messages += `• ${msg}<br>`;
                            });
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: messages,
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: error.response?.data?.message || 'Something went wrong.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });

        });

    </script>
@endsection
