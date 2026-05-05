<!-- app/Views/register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Match - Sign Up (Step 1)</title>
    <!-- Keeping the link just in case, but internal styles will take priority -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Force full screen and background */
        body.signup-page {
            margin: 0 !important;
            padding: 0 !important;
            background-image: url('assets/img/signup-background.png') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
            font-family: 'Arial Black', sans-serif !important;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Center the card perfectly */
        .signup-wrapper {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            height: 100vh !important;
            width: 100% !important;
            background-color: rgba(0, 0, 0, 0.15) !important;
        }

        /* The Growth Green Card Layout */
        .signup-card {
            position: relative !important;
            background: #97f68d !important; /* Growth Green */
            border: 4px solid #f3f3f3 !important;
            padding: 40px !important;
            width: 90% !important;
            max-width: 420px !important;
            box-shadow: 12px 12px 0px #686666 !important;
            border-radius: 24px !important;
            text-align: center !important;
            z-index: 1 !important;
            box-sizing: border-box !important;
        }

        /* The Frosted Glass Halo */
        .signup-card::before {
            content: "" !important;
            position: absolute !important;
            top: -6px !important;
            left: -6px !important;
            right: -6px !important;
            bottom: -6px !important;
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-radius: 30px !important;
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
            z-index: -1 !important;
        }

        /* Typography */
        .brutal-title {
            font-size: 28px !important;
            margin: 0 0 5px 0 !important;
            text-transform: uppercase !important;
            color: #000 !important;
        }

        .subtitle {
            font-size: 14px !important;
            margin: 0 0 25px 0 !important;
            font-weight: 800 !important;
            color: #000 !important;
            opacity: 0.8 !important;
        }

        /* Form & Inputs */
        .brutal-form {
            display: flex !important;
            flex-direction: column !important;
        }

        .brutal-form input {
            width: 100% !important;
            padding: 12px !important;
            margin-bottom: 12px !important;
            border: 3px solid #000 !important;
            border-radius: 12px !important;
            background: #fff !important;
            font-family: 'Arial', sans-serif !important;
            font-weight: bold !important;
            box-shadow: 4px 4px 0px #000 !important;
            box-sizing: border-box !important;
        }

        /* Action Orange Button */
        .btn-orange {
            width: 100% !important;
            background: #ff8c00 !important; /* Action Orange */
            color: #000 !important;
            padding: 18px !important;
            font-family: 'Arial Black', sans-serif !important;
            font-size: 16px !important;
            border: 3px solid #000 !important;
            border-radius: 15px !important;
            cursor: pointer !important;
            box-shadow: 6px 6px 0px #000 !important;
            margin-top: 10px !important;
            text-transform: uppercase !important;
            transition: transform 0.1s !important;
        }

        .btn-orange:hover {
            transform: translate(-2px, -2px) !important;
            box-shadow: 8px 8px 0px #000 !important;
        }

        .btn-orange:active {
            transform: translate(2px, 2px) !important;
            box-shadow: 4px 4px 0px #000 !important;
        }
    </style>
</head>
<body class="signup-page">

    <div class="signup-wrapper">
        <div class="signup-card">
            
            <h2 class="brutal-title">CAMPUS MATCH</h2>
            <p class="subtitle">Step 1: Account Information</p>

            <?php if(isset($error)): ?>
                <p style="color: red; font-size: 0.8rem; font-weight: bold;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="index.php?action=register_step1" method="POST" class="brutal-form">
                <input type="text" name="full_name" placeholder=" User Name" required>
                <input type="email" name="email" placeholder=" use your CHMSU email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="number" name="age" placeholder="Age" required>
                <input type="text" name="course" placeholder="Course (e.g. BSIT)" required>
                <input type="number" name="year_level" placeholder="Year Level" required>
                
                <button type="submit" class="btn-orange">CONTINUE TO GOALS</button>

                 <p class="footer-text">Already have an account? <a href="index.php?action=login">Login Here</a></p>
            </form>
            
        </div>
    </div>
 </body>
</html>