<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<title>Login</title>

    <style>
        *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            row-gap: 90px;
            height: 100vh;
            background: #f1f5f9;
        }
        .main{
            width: 90%;
            max-width: 350px;
            height: 500px;
            overflow: hidden;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 5px 12px 5px rgba(0,0,0,0.3);
            -webkit-box-shadow: 0px 5px 12px 5px rgba(0,0,0,0.3);
            -moz-box-shadow: 0px 5px 12px 5px rgba(0,0,0,0.3);
        }
        .signup{
            position: relative;
            width:100%;
            height: 100%;
        }
        label{
            color: #fff;
            font-size: 2rem;
            justify-content: center;
            display: flex;
            margin: 60px;
            font-weight: bold;
            text-align: center;
            transition: .5s ease-in-out;
        }
        input{
            width: 60%;
            height: 20px;
            background: #f1f5f9;
            justify-content: center;
            display: flex;
            margin: 20px auto;
            padding: 10px;
            border: none;
            outline: none;
            border-radius: 5px;
        }
        button{
            width: 65%;
            height: 40px;
            margin: 10px auto;
            justify-content: center;
            display: block;
            color: #fff;
            background: #0284c7;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 20px;
            outline: none;
            border: none;
            border-radius: 5px;
            transition: .2s ease-in;
            cursor: pointer;
        }
        button:hover{
            background: #0ea5e9;
        }
        .login{
            height: 460px;
            background: #0284c7;
            border-radius: 60% / 10%;
            transform: translateY(-160px);
            transition: .8s ease-in-out;
        }
        .login label{
            color: #fff;
            transform: scale(.6);
        }
        .powered-by {
            font-size: 1.2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
	<div class="main">
        <div class="signup">
            <div style="display: flex;flex-direction: column; align-items: center">
                <div style="display: flex;align-items: center;justify-content: center; margin:15px 0;height:100px;width:100px;border-radius:9999px;border:2px solid #f1f5f9;">
                    <img src="{{ asset('dist/img/com-logo.jpg') }}" alt="" height="90" width="90">
                </div>

                <h4 style="margin: 0; margin-bottom: 10px;">{{ config('configaration.company_name') }}</h4>
            </div>

            <form action="{{ route('login') }}" method="post">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            
        </div>

        <div class="login">
            <label>Log in to start your session</label>
        </div>
	</div>

    <div class="powered-by">
        Design and developed by <a href="http://i-infotechsolution.com" target="_blank">i-infotech Business Solution</a>
    </div>
</body>
</html>
