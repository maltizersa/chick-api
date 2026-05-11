<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel System</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #F6D9D4;
        }

        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .nav-link:hover {
            background-color: #f7e2de;
            color: #000 !important;
        }

        .nav-link.logout {
            color: #dc3545 !important;
        }

        .nav-link.logout:hover {
            background-color: #ffe3e3;
        }

        .content {
            padding: 20px;
        }

        /* Mobile spacing fix */
        @media (max-width: 768px) {
            .nav-link {
                margin-bottom: 8px;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">

        <a class="navbar-brand fw-bold" href="/home">
            Chick-IN Administrator
        </a>

        <!-- Mobile toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse" id="navMenu">

            <ul class="navbar-nav ms-auto text-center text-lg-end">

                <li class="nav-item">
                    <a class="nav-link" href="/addhotel">Add Hotel</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/hotelowners">Hotel Owners</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link logout" href="/logout">
                        Logout
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container content">
    @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>