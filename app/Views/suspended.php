<!-- app/Views/suspended.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended | Campus Match</title>
</head>
<body style="margin: 0; padding: 0; background-color: #1f1f20; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; color: #1a1a1a;">

    <div style="background: white; padding: 40px; border: 4px solid #1a1a1a; box-shadow: 10px 10px 0px #1a1a1a; max-width: 500px; width: 90%; text-align: center; border-radius: 8px;">
        
        <div style="font-size: 50px; margin-bottom: 20px;">🚫</div>
        
        <h1 style="margin: 0 0 15px 0; font-size: 28px; text-transform: uppercase; letter-spacing: 1px;">Account Suspended</h1>
        
        <p style="margin: 0 0 25px 0; line-height: 1.6; color: #444;">
            Your account has been flagged for violating the <strong>CHMSU Community Guidelines</strong> (repeated profanity or misconduct).
        </p>

        <div style="background: #fff5f5; border: 2px dashed #ff4d4d; padding: 20px; border-radius: 6px; margin-bottom: 25px;">
            <p style="margin: 0 0 5px 0; font-size: 14px; color: #ff4d4d; font-weight: bold; text-transform: uppercase;">Access restored in:</p>
            <span style="font-size: 22px; font-weight: 800; color: #d32f2f;">
                <?php 
                if (isset($user_details['suspension_end_datetime']) && !empty($user_details['suspension_end_datetime'])) {
                    try {
                        $now = new DateTime();
                        $end = new DateTime($user_details['suspension_end_datetime']);
                        
                        if ($now < $end) {
                            $diff = $now->diff($end);
                            echo $diff->format('%d days, %h hours');
                        } else {
                            echo "Processing Restoration...";
                        }
                    } catch (Exception $e) {
                        echo "Check Date Format";
                    }
                } else {
                    echo "Pending Update...";
                }
                ?>
            </span>
        </div>

        <p style="font-size: 13px; color: #888; margin-bottom: 30px;">
            Please contact the CHMSU Admin if you believe this is a mistake. 
            Manual reviews can take up to 24 hours.
        </p>

        <a href="index.php?action=logout" 
           style="display: inline-block; background: #1a1a1a; color: white; padding: 15px 30px; text-decoration: none; font-weight: bold; border-radius: 4px; transition: transform 0.2s ease; box-shadow: 4px 4px 0px #888;">
            EXIT APPLICATION
        </a>

    </div>

</body>
</html>