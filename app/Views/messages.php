<!-- app/Views/messages.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Match - My Matches</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.2">
</head>
<body class="messages-page-body">
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
            <span style="font-size: 18px; font-weight: 900;">  ?  </span>
        </a>

        <!-- Global Help Trigger -->
        <a href="javascript:void(0)" onclick="openModal('helpModal')" class="nav-icon-link" title="Help & Support" style="text-decoration: none; color: inherit;">
            <span style="font-size: 18px; font-weight: 900;"> 🛟 </span>
        </a>

    
    <a href="logout.php" style="color: red; font-weight: bold; margin-left: auto;">LOGOUT</a>
</nav>

<h1>My Matches</h1>
<p>These are students you have mutually waved at. Substance over looks!</p>

<div class="matches-container">
    <?php if (empty($matches)): ?>
        <p>No matches yet. Start waving on your feed!</p>
    <?php else: ?>
        <?php foreach ($matches as $match): ?>
            <?php 
                // FETCH UNREAD COUNT FOR EACH CARD
                $unread = $chatController->getUnreadCount($match['id'], $current_user_id); 
            ?>
            
            <div class="match-card" style="border: 2px solid green; margin: 10px; padding: 10px; width: 300px; display: inline-block; position: relative; background: white; vertical-align: top;">
                
               <!-- UPDATED UNREAD BADGE -->
<?php if ($unread > 0): ?>
    <div style="
        position: absolute; 
        top: 8px; 
        right: 8px; 
        background: #ff4d4d; 
        color: white; 
        min-width: 20px; 
        height: 20px; 
        border-radius: 10px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 11px; 
        font-weight: bold; 
        padding: 0 6px;
        border: 1.5px solid white; 
        box-shadow: 0 0 10px rgba(255, 77, 77, 0.5); 
        z-index: 100;
        pointer-events: none;
    ">
        <?php echo $unread; ?>
    </div>
<?php endif; ?>

                <h3><?php echo htmlspecialchars($match['full_name']); ?></h3>
                <p><strong>Goal:</strong> <?php echo $match['goal_name']; ?></p>
                <p><strong>Course:</strong> <?php echo $match['course']; ?> - Yr <?php echo $match['year_level']; ?></p>
                
                <div style="font-size: 24px;">
                    <a href="index.php?action=view_chat&match_id=<?php echo $match['id']; ?>" style="text-decoration: none;">
                        💬 <button style="
                            cursor: pointer; 
                            padding: 5px 15px; 
                            border-radius: 4px; 
                            border: 2px solid #1a1a1a;
                            font-weight: bold;
                            transition: all 0.2s ease;
                            <?php if ($unread > 0): ?>
                                background: #ccffcc; 
                                box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
                                border-color: #008000;
                            <?php else: ?>
                                background: #f0f0f0;
                            <?php endif; ?>
                        ">
                            Chat Now <?php echo ($unread > 0) ? '📩' : ''; ?>
                        </button>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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