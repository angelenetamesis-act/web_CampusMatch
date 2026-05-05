<!-- app/Views/verify_2fa.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Campus Match</title>
    <!-- Keeping your stylesheet for any shared brutal-btn classes -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: url('assets/img/plain-background.png') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Public Sans', sans-serif;
">
    <div style="
        max-width: 400px; 
        width: 90%;
        padding: 40px; 
        background: white; 
        border: 5px solid black; 
        box-shadow: 12px 12px 0px black;
        text-align: center;
    ">
        <!-- High-Contrast Header Box -->
        <div style="
            background: #A3E635; 
            border: 4px solid black; 
            padding: 10px; 
            margin-bottom: 25px; 
            box-shadow: 4px 4px 0px black;
        ">
            <h2 style="margin: 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px;">Security Gate</h2>
        </div>

        <p style="font-weight: 800; line-height: 1.4; margin-bottom: 25px; color: #333;">
            Enter the 6-digit code from your Google Authenticator app to authorize this session.
        </p>

        <?php if (isset($error)): ?>
            <div style="
                background: #ff5f5f; 
                border: 3px solid black; 
                padding: 10px; 
                margin-bottom: 20px; 
                font-weight: 900;
                font-size: 14px;
            ">
                ⚠️ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=verify_2fa" method="POST">
            <input type="text" name="one_time_password" placeholder="000000" maxlength="6" autofocus autocomplete="off"
                   style="
                    width: 100%; 
                    box-sizing: border-box;
                    padding: 15px; 
                    font-size: 32px; 
                    font-weight: 900;
                    text-align: center; 
                    border: 4px solid black; 
                    margin-bottom: 25px;
                    outline: none;
                    background: #fff;
                    box-shadow: inset 4px 4px 0px #eee;
                   ">
            
            <button type="submit" class="brutal-btn" style="
                width: 100%; 
                padding: 18px; 
                background: #000; 
                color: #A3E635; 
                font-size: 18px;
                text-transform: uppercase;
                font-weight: 900; 
                border: none;
                cursor: pointer;
                box-shadow: 6px 6px 0px #A3E635;
                transition: transform 0.1s;
            ">
                AUTHORIZE ACCESS
            </button>
        </form>

        <div style="margin-top: 25px;">
            <a href="index.php?action=logout" style="
                color: black; 
                font-weight: 900; 
                text-decoration: underline; 
                font-size: 13px;
            ">Cancel and Logout</a>
        </div>
    </div>
</body>
</html>