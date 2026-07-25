@extends('layouts.app')
@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <form id="volunteerForm">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 d-inline align-middle">Add New Volunteer</h1>
                    </div>
                    <div class="card-body">
                        <label class="card-title mb-2">First Name</label>
                        <input type="text" class="form-control mb-4" name="first_name" placeholder="First Name">

                        <label class="card-title mb-2">Last Name</label>
                        <input type="text" class="form-control mb-4" name="last_name" placeholder="Last Name">

                        <label class="card-title mb-2">Email</label>
                        <input type="text" class="form-control mb-4" name="email" placeholder="Email">

                        <label class="card-title mb-2">Phone</label>
                        <input type="text" class="form-control mb-4" name="phone" placeholder="Phone">

                        <button type="submit" class="btn btn-success btn-lg">Add</button>

                        <div id="result" class="mt-3 text-center"></div>
                    </div>
                </div>

            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('volunteerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            const first_name = document.querySelector('[name=first_name]').value;
            const last_name = document.querySelector('[name=last_name]').value;
            const email = document.querySelector('[name=email]').value;
            const phone = document.querySelector('[name=phone]').value;

            fetch('/api/volunteers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    first_name: first_name,
                    last_name: last_name,
                    email : email,
                    phone : phone
                })
            })
                .then(response => response.json().then(data => {
                    if (!response.ok) {
                        if (data.errors) {
                            let messages = '';
                            Object.values(data.errors).forEach(errorArray => {
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
                                title: 'Failed to add volunteer!',
                                text: data.message || 'Something went wrong.',
                                confirmButtonColor: '#d33'
                            });
                        }
                        throw new Error();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Volunteer added successfully!',
                        text: 'The new volunteer has been saved in the database.',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    document.getElementById('volunteerForm').reset();

                    setTimeout(() => {
                        window.location.href = '/volunteers';
                    }, 2000);
                }))
                .catch(error => {
                    if (error.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed!',
                            text: error.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });
    </script>

@endsection
