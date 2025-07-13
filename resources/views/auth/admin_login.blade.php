@include('layouts.head')
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #E2ECF2;
        }
        .main-container {
            background-color: #e3edf7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .left-panel {
            background: linear-gradient(to bottom, #033649, #005f73);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
        }
        .left-panel img {
            max-width: 180px;
            margin-top: 20px;
            opacity: 0.8;
            max-width: 60%;
            border-radius: 10px;
            mix-blend-mode: color-burn;
        }
        .right-panel {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            width: 50%;
        }
        .backarrow {
            position: relative;
            top: -50px;
            left: -35px;
            color: black;
            font-size: 21px;
            text-decoration: none;
            background-color: #e3edf7;
            padding: 4px;
            border-radius: 50%;
            height: 40px;
            width: 40px;
            text-align: center;
        }
        .form-check {
            margin-bottom: 15px;
        }
        .form-check-input {
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #005f73;
            border: none;
            padding: 7px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            border: none;
        }
        .btn-primary:hover {
            background-color: #003f4d;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .left-panel {
                padding: 5px;
            }
            .right-panel {
                padding: 2px;
                width: 100%;
            }
            .right-panel {
                padding: 2px;
                width: 100%;
            }
            .Heading-title{
                font-size: 10px;
            }
            .left-panel img {
                max-width: 100px;
                opacity:0.8;
                margin-top: 1px;
                border-radius: 10px;
                mix-blend-mode:
                color-burn;
            }
            .backarrow {
                position: absolute;
                top: 53px;  
                left: 12px;
                color: black;
                font-size: 11px;
                text-decoration: none;
                background-color: #ffffff;
                padding: 4px;
                border-radius: 50%;
                height: 25px;
                width: 25px;
                text-align: center;
            }
            .main-container {
                padding: 1px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="login-container">
            <div class="col-md-6 left-panel">
                <h2 class="Heading-title">Foundation University<br>School of Science and Technology</h2>
                <img src="{{ asset('img/FUSSTLogo.jpg') }}" alt="University Logo">
            </div>
            <div class="col-md-6 right-panel">
                <a href="{{ route('home') }}" class="backarrow"><i class="bi bi-arrow-left"></i></a>
                <h2 class="mb-2 fs-5  text-center">Admin Login</h2>
                <form method="POST" action="{{ route('admin.authenticate') }}">
                    @csrf
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <p class="text-danger" style="text-align: left;">{{ $errors->first('password') }}</p>
                        <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="#" class="text-muted">Forgot your User ID or Password?</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
