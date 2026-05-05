<!-- app/Views/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Match</title>
    <!-- Kept your link for safety, but inline will take priority -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.5">
    
    <style>
        /* This CSS is now embedded to ensure no external file can break it */
        .login-page-lg {
            margin: 0;
            padding: 0;
            background-image: url('assets/img/campus-background.png'); 
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Arial Black', sans-serif;
            min-height: 100vh;
        }

        .login-wrapper-lg {
            display: flex;
            justify-content: center; 
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .login-card-lg {
            position: relative;
            background: #97f68d; /* Growth Green */
            border: 5px solid #000;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px; 
            box-shadow: 15px 15px 0px #000;
            border-radius: 35px; /* Matches your Sign Up bubbly radius */
            text-align: center;
            z-index: 1; 
        }

        /* The Glass Halo */
        .login-card-lg::before {
            content: "";
            position: absolute;
            top: -10px; left: -10px; right: -10px; bottom: -10px;
            background: rgba(255, 255, 255, 0.3); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px);
            border-radius: 42px; 
            border: 2px solid rgba(255, 255, 255, 0.5);
            z-index: -1; 
        }

        .brutal-title { 
            font-size: 32px; 
            margin: 0 0 5px 0; 
            text-transform: uppercase; 
            letter-spacing: -1px;
        }

        .subtitle { 
            font-size: 14px; 
            margin: 0 0 35px 0; 
            font-weight: 800; 
            opacity: 0.9; 
        }

        .brutal-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-group-lg {
            text-align: left;
        }

        .input-group-lg label {
            display: block;
            font-size: 12px;
            margin-bottom: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .input-group-lg input {
            width: 100%;
            padding: 15px;
            border: 4px solid #000;
            border-radius: 15px;
            font-family: 'Arial', sans-serif;
            font-weight: 800;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            background: #fff;
        }

        .login-btn-lg {
            width: 100%;
            background: #FF8C00; /* Action Orange */
            color: #000; 
            padding: 20px;
            font-family: 'Arial Black', sans-serif;
            font-size: 18px;
            border: 4px solid #000;
            border-radius: 18px;
            cursor: pointer;
            box-shadow: 8px 8px 0px #000;
            margin-top: 15px;
            text-transform: uppercase;
            transition: 0.1s;
        }

        .login-btn-lg:hover {
            transform: translate(-3px, -3px);
            box-shadow: 11px 11px 0px #000;
        }

        .login-btn-lg:active {
            transform: translate(2px, 2px);
            box-shadow: 4px 4px 0px #000;
        }

        .footer-text-lg {
            margin-top: 35px;
            font-weight: 900;
            font-size: 14px;
        }

        .footer-text-lg a {
            color: #000;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .footer-text-lg a:hover {
            color: #FF8C00;
        }
    </style>
</head>
<body class="login-page-lg">
    <div class="login-wrapper-lg">
        <div class="login-card-lg">
            <h2 class="brutal-title">CAMPUS MATCH</h2>
            <p class="subtitle">CHMSU's Student Connection Hub</p>

            <form action="index.php?action=login_process" method="POST" class="brutal-form">
                <div class="input-group-lg">
                    <label>CHMSU EMAIL</label>
                    <input type="email" name="email" placeholder="user@chmsu.edu.ph" required>
                </div>

                <div class="input-group-lg">
                    <label>PASSWORD</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="login-btn-lg">ENTER APP ➔</button>
            </form>

            <p class="footer-text-lg">New here? <a href="index.php?action=register_step1">Create Account</a></p>
        </div>
    </div>
</body>
</html>