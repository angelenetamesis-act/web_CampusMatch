<?php
// public/index.php

// 1. Initialize Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../vendor/autoload.php';
use PragmaRX\Google2FAQRCode\Google2FA;
$google2fa = new Google2FA();
// 2. Load Core Configurations & Controllers
require_once '../config/constants.php';
require_once '../config/Database.php';
require_once '../app/Models/User.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/FeedController.php';
require_once '../app/Controllers/MatchController.php';
require_once '../app/Controllers/ChatController.php';
require_once '../app/Controllers/NotificationController.php';
require_once '../app/Controllers/PostController.php';

// 3. Initialize Database Connection
$database = new Database();
$db = $database->getConnection();

// 4. Instantiate Controllers and Models
$userModel = new User($db);
$authController = new AuthController($db);
$feedController = new FeedController($db);
$matchController = new MatchController($db);
$chatController = new ChatController($db);
$notifController = new NotificationController($db);
$postController = new PostController($db);
 
$current_user_id = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? 1; 
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 2. Notification Check (Now $current_user_id is defined, so this works)
$unread_count = 0;
$total_chat_unread = 0;
if ($current_user_id) {
    // Make sure $notifController was initialized above this line!
    $unread_count = $notifController->getUnreadCount($current_user_id);
    $total_chat_unread = $chatController->getTotalUnreadCount($current_user_id);
}
// 6. Fetch User Context Safely
$user_data = $userModel->getUserDetails($current_user_id);
// 7. --- THE SUSPENSION & VERIFICATION GUARD ---

// ADD 'step2', 'submit_goal', 'step3', and 'submit_interests' to this list
$allowed_actions = [
    'logout', 'login', 'login_process', 
    'setup_2fa', 'confirm_2fa_setup',
    'step2', 'submit_goal', 'step3', 'submit_interests'
];

if (!in_array($action, $allowed_actions)) {
    
    // Case A: Suspension (Remains the same)
    if ($user_data && $user_data['account_status'] === 'suspended') {
        // ... (your existing suspension logic) ...
    }

    // Case B: Manual status check (Remains the same)
    if (isset($_GET['status']) && $_GET['status'] === 'suspended') {
        include '../app/Views/suspended.php'; 
        exit(); 
    }

    // --- Case C: Verification Guard ---
    if ($current_user_id && $user_data && (int)$user_data['is_verified'] === 0) {
        header("Location: index.php?action=setup_2fa");
        exit();
    }
}
// 8. --- ROUTING SWITCH ---
switch ($action) {

    // --- AUTH / REGISTRATION FLOW ---
    case 'register_step1':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $authController->registerStepOne($_POST);
            if ($result === "Success") {
                header("Location: index.php?action=step2");
                exit;
            } else {
                $error = $result;
                include '../app/Views/register.php';
            }
        } else {
            include '../app/Views/register.php';
        }
        break;

    case 'step2':
        $goals = $authController->getGoalsList();
        include '../app/Views/step2_goals.php';
        break;

    case 'submit_goal':
        if ($authController->setGoal($_POST['goal_id'])) {
            header("Location: index.php?action=step3");
            exit;
        }
        break;

    case 'step3':
          $goal_id = $_SESSION['temp_goal_id'] ?? $user_data['current_goal_id'] ?? 0;
        $interests = $authController->getInterests($goal_id);
        $user_interests_raw = $userModel->getInterestsByUserId($current_user_id);
        $current_selected_ids = array_column($user_interests_raw, 'interest_id');
    
    include '../app/Views/step3_interests.php';
    break;

    case 'submit_interests':
    $result = $authController->finalizeInterests($_POST['interest_ids'] ?? []);
    if ($result === "Success") {
        // If the user is already logged in (Editing from Profile)
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=profile&status=updated");
        } 
        // If they are a new registration (No full session yet)
        else {
            header("Location: index.php?action=onboarding");
        }
        exit;
    } else {
        echo $result;
    }
    break;

    case 'onboarding':
        include '../app/Views/onboarding.php'; 
        break;

    case 'finish_onboarding':
        header("Location: index.php?action=uni_feed");
        exit;

    // --- NEW: 2FA & LOGIN SECURITY ---
    case 'login':
        include '../app/Views/login.php';
        break;

    case 'login_process':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $authController->login($_POST['email'], $_POST['password']);
            
            if ($result === "Success") {
                header("Location: index.php?action=uni_feed");
                exit;
            } elseif ($result === "2FA_REQUIRED") {
                header("Location: index.php?action=verify_2fa");
                exit;
            } elseif ($result === "ACCOUNT_SUSPENDED") {
                // This targets your suspension page specifically
                header("Location: index.php?action=show_suspended");
                exit;
            } else {
                $error = $result;
                include '../app/Views/login.php';
            }
        }
        break;

    case 'setup_2fa':
        $secret = $google2fa->generateSecretKey();
        $qrCodeSvg = $google2fa->getQRCodeInline('Campus Match', $user_data['email'], $secret, 200);
        include '../app/Views/setup_2fa.php';
        break;

