<!-- app/Views/setup_2fa.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup 2FA - Campus Match</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.8">
</head>
<body class="brutal-bg">
    <div class="setup-container" style="max-width: 500px; margin: 50px auto; padding: 20px; background: white; border: 4px solid black; box-shadow: 10px 10px 0px black;">
        <h1>Secure Your Account</h1>
        <p>Scan the QR code below using <strong>Google Authenticator</strong> or <strong>Authy</strong>.</p>
        
        <div class="qr-code-wrapper" style="text-align: center; margin: 20px 0; border: 2px dashed black; padding: 10px;">
            <?php echo $qrCodeSvg; ?>
        </div>

        <div class="manual-entry" style="background: #f0f0f0; padding: 10px; margin-bottom: 20px;">
            <p style="font-size: 12px; margin: 0;">If you can't scan, enter this code manually:</p>
            <code style="font-weight: bold; font-size: 16px;"><?php echo $secret; ?></code>
        </div>
    <div style="border: 4px solid black; padding: 12px; margin-bottom: 20px; background: #ffeb3b; box-shadow: 6px 6px 0px black;">
    <p style="margin: 0; font-weight: bold;">
        ⚠️ SAVE YOUR SECURITY KEY!
    </p>
    <p style="margin: 5px 0 0 0; font-size: 13px;">
        You will need this when logging in on a different device. Do not lose it.
    </p>
</div>

        <form action="index.php?action=confirm_2fa_setup" method="POST">
            <input type="hidden" name="secret" value="<?php echo $secret; ?>">
            <p>Enter the 6-digit code from your app to confirm:</p>
            <input type="text" name="one_time_password" placeholder="000000" maxlength="6" required 
                   style="width: 100%; padding: 10px; font-size: 20px; border: 2px solid black; margin-bottom: 15px;">
            <button type="submit" class="brutal-btn" style="width: 100%; cursor: pointer; padding: 15px; background: #ff9800; font-weight: bold;">
                Verify and Enable 2FA
            </button>
        </form>
    </div>
</body>
</html>