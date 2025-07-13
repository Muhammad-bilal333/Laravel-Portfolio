@include('layouts.head')
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Faculty Login</title>
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #E2ECF2; /* Light blue background */
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
            opacity:0.8;
            border-radius: 10px;
            mix-blend-mode:
            color-burn;
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
            top: -35px;
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
        .form-control, .form-select {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #005f73;
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
            .left-panel {
                padding: 5px;
            }
            .right-panel h2{
                font-size: 10px;
            }
            .right-panel {
            padding: 5px;
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
                top: 43px;
                left: 7px;
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
            <!-- Left Panel -->
            <div class="col-md-6 left-panel">
                <h2 class="Heading-title">Foundation University<br>School of Science and Technology</h2>
                <img src="{{ asset('img/FUSSTLogo.jpg') }}" alt="University Logo">
            </div>

            <!-- Right Panel -->
            <div class="col-md-6 right-panel">
                <a href="{{ route('home') }}" class="backarrow"><i class="bi bi-arrow-left"></i></a>
                <h2 class="mb-2 fs-5 text-center">Faculty Login</h2>
                <form class="login-form" method="POST" action="{{ route('faculty.authenticate') }}">
                    @csrf
                    <input type="email" name="email" class="form-control" placeholder="User Email" required>
                    <p class="text-danger">{{ $errors->first('email') }}</p>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <p class="text-danger" style="text-align: left;">{{ $errors->first('password') }}</p>
                    {{-- <select name="designation" class="form-select" required>
                        <option value="" disabled selected>Choose your designation</option>
                        <option value="Lab Engineer">Lab Engineer</option>
                        <option value="Lecturer">Lecturer</option>
                        <option value="Assistant Professor">Assistant Professor</option>
                        <option value="Associate Professor">Associate Professor</option>
                        <option value="Professor">Professor</option>
                    </select>
                    <p class="text-danger" style="text-align: left;">{{ $errors->first('designation') }}</p> --}}
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>                
                <div class="mt-3 text-center">
                    <a href="#" class="text-muted">Forgot your User ID or Password?</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
