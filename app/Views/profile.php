<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Campus Match</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
</head>
<body class="profile-page">
<nav>
    <a href="index.php?action=uni_feed">UNIVERSITY FEED</a> 
    <a href="index.php?action=my_feed">MY FEED</a> 
    <a href="index.php?action=messages" style="position: relative;">
    MESSAGES
    <?php if ($total_chat_unread > 0): ?>
        <span style="
            position: absolute;
            top: -5px;
            right: -10px;
            background: #ff4d4d;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 1.5px solid white;
            box-shadow: 0 0 5px rgba(255, 77, 77, 0.8);
        "></span>
    <?php endif; ?>
</a>
    <a href="index.php?action=profile">MY PROFILE</a>
    <a href="index.php?action=freedom_wall">FREEDOM WALL</a> 
<!-- UPDATED: NOTIFS ICON -->
    <a href="index.php?action=notifications" class="nav-item <?php echo ($_GET['action'] == 'notifications') ? 'nav-active' : ''; ?>" style="position: relative; display: flex; align-items: center; justify-content: center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <?php if ($unread_count > 0): ?>
            <span class="notif-badge" style="position: absolute; top: -5px; right: -8px; background: #ff4d4d; color: white; font-size: 9px; font-weight: bold; padding: 1px 5px; border-radius: 50%; border: 2px solid white; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; min-width: 12px; height: 12px;">
                <?php echo $unread_count; ?>
            </span>
        <?php endif; ?>
    </a>
    
       <div style="margin-left: auto; display: flex; align-items: center; gap: 15px;">
        <!-- Global About Trigger -->
        <a href="javascript:void(0)" onclick="openModal('aboutModal')" class="nav-icon-link" title="About Us" style="text-decoration: none; color: inherit;">
            <span style="font-size: 18px; font-weight: 900;"> ? </span>
        </a>

        <!-- Global Help Trigger -->
        <a href="javascript:void(0)" onclick="openModal('helpModal')" class="nav-icon-link" title="Help & Support" style="text-decoration: none; color: inherit;">
            <span style="font-size: 18px; font-weight: 900;"> 🛟 </span>
        </a>

    
    <a href="logout.php" style="color: red; font-weight: bold; margin-left: auto;">LOGOUT</a>
