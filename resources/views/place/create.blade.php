@extends('layouts.app')
@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <form id="placeForm">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 d-inline align-middle">Add New Place</h1>
                    </div>
                    <div class="card-body">
                        <label class="card-title mb-2">Name</label>
                        <input type="text" class="form-control mb-4" name="name" placeholder="Name">

                        <label class="card-title mb-2">Location</label>
                        <input type="text" class="form-control mb-4" name="location" placeholder="Location">

                        <button type="submit" class="btn btn-success btn-lg">Add</button>

                        <div id="result" class="mt-3 text-center"></div>
                    </div>
                </div>

            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('placeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            const name = document.querySelector('[name=name]').value;
            const location = document.querySelector('[name=location]').value;

            fetch('/api/places', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name: name,
                    location: location
                })
            })
                .then(response => response.json().then(data => {
                    if (!response.ok) {

                        if (data.errors) {
                            let messages = '';
                            Object.values(data.errors).forEach(errArray => {
                                errArray.forEach(msg => {
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
                            throw new Error(data.message || 'Failed to add place.');
                        }
                        throw new Error();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Place added successfully!',
                        text: 'The new place has been saved in the database.',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    document.getElementById('placeForm').reset();

                    setTimeout(() => {
                        window.location.href = '/places';
                    }, 2000);
                }))
                .catch(error => {
                    if (error.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to add place!',
                            text: error.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });
    </script>

@endsection
