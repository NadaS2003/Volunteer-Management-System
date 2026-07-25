@extends('layouts.app')

@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <form id="assignmentForm">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 d-inline align-middle">Assign</h1>
                    </div>
                    <div class="card-body">
                        <label class="card-title mb-2">Volunteer</label>
                        <select id="volunteer-select" class="form-select mb-3" required>
                            <option selected disabled>Choose a volunteer</option>
                        </select>

                        <label class="card-title mb-2">Place</label>
                        <select id="place-select" class="form-select mb-3" required>
                            <option selected disabled>Choose a place</option>
                        </select>

                        <label class="card-title mb-2">Task</label>
                        <select id="task-select" class="form-select mb-3" required>
                            <option selected disabled>Choose a task</option>
                        </select>

                        <button type="submit" class="btn btn-success btn-lg">Save</button>

                        <div id="result" class="mt-3 text-center"></div>
                    </div>
                </div>
            </form>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const token = localStorage.getItem('token');

        document.addEventListener('DOMContentLoaded', () => {
            loadVolunteers();
            loadPlaces();
            loadTasks();
        });

        function loadVolunteers() {
            axios.get('/api/volunteers', {
                headers: { Authorization: `Bearer ${token}` }
            })
                .then(response => {
                    const select = document.getElementById('volunteer-select');
                    const volunteers = response.data.data;

                    volunteers.forEach(volunteer => {
                        const option = document.createElement('option');
                        option.value = volunteer.id;
                        option.textContent = `${volunteer.first_name} ${volunteer.last_name}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading volunteers:', error));
        }


        function loadPlaces() {
            axios.get('/api/places', {
                headers: { Authorization: `Bearer ${token}` }
            })
                .then(response => {
                    const select = document.getElementById('place-select');


                    const places = response.data.data;

                    if (places && Array.isArray(places)) {
                        places.forEach(place => {
                            const option = document.createElement('option');
                            option.value = place.id;
                            option.textContent = place.name;
                            select.appendChild(option);
                        });
                    } else {
                        console.error("Data Not Found", places);
                    }
                })
                .catch(error => console.error('Error loading places:', error));
        }


        function loadTasks() {
            axios.get('/api/tasks', {
                headers: { Authorization: `Bearer ${token}` }
            })
                .then(response => {
                    const tasks = response.data.data;
                    console.log(tasks);

                    const select = document.getElementById('task-select');
                    select.innerHTML = '<option selected disabled>Choose a task</option>';

                    if (Array.isArray(tasks)) {
                        tasks.forEach(task => {
                            console.log('Task:', task.name, '-', task.description);
                            const option = document.createElement('option');
                            option.value = task.id;
                            option.textContent = task.name;
                            select.appendChild(option);
                        });
                    } else {
                        console.error("Data Not Found", tasks);
                    }
                })
                .catch(error => {
                    console.error('Error loading tasks:', error);
                });
        }



        document.getElementById('assignmentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const volunteer_id = document.getElementById('volunteer-select').value;
            const place_id = document.getElementById('place-select').value;
            const task_id = document.getElementById('task-select').value;

            fetch('/api/assignments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    volunteer_id: volunteer_id,
                    place_id: place_id,
                    task_id: task_id
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
                                title: 'Failed to Assign!',
                                text: data.message || 'Something went wrong.',
                                confirmButtonColor: '#d33'
                            });
                        }
                        throw new Error();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Assignment done successfully!',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    document.getElementById('assignmentForm').reset();

                    setTimeout(() => {
                        window.location.href = '/assignments';
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
