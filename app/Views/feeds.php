<!-- app/Views/feeds.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Match - Feed</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.2">
</head>
<body class="<?php echo ($view_type == 'uni') ? 'uni-feed-page' : 'my-feed-page'; ?>">

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


<h1><?php echo ($view_type == 'uni') ? "University Feed" : "My Feed"; ?></h1>

<?php if ($view_type == 'my'): ?>
    <div class="filter-box">
        <form method="POST" action="index.php?action=my_feed">
            <!-- Row 1: Course & Basic Info -->
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" name="course" placeholder="Filter by Course (e.g., BSIT)" 
                       value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>" style="flex: 2;">
                
                <input type="number" name="age" placeholder="Age" 
                       value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>" style="flex: 1;">
                
                <input type="number" name="year_level" placeholder="Year" min="1" max="5" 
                       value="<?php echo htmlspecialchars($_POST['year_level'] ?? ''); ?>" style="flex: 1;">
            </div>
            
            <p style="font-size: 12px; margin-bottom: 10px;">Your Interests | <a href="index.php?action=profile" style="color: #FF8C00;">Update in Profile</a></p>
            
            <div class="interest-selection">
                <?php foreach($available_interests as $interest): ?>
                    <?php $checked = (isset($_POST['interest_ids']) && in_array($interest['id'], $_POST['interest_ids'])) ? 'checked' : ''; ?>
                    <label class="custom-checkbox">
                        <input type="checkbox" name="interest_ids[]" value="<?php echo $interest['id']; ?>" <?php echo $checked; ?>> 
                        <?php echo $interest['interest_name']; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 15px; display: flex; align-items: center; gap: 15px;">
                <button type="submit" class="brutal-btn">APPLY FILTERS</button>
                <a href="index.php?action=my_feed" class="clear-btn">CLEAR ALL</a>
            </div>
        </form>
    </div> <!-- FILTER BOX ENDS HERE -->
<?php endif; ?>

<!-- CARDS ARE NOW OUTSIDE THE FILTER BOX -->
<div class="card-container">
    <?php if (empty($students)): ?>
        <div class="student-card" style="width: auto; padding: 20px;">
            <p>No students found matching your criteria.</p>
        </div>
    <?php else: ?>
        <?php foreach ($students as $student): ?>
            <div class="student-card">
                <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                <div style="padding: 10px; text-align: left;">
                    <p><strong>Goal:</strong> <?php echo $student['goal_name']; ?></p>
                    <p><strong>Course:</strong> <?php echo $student['course']; ?> - Yr <?php echo $student['year_level']; ?></p>
                    <p><strong>Age:</strong> <?php echo $student['age']; ?></p>

                    <p style="margin-top: 10px;"><strong>Interests:</strong><br>
                        <?php 
                            $interests = $userModel->getInterestsByUserId($student['id']);
                            foreach($interests as $i) {
                                echo "<span class='interest-tag'>#" . htmlspecialchars($i['interest_name']) . "</span> ";
                            }
                        ?>
                    </p>
                </div>

                <?php 
                $checkWave = $db->prepare("SELECT * FROM waves WHERE sender_id = ? AND receiver_id = ?");
                $checkWave->execute([$current_user_id, $student['id']]);
                $isWaved = $checkWave->rowCount() > 0;
                ?>

                <div style="margin-top: auto; padding: 10px;">
                    <?php if (!$isWaved): ?>
                        <form action="index.php?action=wave" method="POST">
                            <input type="hidden" name="receiver_id" value="<?php echo $student['id']; ?>">
                            <input type="hidden" name="redirect_to" value="<?php echo $view_type; ?>_feed">
                            <button type="submit" class="brutal-btn" style="width: 100%;">👋 Wave</button>
                        </form>
                    <?php else: ?>
                        <form action="index.php?action=unwave" method="POST" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="receiver_id" value="<?php echo $student['id']; ?>">
                            <input type="hidden" name="redirect_to" value="<?php echo $view_type; ?>_feed">
                            <button type="submit" class="clear-btn" style="width: 100%; background: #ff4d4d !important; color: white !important;">Undo Wave</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
 <script>
