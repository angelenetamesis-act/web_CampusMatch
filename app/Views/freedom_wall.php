<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freedom Wall - Campus Match</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.5">
</head>
<body class="freedom-wall-page">
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


<div class="wall-header">
    <h1>the freedom wall</h1>
    <div class="rules-sticker">
        ⚠️ posts expire after 3 hours. keep it respectful.
    </div>
</div>

<!-- Create Post Form -->
<div class="post-creator-card">
    <form action="index.php?action=create_post" method="POST">
        <textarea name="content" placeholder="what's on your mind? (course/year will be visible)" required></textarea>
        <button type="submit" class="brutal-btn" style="width: 100%; background: #32CD32 !important;">PUSH TO WALL 📌</button>
    </form>
</div>

<div class="freedom-wall-container">
    <?php if (empty($posts)): ?>
        <p style="grid-column: 1/-1; text-align: center; font-family: 'Arial Black';">the wall is empty... for now. be the first to post!</p>
    <?php else: ?>
        <?php foreach ($posts as $index => $post): ?>
            <!-- We add a slight random rotation in CSS based on index -->
            <div class="post-it-note">
                <div class="post-it-meta">
                    <span class="student-tag"><?php echo htmlspecialchars($post['course']); ?> - yr <?php echo $post['year_level']; ?></span>
                    
                    <?php if ($post['user_id'] == $current_user_id): ?>
                        <div class="owner-controls">
                            <button onclick="toggleEdit(<?php echo $post['id']; ?>)" class="icon-btn">✏️</button>
                            <form action="index.php?action=delete_post" method="POST" style="display:inline;" onsubmit="return confirm('Delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="icon-btn" style="background: #ff4444;">🗑️</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="content-<?php echo $post['id']; ?>" class="post-it-content">
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                </div>

                <div id="edit-form-<?php echo $post['id']; ?>" style="display:none;" class="edit-mode">
                    <form action="index.php?action=edit_post" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <textarea name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                        <div style="display: flex; gap: 5px; margin-top: 5px;">
                            <button type="submit" class="mini-btn">SAVE</button>
                            <button type="button" onclick="toggleEdit(<?php echo $post['id']; ?>)" class="mini-btn" style="background: #ccc;">CANCEL</button>
                        </div>
                    </form>
                </div>

                <div class="post-it-footer">
                    <div class="reaction-bar">
                        <form action="index.php?action=react_post" method="POST" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="type" value="up">
                            <button type="submit" class="react-btn up">👍 <?php echo $post['ups']; ?></button>
                        </form>

                        <form action="index.php?action=react_post" method="POST" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="type" value="down">
                            <button type="submit" class="react-btn down">👎 <?php echo $post['downs']; ?></button>
                        </form>
                    </div>
                    <span class="post-time"><?php echo date('H:i', strtotime($post['created_at'])); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
/* Your existing toggleEdit function remains identical */
function toggleEdit(postId) {
    const displayDiv = document.getElementById('content-' + postId);
    const editDiv = document.getElementById('edit-form-' + postId);
    if (editDiv.style.display === "none") {
        displayDiv.style.display = "none";
        editDiv.style.display = "block";
    } else {
        displayDiv.style.display = "block";
        editDiv.style.display = "none";
    }
}
</script>


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