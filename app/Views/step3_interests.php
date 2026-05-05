<!-- app/Views/step3_interests.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Match - Step 3</title>
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
            padding: 35px 30px !important;
            width: 95% !important;
            max-width: 440px !important;
            box-shadow: 12px 12px 0px #686666 !important;
            border-radius: 24px !important;
            text-align: center !important;
            z-index: 1 !important;
            box-sizing: border-box !important;
            display: flex;
            flex-direction: column;
            max-height: 90vh; /* Prevents card from going off-screen */
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

        .brutal-title {
            font-size: 22px !important;
            margin: 0 0 5px 0 !important;
            text-transform: uppercase !important;
            color: #000 !important;
        }

        .subtitle {
            font-size: 13px !important;
            margin: 0 0 15px 0 !important;
            font-weight: 800 !important;
            color: #000 !important;
            opacity: 0.8 !important;
        }

        /* Interest List Container - Scrollable */
        .interest-list {
            background: #fff !important;
            border: 3px solid #000 !important;
            border-radius: 15px !important;
            padding: 15px !important;
            margin-bottom: 20px !important;
            text-align: left !important;
            overflow-y: auto !important;
            flex-grow: 1;
            box-shadow: inset 4px 4px 0px rgba(0,0,0,0.1);
        }

        .interest-item {
            display: block !important;
            padding: 8px 5px !important;
            font-family: 'Arial', sans-serif !important;
            font-weight: bold !important;
            font-size: 14px !important;
            color: #000 !important;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .interest-item:last-child { border-bottom: none; }

        input[type="checkbox"] {
            margin-right: 10px !important;
            transform: scale(1.3) !important;
            accent-color: #ff8c00 !important; /* Action Orange */
            cursor: pointer;
        }

        /* Finish Button */
        .btn-finish {
            width: 100% !important;
            background: #ff8c00 !important;
            color: #000 !important;
            padding: 16px !important;
            font-family: 'Arial Black', sans-serif !important;
            font-size: 16px !important;
            border: 3px solid #000 !important;
            border-radius: 15px !important;
            cursor: pointer !important;
            box-shadow: 6px 6px 0px #000 !important;
            text-transform: uppercase !important;
            transition: all 0.1s ease !important;
            flex-shrink: 0;
        }

        .btn-finish:hover {
            transform: translate(-2px, -2px) !important;
            box-shadow: 8px 8px 0px #000 !important;
        }
    </style>
</head>
<body class="signup-page">

    <div class="signup-wrapper">
        <div class="signup-card">
            
            <h2 class="brutal-title">CAMPUS MATCH</h2>
            <p class="subtitle">Step 3: Choose up to 5 Interests</p>

            <form action="index.php?action=submit_interests" method="POST" style="display: contents;">
                <div class="interest-list">
    <?php foreach($interests as $interest): ?>
        <label class="interest-item">
            <input type="checkbox" name="interest_ids[]" value="<?php echo $interest['id']; ?>"
                <?php 
                    // Check if this interest ID is in our array of currently selected IDs
                    if (isset($current_selected_ids) && in_array($interest['id'], $current_selected_ids)) {
                        echo 'checked';
                    }
                ?>
            >
            <?php echo $interest['interest_name']; ?>
        </label>
    <?php endforeach; ?>
</div>
                
<button type="submit" class="btn-finish">
    <?php echo (isset($_SESSION['user_id'])) ? 'Update My Interests' : 'Complete Sign Up'; ?>
</button>            </form>
            
        </div>
    </div>

    <script>
        // Simple logic to limit selection to 5
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const checked = document.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > 5) {
                    cb.checked = false;
                    alert("Only 5 interests allowed!");
                }
            });
        });
    </script>

</body>
</html>