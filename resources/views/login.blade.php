<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #F6D9D4;
            height: 100vh;
        }

        .login-card {
            width: 380px;
            border-radius: 16px;
            padding: 30px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .btn-primary-custom {
            background-color: #F6D9D4;
            border: none;
            color: #333;
        }

        .btn-primary-custom:hover {
            background-color: #f2c9c2;
        }

        .loading {
            display: none;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="login-card">

    <h4 class="text-center mb-4">Login</h4>

    <form method="POST" action="/login" onsubmit="showLoading()">
        @csrf

        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100" id="loginBtn">
            Login
        </button>

        @if ($errors->has('login'))
            <div class="alert alert-danger text-center">
                {{ $errors->first('login') }}
            </div>
        @endif

        <!-- Loading -->
        <button class="btn btn-primary-custom w-100 loading" id="loadingBtn" disabled>
            <span class="spinner-border spinner-border-sm"></span>
            Logging in...
        </button>

    </form>
</div>

<script>
    function showLoading() {
        document.getElementById('loginBtn').style.display = 'none';
        document.getElementById('loadingBtn').style.display = 'block';
    }
</script>

</body>
</html>