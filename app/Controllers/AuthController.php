<?php
// app/Controllers/AuthController.php

require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function registerStepOne($data) {
        if (empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
            return "Please fill in all required fields.";
        }
        if (!$this->user->isValidEmail($data['email'])) {
            return "Error: You must use a @chmsu.edu.ph email address.";
        }
        if ($this->user->emailExists($data['email'])) {
            return "Error: This email is already registered.";
        }

        $this->user->full_name = $data['full_name'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];
        $this->user->age = $data['age'];
        $this->user->course = $data['course'];
        $this->user->year_level = $data['year_level'];

        if ($this->user->create()) {
            $_SESSION['temp_user_id'] = $this->user->id;
            return "Success";
        }
        return "Something went wrong.";
    }

    public function getGoalsList() {
        return $this->user->getGoals();
    }

    public function setGoal($goalId) {
        if (isset($_SESSION['temp_user_id'])) {
            $this->user->updateGoal($_SESSION['temp_user_id'], $goalId);
            $_SESSION['temp_goal_id'] = $goalId;
            return true;
        }
        return false;
    }

    public function getInterests($goalId) {
        return $this->user->getInterestsByGoal($goalId);
    }

    public function finalizeInterests($interestIds) {
        if (count($interestIds) > 5) {
            return "You can only choose up to 5 interests.";
        }
        if (isset($_SESSION['temp_user_id'])) {
            $this->user->saveInterests($_SESSION['temp_user_id'], $interestIds);
            return "Success";
        }
        return "Session expired.";
    }

    public function updateGoalAndReset($userId, $newGoalId) {
    // 1. Update the goal in the users table
    $this->user->updateGoal($userId, $newGoalId);
    
    // 2. Wipe the existing interests for this user
    $query = "DELETE FROM user_interests WHERE user_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$userId]);

    // 3. Set the session goal so Step 3 knows what interests to show
    $_SESSION['temp_goal_id'] = $newGoalId;
    $_SESSION['temp_user_id'] = $userId;
    
    return true;
}

public function login($email, $password) {
    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        
    // --- THE SUSPENSION GATE ---
        // If status is suspended, we check if they can be let back in yet
        if ($user['account_status'] === 'suspended') {
            $now = new DateTime();
            $suspensionEnd = new DateTime($user['suspension_end_datetime']);

            if ($now < $suspensionEnd) {
                $_SESSION['suspended_user_id'] = $user['id'];
                return "ACCOUNT_SUSPENDED"; 
            } else {
                // Auto-reactivate[cite: 1]
                $this->db->prepare("UPDATE users SET account_status = 'active', suspension_end_datetime = NULL WHERE id = ?")
                         ->execute([$user['id']]);
                $user['account_status'] = 'active'; 
            }
        }

// --- END OF SUSPENSION CHECK ---
       if ($user['is_verified'] == 1 && (int)$user['two_fa_enabled'] === 1) {
    // This ONLY triggers if they are verified but haven't hit status '2' yet
    $_SESSION['2fa_pending_user_id'] = $user['id'];
    $_SESSION['2fa_secret'] = $user['google2fa_secret'];
    return "2FA_REQUIRED";
}
        // If two_fa_enabled is 2, or they are a new user (is_verified = 0), let them in
        $_SESSION['user_id'] = $user['id'];
        $this->user->resetFailedAttempts($user['id']); 

        return "Success";
    }
    return "Invalid email or password.";
}
}