function checkNotifs() {
    // This fetches a small JSON response from your server
    fetch('index.php?action=check_notifications_ajax')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                // Update the badge number in the Nav
                const badge = document.querySelector('.notif-badge');
                if (badge) {
                    badge.innerText = data.count;
                    badge.style.display = 'inline-block';
                }
                
                // Show a Neo-Brutalist Toast if there is a NEW notification
                if (data.new_notif) {
                    showToast(data.message);
                }
            }
        });
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.style = "position:fixed; bottom:20px; right:20px; background:#FF8C00; border:4px solid black; padding:15px; box-shadow:6px 6px 0px black; font-weight:bold; z-index:1000;";
    toast.innerText = "👋 " + msg;
    document.body.appendChild(toast);
    
    // Disappear after 4 seconds
    setTimeout(() => toast.remove(), 4000);
}

// Check every 10 seconds
setInterval(checkNotifs, 10000);
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
 <script src="assets/js/main.js?v=1.0"></script>


<?php if (isset($_GET['match_overlay']) && $_GET['match_overlay'] === 'true'): ?>
<div id="matchOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; justify-content: center; align-items: center; cursor: pointer; overflow: hidden;" onclick="this.style.display='none'">
    
    <!-- Floating Background Icons -->
    <div class="match-icon" style="top: 15%; left: 10%; font-size: 40px;">💖</div>
    <div class="match-icon" style="top: 20%; right: 15%; font-size: 35px; animation-delay: 0.5s;">✨</div>
    <div class="match-icon" style="bottom: 20%; left: 20%; font-size: 30px; animation-delay: 1s;">🔥</div>
    <div class="match-icon" style="bottom: 15%; right: 10%; font-size: 45px; animation-delay: 1.5s;">⚡</div>

    <div style="background: #19e069; border: 8px solid #000; padding: 60px 40px; text-align: center; box-shadow: 20px 20px 0px #000; transform: rotate(-2deg); animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;">
        
        <!-- Icon Header -->
        <div style="font-size: 80px; margin-bottom: 10px; filter: drop-shadow(5px 5px 0px #000);">🤝</div>
        
        <h1 style="font-size: 60px; color: #20262b; text-shadow: 6px 6px 0px #000; margin: 0; line-height: 1;">IT'S A MATCH!</h1>
        
        <div style="background: #000; color: #19e069; display: inline-block; padding: 5px 15px; font-weight: 900; margin-top: 15px; transform: rotate(2deg);">
            CONNECTION FOUND
        </div>

        <p style="font-size: 20px; font-weight: bold; margin: 25px 0; color: #000;">
            You and this student have shared interests!
        </p>

        <div style="display: flex; gap: 20px; justify-content: center; margin-top: 30px;">
            <a href="index.php?action=messages" style="background: #fff; color: #000; padding: 15px 30px; border: 4px solid #000; font-weight: 900; text-decoration: none; box-shadow: 5px 5px 0px #000; transition: 0.2s;">
                GO TO CHAT
            </a>
            <button onclick="document.getElementById('matchOverlay').style.display='none'" style="background: #000; color: #fff; padding: 15px 30px; border: 4px solid #000; font-weight: 900; cursor: pointer; box-shadow: 5px 5px 0px #444;">
                KEEP BROWSING
            </button>
        </div>
    </div>
</div>

<style>
/* Main Pop Animation */
@keyframes popIn {
    0% { transform: scale(0.5) rotate(5deg); opacity: 0; }
    100% { transform: scale(1) rotate(-2deg); opacity: 1; }
}

/* Floating Background Icons Animation */
.match-icon {
    position: absolute;
    user-select: none;
    animation: floatAround 3s ease-in-out infinite;
    filter: drop-shadow(3px 3px 0px #000);
}

@keyframes floatAround {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-20px) scale(1.2); }
}

/* Hover effects for buttons */
#matchOverlay a:hover {
    transform: translate(-2px, -2px);
    box-shadow: 7px 7px 0px #000;
}
</style>
<?php endif; ?>