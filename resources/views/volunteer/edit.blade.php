@extends('layouts.app')

@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <form id="editForm">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 d-inline align-middle">Edit Volunteer</h1>
                    </div>
                    <div class="card-body">
                        <label class="card-title mb-2">First Name</label>
                        <input type="text" class="form-control mb-4" id="first_name" name="first_name" placeholder="First Name">

                        <label class="card-title mb-2">Last Name</label>
                        <input type="text" class="form-control mb-4" id="last_name" name="last_name" placeholder="Last Name">

                        <label class="card-title mb-2">Email</label>
                        <input type="text" class="form-control mb-4" id="email" name="email" placeholder="Email">

                        <label class="card-title mb-2">Phone</label>
                        <input type="text" class="form-control mb-4" id="phone" name="phone" placeholder="Phone">

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


        axios.get(`/api/volunteers/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
            .then(response => {
                console.log(id);

                const volunteer = response.data.data;


                if (!volunteer) {
                    Swal.fire('Not Found', 'Volunteer not found in the database.', 'warning');
                    return;
                }
                document.getElementById('first_name').value = volunteer.first_name;
                document.getElementById('last_name').value = volunteer.last_name;
                document.getElementById('email').value = volunteer.email;
                document.getElementById('phone').value = volunteer.phone;
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Failed to load volunteer data.', 'error');
            });


        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const first_name = document.getElementById('first_name').value;
            const last_name = document.getElementById('last_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            axios.put(`/api/volunteers/${id}`, {
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone
            }, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Successful!',
                        text: 'The volunteer details have been updated.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            title: 'fs-4',
                            confirmButton: 'btn btn-primary'
                        }
                    }).then(() => {
                        window.location.href = '/volunteers';
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
