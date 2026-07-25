@extends('layouts.app')

@section('main')
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3 fw-bold">Dashboard</h1>

            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Total Volunteers</h5>
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i data-feather="users"></i>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-1" id="volunteers-count">--</h1>
                            <div class="text-muted">Active in the system</div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Total Places</h5>
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i data-feather="map"></i>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-1" id="places-count">--</h1>
                            <div class="text-muted">Registered locations</div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Total Tasks</h5>
                                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i data-feather="check-square"></i>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-1" id="tasks-count">--</h1>
                            <div class="text-muted">Across all places</div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Assigned Volunteers</h5>
                                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i data-feather="user-check"></i>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-1" id="assigned-count">--</h1>
                            <div class="text-muted">Currently assigned</div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-lg-8 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Volunteers per Place</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="bar-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Task Distribution</h5>
                        </div>
                        <div class="card-body d-flex">
                            <div class="chart chart-xs w-100">
                                <canvas id="pie-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Latest Volunteers</h5>
                        </div>
                        <table class="table table-hover my-0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Place</th>
                                <th>Task</th>
                            </tr>
                            </thead>
                            <tbody id="latest-volunteers">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const token = localStorage.getItem('token');

        function generateRandomColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                colors.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
            }
            return colors;
        }

        document.addEventListener('DOMContentLoaded', function () {
            axios.get('/api/dashboard', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then(function (response) {
                    const data = response.data;


                    document.getElementById('volunteers-count').innerText = data.volunteers;
                    document.getElementById('places-count').innerText = data.places;
                    document.getElementById('tasks-count').innerText = data.tasks;
                    document.getElementById('assigned-count').innerText = data.assigned;


                    new Chart(document.getElementById("bar-chart"), {
                        type: 'bar',
                        data: {
                            labels: data.bar_chart_labels,
                            datasets: [{
                                label: 'Volunteers',
                                data: data.bar_chart_data,
                                backgroundColor: '#e34ca1'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });


                    new Chart(document.getElementById("pie-chart"), {
                        type: 'doughnut',
                        data: {
                            labels: data.pie_chart_labels,
                            datasets: [{
                                label: 'Tasks',
                                data: data.pie_chart_data,
                                backgroundColor: generateRandomColors(data.pie_chart_data.length)

                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });


                    const tbody = document.getElementById("latest-volunteers");
                    tbody.innerHTML = "";
                    data.latest_volunteers.forEach(vol => {
                        tbody.innerHTML += `
                        <tr>
                            <td>${vol.first_name} ${vol.last_name}</td>
                            <td>${vol.email}</td>
                            <td>${vol.place}</td>
                            <td>${vol.task}</td>
                        </tr>`;
                    });
                })
                .catch(function (error) {
                    console.error("Failed to fetch dashboard data:", error);
                });
        });
    </script>

@endsection
