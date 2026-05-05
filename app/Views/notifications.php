<!-- app/Views/notifications.php -->
<link rel="stylesheet" href="assets/css/style.css">
<nav>
    <a href="index.php?action=uni_feed">UNIVERSITY FEED</a> 
    <a href="index.php?action=my_feed">MY FEED</a> 
    <a href="index.php?action=messages" style="position: relative;">
        MESSAGES
        <?php if ($total_chat_unread > 0): ?>
            <span style="position: absolute; top: -5px; right: -10px; background: #ff4d4d; width: 8px; height: 8px; border-radius: 50%; border: 1.5px solid white; box-shadow: 0 0 5px rgba(255, 77, 77, 0.8);"></span>
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








<h1>Recent Activity</h1>
<div class="card-container">
    <?php if (empty($notifications)): ?>
        <p>No new activity. Why not send a wave to someone?</p>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <div class="student-card" style="
                padding: 15px; 
                margin-bottom: 15px; 
                width: 100%; 
                display: flex; 
                justify-content: space-between; 
                align-items: center;
                /* Logic: If is_read is 0, give it a subtle orange tint */
                background: <?php echo ($n['is_read'] == 0) ? '#fffef0' : '#fff'; ?>;
                border-left: 8px solid <?php echo ($n['is_read'] == 0) ? '#FF8C00' : '#000'; ?>;
            ">
                <div>
                    <p style="margin: 0; font-weight: <?php echo ($n['is_read'] == 0) ? '900' : '500'; ?>;">
                        <?php if($n['is_read'] == 0): ?>
                            <span style="background: #FF8C00; color: #000; font-size: 10px; padding: 2px 5px; margin-right: 5px; border: 1px solid #000;">NEW</span>
                        <?php endif; ?>
                        <strong><?php echo htmlspecialchars($n['actor_name']); ?></strong> 
                        <?php echo htmlspecialchars($n['message']); ?>
                    </p>
                    <small style="opacity: 0.6;">Received: <?php echo $n['created_at']; ?></small>
                </div>

                <div style="display: flex; gap: 10px;">
                    <!-- Direct Link to Delete (Matches index.php logic you'll add) -->
                    <a href="index.php?action=delete_notif&id=<?php echo $n['id']; ?>" 
                       style="color: red; text-decoration: none; font-weight: 900;" 
                       onclick="return confirm('Delete this record?')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
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
<script src="assets/js/main.js?v=1.0"></script>