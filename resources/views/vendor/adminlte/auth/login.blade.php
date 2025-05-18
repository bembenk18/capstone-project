<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Open Sans', sans-serif;
            background: url('/images/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            color: #fff;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-box input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 25px;
            border: none;
            margin-bottom: 15px;
            outline: none;
        }
        .login-box .btn {
            background-color: #ffa07a;
            border: none;
            border-radius: 25px;
            padding: 10px 15px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-box .btn:hover {
            background-color: #ff7f50;
        }
        .login-box .links {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9em;
        }
        .login-box .social-login {
            text-align: center;
            margin-top: 20px;
        }
        .social-login button {
            margin: 0 10px;
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            background: #fff;
            color: #333;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            <input type="password" name="password" placeholder="Password" required>

            <!-- <div style="display:flex;align-items:center;margin-bottom:15px;">
                <input type="checkbox" name="remember" id="remember" style="margin-right: 10px;">
                <label for="remember">Remember Me</label>
            </div> -->

            <button type="submit" class="btn">SIGN IN</button>

            <div class="links">
                <a href="{{ route('password.request') }}" style="color:#fff">Forgot Password?</a>
            </div>

           
        </form>
    </div>
</div>
</body>
</html>
