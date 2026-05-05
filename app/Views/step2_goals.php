<!-- app/Views/step2_goals.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Match - Step 2</title>
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

        .brutal-title {
            font-size: 24px !important;
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

        /* Goal Buttons Container */
        .goal-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
        }

        /* Neo-Brutalist Goal Buttons */
        .goal-btn {
            width: 100% !important;
            background: #ffffff !important;
            color: #000 !important;
            padding: 15px !important;
            font-family: 'Arial Black', sans-serif !important;
            font-size: 14px !important;
            border: 3px solid #000 !important;
            border-radius: 12px !important;
            cursor: pointer !important;
            box-shadow: 5px 5px 0px #000 !important;
            text-transform: uppercase !important;
            transition: all 0.1s ease !important;
        }

        .goal-btn:hover {
            background: #ff8c00 !important; /* Turns Action Orange on hover */
            transform: translate(-2px, -2px) !important;
            box-shadow: 7px 7px 0px #000 !important;
        }

        .goal-btn:active {
            transform: translate(2px, 2px) !important;
            box-shadow: 2px 2px 0px #000 !important;
        }
    </style>
</head>
<body class="signup-page">

    <div class="signup-wrapper">
        <div class="signup-card">
            
            <h2 class="brutal-title">CAMPUS MATCH</h2>
            <p class="subtitle">Step 2: Identify your Goal</p>

            <form action="index.php?action=submit_goal" method="POST" class="goal-container">
                <?php foreach($goals as $goal): ?>
                    <button type="submit" name="goal_id" value="<?php echo $goal['id']; ?>" class="goal-btn">
                        <?php echo $goal['goal_name']; ?>
                    </button>
                <?php endforeach; ?>
            </form>
            
        </div>
    </div>

</body>
</html>