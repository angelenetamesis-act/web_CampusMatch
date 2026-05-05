<!-- app/Views/onboarding.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Campus Match</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9.9">
</head>
<body class="onboarding-page">

    <div class="onboarding-container">
        <h1 class="onboarding-title">Welcome to Campus Match!</h1>
        <p class="onboarding-intro">Before you start building a memorable college experience, please agree to our community guidelines:</p>
        
        <div class="guidelines-scrollbox">
            <h3 style="margin-top: 0; text-transform: uppercase;">Community Manifesto:</h3>
            <ul>
                <li><strong>Be Respectful:</strong> Treat every student with kindness. Harassment, bullying, or hate speech will result in an immediate permanent ban.</li>
                <li><strong>Substance Over Looks:</strong> Our platform is intent-based. Focus on shared academic goals and genuine interests to build lasting connections.</li>
                <li><strong>Authenticity:</strong> You are verified via 2FA as **<?php echo htmlspecialchars($user_data['full_name']); ?>**. Maintain this integrity; impersonation is a serious violation.</li>
                <li><strong>Freedom Wall Ethics:</strong> Use the Freedom Wall for expression, but avoid spamming or sharing sensitive university information.</li>
                <li><strong>Safety First:</strong> Never share your 2FA secret key or login credentials with anyone, even if they claim to be "Campus Match Support."</li>
            </ul>
            <p style="font-size: 12px; margin-top: 20px; font-weight: bold; color: #d9534f;">
                By clicking the button below, you agree to follow these rules and our official Terms & Conditions.
            </p>
        </div>

        <div class="onboarding-footer">
            <label style="display: block; margin-bottom: 20px; font-weight: 900; cursor: pointer;">
                <input type="checkbox" id="consent-check" style="transform: scale(1.5); margin-right: 10px;">
                I AGREE TO THE CAMPUS STANDARDS
            </label>

            <!-- Action remains pointing to finish_onboarding -->
            <a href="index.php?action=finish_onboarding" id="enter-btn" class="agree-btn">
                I Agree & Enter Campus Match
            </a>
        </div>
    </div>

    <script>
        // Logic to ensure they can't enter without checking the box
        const enterBtn = document.getElementById('enter-btn');
        const checkbox = document.getElementById('consent-check');

        enterBtn.addEventListener('click', function(e) {
            if (!checkbox.checked) {
                e.preventDefault();
                alert('Please check the box to confirm you agree to the rules.');
            }
        });
    </script>
</body>
</html>