</nav>
<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <h1 class="profile-name">
                <?php echo htmlspecialchars($user['full_name']); ?>
                <!-- NEW: Campus Verified Badge -->
                <?php if ($user['is_verified'] == 1): ?>
                    <span style="
                        display: inline-flex;
                        align-items: center;
                        background: #007bff;
                        color: white;
                        font-size: 10px;
                        padding: 3px 10px;
                        border: 3px solid #000;
                        border-radius: 8px;
                        vertical-align: middle;
                        margin-left: 10px;
                        box-shadow: 3px 3px 0px #000;
                        text-transform: uppercase;
                        font-weight: 900;
                    ">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" style="margin-right: 4px;">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                        Campus Verified
                    </span>
                <?php endif; ?>
            </h1>
            <p class="profile-subtext"><?php echo htmlspecialchars($user['course']); ?> - Yr <?php echo $user['year_level']; ?></p>
        </div>

                    <div class="profile-content">
    <!-- Account Info Capsules -->

    <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
        <div style="background: #fd5b20; border: 3px solid #000; padding: 10px; margin-bottom: 20px; font-weight: 900; box-shadow: 4px 4px 0px #000; text-align: center;">
            ✅ Interests Updated Successfully!
        </div>
    <?php endif; ?>

    <div class="stat-capsule" style="margin-bottom: 20px; display: block; text-align: center;">
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
    </div>

    <div class="profile-section">
        <h3>Current Goal: <?php echo htmlspecialchars($user['goal_name']); ?></h3>
        <div class="interest-sticker-cloud">
            <?php foreach($current_interests as $i): ?>
                <span class="interest-tag">#<?php echo htmlspecialchars($i['interest_name']); ?></span>
            <?php endforeach; ?>
        </div>
    </div>

            <hr style="border: 2px solid #000; margin: 25px 0;">

            <!-- NEW: Security Handshake Section -->
            <div class="settings-block" style="background: #fff; border: 3px solid #000; padding: 20px; box-shadow: 6px 6px 0px #000; margin-bottom: 25px;">
                <h4 style="margin-bottom: 10px;">🛡️ Security Status</h4>
                <?php if (!$user['two_fa_enabled']): ?>
                    <p style="font-size: 12px; margin-bottom: 15px;">Enable 2FA to protect your account and earn your verification badge.</p>
                    <a href="index.php?action=setup_2fa" class="brutal-btn" style="background: #ffc107 !important; text-decoration: none; display: block; text-align: center; border: 3px solid #000; box-shadow: 4px 4px 0px #000;">
                        Setup 2FA Authentication
                    </a>
                <?php else: ?>
                    <div style="background: #A3E635; border: 3px solid #000; padding: 10px; text-align: center; font-weight: 900; box-shadow: 4px 4px 0px #000;">
                        ✅ 2FA PROTECTED
                    </div>
                <?php endif; ?>
            </div>

            <!-- UPDATE GOAL LOGIC -->
            <div class="settings-block">
                <h4>Change Your Goal</h4>
                <p style="font-size: 11px; color: #d9534f; font-weight: 900; margin-bottom: 10px;">⚠️ ACTION WIPES YOUR CURRENT INTERESTS</p>
                <form action="index.php?action=update_goal" method="POST" onsubmit="return confirm('Changing your goal will PERMANENTLY WIPE your current 5 interests. Do you want to proceed?');">
                    <select name="new_goal_id" class="brutal-select">
                        <?php foreach($all_goals as $goal): ?>
                            <option value="<?php echo $goal['id']; ?>" <?php echo ($goal['id'] == $user['current_goal_id']) ? 'selected' : ''; ?>>
                                <?php echo $goal['goal_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="brutal-btn" style="width: 100%; margin-top: 10px; background: #FF8C00 !important;">Update & Reset</button>
                </form>
            </div>

            <div class="settings-block" style="margin-top: 20px;">
                <h4>Update Interests</h4>
                <p style="font-size: 12px; margin-bottom: 10px;">Keep your goal but change your tags.</p>
                <a href="index.php?action=step3" class="clear-btn" style="display: block; text-align: center; text-decoration: none; border: 3px solid #000; padding: 10px; background: #fff; box-shadow: 4px 4px 0px #000; color: #000; font-weight: 900;">Edit My 5 Interests</a>
            </div>
        </div>
    </div>
</div>
<!-- GLOBAL ABOUT US MODAL -->
<div id="aboutModal" class="bento-modal-overlay" onclick="closeModalOnOut(event, 'aboutModal')">
    <div class="bento-cell bento-modal-content">
        <header class="modal-header teal-header">
            <h3 style="margin: 0; font-size: 16px;">ABOUT CAMPUS MATCH</h3>
            <button class="close-btn" onclick="closeModal('aboutModal')">[ X ]</button>
        </header>
        <div class="modal-body">
            <p><strong>Substance over Looks.</strong></p>
            <p>Campus Match is a university-exclusive platform for CHMSU students to connect based on shared interests and meaningful dialogue.</p>
            <hr style="border: 1px solid #000; margin: 15px 0;">
            <p><small>Built with ❤️ by the DropOuts.</small></p>
        </div>
    </div>
</div>

<!-- GLOBAL HELP & REPORT MODAL -->
<div id="helpModal" class="bento-modal-overlay" onclick="closeModalOnOut(event, 'helpModal')">
    <div class="bento-cell bento-modal-content">
        <header class="modal-header orange-header">
            <h3 style="margin: 0; font-size: 16px;">HELP & SAFETY HUB</h3>
            <button class="close-btn" onclick="closeModal('helpModal')">[ X ]</button>
        </header>
        <div class="modal-body">
            <h4>Report to Admin</h4>
            <form action="index.php?action=report_to_admin" method="POST">
                <textarea name="report_text" placeholder="Describe the issue or user misconduct..." required class="bento-input"></textarea>
                <button type="submit" class="bento-send-btn" style="margin-top: 10px; width: 100%;">SEND REPORT</button>
            </form>
            <p style="font-size: 11px; margin-top: 15px; opacity: 0.7;">Reports are reviewed by student moderators within 24 hours.</p>
        </div>
    </div>
</div>
</body>
</html>
<script src="assets/js/main.js?v=1.0"></script>