case 'confirm_2fa_setup':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $secret = $_POST['secret'];
        $code = $_POST['one_time_password'];
        if ($google2fa->verifyKey($secret, $code)) {
            // This sets the initial state to 1
            $stmt = $db->prepare("UPDATE users SET google2fa_secret = ?, two_fa_enabled = 1 WHERE id = ?");
            $stmt->execute([$secret, $current_user_id]);
            
            $userModel->verifyUser($current_user_id);
            $userModel->resetFailedAttempts($current_user_id);
            
            $_SESSION['2fa_verified'] = true; 
            header("Location: index.php?action=onboarding&status=verified");
            exit;
        
        } else {
                $error = "Invalid 6-digit code. Please try again.";
                $qrCodeSvg = $google2fa->getQRCodeInline('Campus Match', $user_data['email'], $secret, 200);
                include '../app/Views/setup_2fa.php';
            }
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?action=login");
        exit;
        break;

    // --- FEEDS LOGIC ---
    case 'uni_feed':
        $view_type = 'uni';
        $students = $feedController->getUniversityFeed($current_user_id);
        include '../app/Views/feeds.php';
        break;

    case 'my_feed':
        $view_type = 'my';
        $filters = $_POST;
        $available_interests = $userModel->getInterestsByGoal($user_data['current_goal_id'] ?? 0);
        $students = $feedController->getMyFeed($current_user_id, $user_data['current_goal_id'] ?? 0, $filters);
        include '../app/Views/feeds.php';
        break;

    // --- MATCHING & WAVES ---
case 'wave':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receiverId = $_POST['receiver_id'];
            // Capture the intended return path from the form
            $return_to = $_POST['redirect_to'] ?? 'uni_feed'; 
            
            $result = $matchController->sendWave($current_user_id, $receiverId);
            
            if ($result === "Matched") {
                // Stay on current feed but trigger the "YOU MATCH!" animation
                $redirect = "$return_to&match_overlay=true";
            } else {
                // Standard redirect for a single wave
                $redirect = "$return_to&status=waved";
            }
            
            header("Location: index.php?action=$redirect");
            exit();
        }
        break;
    case 'unwave':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capture the intended return path from the form
            $return_to = $_POST['redirect_to'] ?? 'uni_feed';
            
            $matchController->unwave($current_user_id, $_POST['receiver_id']);
            
            // Return to the source feed instead of hardcoded uni_feed
            header("Location: index.php?action=$return_to");
            exit();
        }
        break;

    // --- INTERACTIVE NOTIFICATIONS ROUTING ENDPOINTS ---
    case 'accept_wave':
        $notifId = $_GET['id'] ?? null;
        $actorId = $_GET['actor_id'] ?? null;
        
        if ($actorId) {
            // 1. Fire the wave mapping back to create the match connection row
            $result = $matchController->sendWave($current_user_id, $actorId);
            
            // 2. Archive the alert element instead of hard deleting it
            if ($notifId) {
                $notifController->updateStatus($notifId, $current_user_id, 'accepted');
            }
            
            if ($result === "Matched") {
                header("Location: index.php?action=notifications&match_overlay=true");
                exit();
            }
        }
        header("Location: index.php?action=notifications");
        exit();
        break;

    case 'decline_wave':
        $notifId = $_GET['id'] ?? null;
        $actorId = $_GET['actor_id'] ?? null;
        
        if ($actorId) {
            // 1. Remove the pending line from the waves mapping table
            $matchController->unwave($actorId, $current_user_id);
        }
        
        if ($notifId) {
            // 2. Shift status column value to 'declined' archive category slot
            $notifController->updateStatus($notifId, $current_user_id, 'declined');
        }
        
        header("Location: index.php?action=notifications");
        exit();
        break;

    // --- MESSAGING & CHAT ---
    case 'messages':
        $matches = $matchController->getMyMatches($current_user_id);
        include '../app/Views/messages.php';
        break;
    
    case 'view_chat':
        $match_id = $_GET['match_id'];
        $matches = $matchController->getMyMatches($current_user_id);
        $partner = $db->prepare("SELECT u.id, u.full_name, u.course, u.account_status FROM users u 
                                 JOIN matches m ON (u.id = m.user_one_id OR u.id = m.user_two_id)
                                 WHERE m.id = ? AND u.id != ?");
        $partner->execute([$match_id, $current_user_id]);
        $partner = $partner->fetch();
        $user = $user_data; 
        $match_info = $db->prepare("SELECT streak_count, match_substance FROM matches WHERE id = ?");
        $match_info->execute([$match_id]);
        $match_data = $match_info->fetch();
        $chatController->markAsRead($match_id, $current_user_id);
        $messages = $chatController->getChatHistory($match_id);
        include '../app/Views/chat.php';
        break;

    case 'send_message':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $chatController->sendMessage($_POST['match_id'], $_POST['message_text']);
            if ($status === "BLOCKED") {
                header("Location: index.php?status=suspended");
            } elseif ($status === "WARNING_STRIKE_ONE") {
                header("Location: index.php?action=view_chat&match_id=" . $_POST['match_id'] . "&warning=strike_one");
            } else {
                header("Location: index.php?action=view_chat&match_id=" . $_POST['match_id']);
            }
            exit();
        }
        break;

    // --- PROFILE & SETTINGS ---
    case 'profile':
        $current_interests = $userModel->getInterestsByUserId($current_user_id);
        $all_goals = $authController->getGoalsList();
        $user = $user_data; 
        include '../app/Views/profile.php';
        break;

    case 'update_goal':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->updateGoalAndReset($current_user_id, $_POST['new_goal_id']);
            header("Location: index.php?action=step3");
            exit();
        }
        break;

    // --- FREEDOM WALL & POSTS ---
    case 'freedom_wall':
        $posts = $postController->getActivePosts();
        include '../app/Views/freedom_wall.php';
        break;

    case 'create_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $postController->createPost($current_user_id, $_POST['content']);
            if ($result === "BLOCKED") {
                header("Location: index.php?status=suspended");
            } else {
                header("Location: index.php?action=freedom_wall");
            }
            exit();
        }
        break;

    case 'edit_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $postController->updatePost($_POST['post_id'], $current_user_id, $_POST['content']);
            if ($result === "BLOCKED") {
                header("Location: index.php?status=suspended");
            } else {
                header("Location: index.php?action=freedom_wall");
            }
            exit();
        }
        break;

    case 'delete_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postController->deletePost($_POST['post_id'], $current_user_id);
            header("Location: index.php?action=freedom_wall");
            exit();
        }
        break;

    case 'react_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postController->toggleReaction($_POST['post_id'], $current_user_id, $_POST['type']);
            header("Location: index.php?action=freedom_wall");
            exit();
        }
        break;

    // --- NOTIFICATIONS & SYSTEM ---
    case 'notifications':
        $notifications = $notifController->checkUnread($current_user_id);
        $notifController->markAsRead($current_user_id);
        include '../app/Views/notifications.php';
        break;

    case 'check_notifications':
        $notifications = $notifController->checkUnread($current_user_id);
        header('Content-Type: application/json');
        echo json_encode($notifications);
        $notifController->markAsRead($current_user_id);
        exit();
        break;

    case 'delete_notif':
    $notifId = $_GET['id'] ?? null;
    if ($notifId) {
        // CHANGED: $notificationController -> $notifController
        $notifController->deleteNotification($notifId, $_SESSION['user_id']);
    }
    header("Location: index.php?action=notifications");
    exit;
    break;

    case 'report_user':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chatController->reportUser($current_user_id, $_POST['reported_id']);
            header("Location: index.php?action=messages&status=reported");
            exit();
        }
        break;

