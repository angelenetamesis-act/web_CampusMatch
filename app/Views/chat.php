<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($partner['full_name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
</head>
<body class="chat-page-bg">

    <!-- Primary Navigation -->
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
        <!-- About Trigger -->
        <a href="javascript:void(0)" onclick="openModal('aboutModal')" class="nav-icon-link" title="About Us">
            <span style="font-size: 18px; font-weight: 900;"> ? </span>
        </a>

        <!-- Help Trigger -->
        <a href="javascript:void(0)" onclick="openModal('helpModal')" class="nav-icon-link" title="Help & Support">
            <span style="font-size: 18px; font-weight: 900;"> 🛟</span>
        </a>

        <a href="logout.php" style="color: red; font-weight: bold; margin-left: auto;">LOGOUT</a>
    </nav>
<!-- 1. The Outer Wrapper that creates the 2-Column Bento Layout -->
<div class="chat-layout-wrapper">

    <!-- 2. NEW SIDEBAR CELL: Active Conversations -->
    <aside class="bento-cell sidebar-cell">
        <div class="sidebar-header">
            <h3>Active Chats</h3>
        </div>
        <div class="active-list">
            <?php foreach ($matches as $m_list): ?>
                <a href="index.php?action=view_chat&match_id=<?php echo $m_list['id']; ?>" 
                   class="sidebar-item <?php echo ($m_list['id'] == $match_id) ? 'active-chat' : ''; ?>">
                    <div class="item-info">
                        <strong><?php echo htmlspecialchars($m_list['full_name']); ?></strong>
                        <small><?php echo htmlspecialchars($m_list['match_substance'] ?? $m_list['course']); ?></small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- 3. THE CHAT CONTAINER (Your Original Logic Resides Here) -->
    <div class="bento-chat-container">
        
        <!-- HEADER CELL: Partner Info & Substance -->
        <header class="bento-cell header-cell">
            <div class="header-info">
                <a href="index.php?action=messages" class="back-pill">←</a>
                <div class="user-meta">
                    <h2 class="partner-name">chatting with <?php echo htmlspecialchars($partner['full_name']); ?></h2>
                    <p class="substance-text">
                        <span class="label">SUBSTANCE:</span>
                        <?php if (isset($partner['account_status']) && $partner['account_status'] === 'suspended'): ?>
                            <span class="confidential-blur">Connection Paused, User can't see your message</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($match_data['match_substance'] ?? $partner['course']); ?>
                        <?php endif; ?>
                        <span class="streak-fire">| 🔥 <?php echo $match_data['streak_count'] ?? 0; ?></span>
                    </p>
                </div>
            </div>

            <!-- Report Flag -->
            <form action="index.php?action=report_user" method="POST" onsubmit="return confirm('Report this user for misconduct?');">
                <input type="hidden" name="reported_id" value="<?php echo $partner['id']; ?>">
                <button type="submit" class="bento-report-btn" title="Report User">🚩</button>
            </form>
        </header>

        <!-- THREAD CELL: The Conversation -->
        <main class="bento-cell thread-cell" id="chat-box">
            <?php foreach ($messages as $msg): ?>
                <?php $is_me = ($msg['sender_id'] == $current_user_id); ?>
                <div class="msg-wrapper <?php echo $is_me ? 'me' : 'them'; ?>">
                    <div class="bento-bubble">
                        <span class="bubble-sender"><?php echo $is_me ? 'you' : htmlspecialchars($msg['sender_name'] ?? 'Partner'); ?></span>
                        <p class="bubble-text"><?php echo htmlspecialchars($msg['message_text']); ?></p>
                        <small class="bubble-time"><?php echo date('H:i', strtotime($msg['created_at'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>

        <!-- INPUT CELL: Form -->
        <footer class="bento-cell input-cell">
            <form action="index.php?action=send_message" method="POST" class="bento-input-group">
                <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                <textarea name="message_text" placeholder="type a respectful message..." required></textarea>
                <button type="submit" class="bento-send-btn">SEND</button>
            </form>
        </footer>
    </div>
</div>
    <script>
        // Auto-scroll logic
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>



<!-- ABOUT US MODAL -->
<div id="aboutModal" class="bento-modal-overlay">
    <div class="bento-cell bento-modal-content">
        <header class="modal-header teal-header">
            <h3>ABOUT CAMPUS MATCH</h3>
            <button class="close-btn" onclick="closeModal('aboutModal')">[ X ]</button>
        </header>
        <div class="modal-body">
            <p><strong>Substance over Looks.</strong></p>
            <p>Campus Match is a university-exclusive platform designed for CHMSU students to connect based on shared interests and meaningful dialogue rather than mindless swiping.</p>
            <hr border="1">
            <p><small>Built with ❤️ by the DropOuts.</small></p>
        </div>
    </div>
</div>

<!-- HELP & REPORT MODAL -->
<div id="helpModal" class="bento-modal-overlay">
    <div class="bento-cell bento-modal-content">
        <header class="modal-header orange-header">
            <h3>HELP & SAFETY hub</h3>
            <button class="close-btn" onclick="closeModal('helpModal')">[ X ]</button>
        </header>
        <div class="modal-body">
            <h4>Report to Admin</h4>
            <form action="index.php?action=report_to_admin" method="POST">
                <textarea name="report_text" placeholder="Describe the issue or user misconduct..." required class="bento-input"></textarea>
                <button type="submit" class="bento-send-btn" style="margin-top: 10px; width: 100%;">SEND REPORT</button>
            </form>
            <p style="font-size: 11px; margin-top: 15px;">Reports are reviewed by CHMSU student moderators within 24 hours.</p>
        </div>
    </div>
</div>
</body>
</html>

<script src="assets/js/main.js?v=1.0"></script>