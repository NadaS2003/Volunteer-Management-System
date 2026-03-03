<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Volunteer Management</title>
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body>
<main class="d-flex w-100">
    <div class="container d-flex flex-column">

        <div class="row vh-100">

            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">

                <div class="d-table-cell align-middle">
                    <div class="text-center mt-4">
                        <h1 class="h2">Welcome back!</h1>
                        <p class="lead">
                            Sign in to your account to continue
                        </p>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-3">
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" required />
                                    </div>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary">Sign in</button>
                                    </div>
                                    <div id="result" class="mt-3 text-center"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const resultDiv = document.getElementById('result');
        resultDiv.innerText = 'Loading...';
        resultDiv.className = 'mt-3 text-center text-muted';

        fetch('http://localhost:8000/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: document.querySelector('[name=email]').value,
                password: document.querySelector('[name=password]').value
            })
        })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            })
            .then(data => {
                localStorage.setItem('token', data.token);

                resultDiv.className = 'mt-3 text-center text-success';
                resultDiv.innerText = 'Login successful! Redirecting...';

                setTimeout(() => {
                    window.location.href = "/dashboard";
                }, 1000);
            })
            .catch(error => {
                resultDiv.className = 'mt-3 text-center text-danger';
                resultDiv.innerText = error.message ?? 'Login failed';
            });

    });
</script>
</body>
</html>