case 'verify_2fa':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Use the ID specifically stored during the login process/ ange
        $pending_id = $_SESSION['2fa_pending_user_id'] ?? null;
        $secret = $_SESSION['2fa_secret'] ?? null;
        $code = $_POST['one_time_password'] ?? '';

        if ($pending_id && $google2fa->verifyKey($secret, $code)) {
            
            // 1. PERFORM THE HANDSHAKE
            // We use $pending_id directly to ensure the correct row is updated
            $sql = "UPDATE users SET two_fa_enabled = 2 WHERE id = ?";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$pending_id]);

            if ($success) {
                // 2. PROMOTE TO FULL SESSION
                $_SESSION['user_id'] = $pending_id;
                $_SESSION['2fa_verified'] = true;
                
                // 3. CLEANUP
                unset($_SESSION['2fa_pending_user_id'], $_SESSION['2fa_secret']);
                
                header("Location: index.php?action=uni_feed");
                exit;
            } else {
                $error = "Database update failed. Please contact admin.";
            }
        } else {
            $error = "Invalid 2FA code. Please try again.";
            include '../app/Views/verify_2fa.php';
        }
    } else {
        include '../app/Views/verify_2fa.php';
    }
    break;


    // Inside the switch ($action) block in index.php
case 'show_suspended':
    // We only show this if there is a suspended user ID in the session
    if (isset($_SESSION['suspended_user_id'])) {
        // Fetch the details one last time to ensure the timer is accurate
        $user_details = $userModel->getUserDetails($_SESSION['suspended_user_id']);
        include '../app/Views/suspended.php';
    } else {
        // If someone tries to access this URL directly without being suspended, send them home
        header("Location: index.php?action=login");
    }
    break;


    // --- DEFAULT REDIRECT ---
    default:
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=uni_feed");
        } else {
            include '../app/Views/login.php';
        }
        break;
}