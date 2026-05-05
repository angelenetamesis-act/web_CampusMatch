<?php
// app/Controllers/ChatController.php

require_once __DIR__ . '/../../src/Helpers/ModerationHelper.php';

class ChatController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Fetch all messages for a specific match
     */
    public function getChatHistory($matchId) {
        $query = "SELECT cm.*, u.full_name as sender_name 
                  FROM chat_messages cm
                  JOIN users u ON cm.sender_id = u.id
                  WHERE cm.match_id = :match_id
                  ORDER BY cm.created_at ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll();
    }

 public function sendMessage($matchId, $messageText) {
    // 1. ENSURE SENDER ID IS ALWAYS CAPTURED
    $senderId = $_SESSION['user_id'] ?? null;

    if (!$senderId) {
        return "ERROR_AUTH"; 
    }

    // 2. DETECT ARGUMENT SHIFT
    $args = func_get_args();
    if (count($args) === 3) {
        $actualMessage = $args[2]; // The 3rd item is the real text
    } else {
        $actualMessage = $messageText;
    }

    // 3. MODERATION CHECK (on the actual text)
 if (ModerationHelper::checkMessage($actualMessage)) {
    // 1. Fetch current count to determine the warning type
    $stmt = $this->db->prepare("SELECT violation_count FROM users WHERE id = ?");
    $stmt->execute([$senderId]);
    $user = $stmt->fetch();
    $currentStrikes = $user['violation_count'] ?? 0;

    // 2. Increment the count in the DB immediately (The Fix)
    $this->db->prepare("UPDATE users SET violation_count = violation_count + 1 WHERE id = ?")->execute([$senderId]);

    if ($currentStrikes == 0) {
        return "WARNING_STRIKE_ONE"; 
    } else {
        // User already had at least 1 strike, apply the 3-day block
        $this->applyThreeDayBlock($senderId);
        return "BLOCKED";
    }
}

    // 4. SAVE MESSAGE
    try {
        $query = "INSERT INTO chat_messages (match_id, sender_id, message_text, created_at) 
                  VALUES (:match_id, :sender_id, :msg, NOW())";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            'match_id' => $matchId,
            'sender_id' => $senderId,
            'msg' => htmlspecialchars($actualMessage)
        ]);

        if ($result) {
            $this->processStreak($matchId);
            return "SENT";
        }
    } catch (Exception $e) {
        return "ERROR_DB";
    }

    return "ERROR";
}
    public function reportUser($reporterId, $reportedId) {
        // 1. Log to Reports table
        $query = "INSERT INTO reports (reporter_id, reported_id, reason) 
                  VALUES (?, ?, 'Manual Report via Flag Icon')";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$reporterId, $reportedId]);

        // 2. Immediate Soft Block (Delete Match)
        $queryDelete = "DELETE FROM matches WHERE (user_one_id = ? AND user_two_id = ?) 
                        OR (user_one_id = ? AND user_two_id = ?)";
        $stmtDelete = $this->db->prepare($queryDelete);
        $stmtDelete->execute([$reporterId, $reportedId, $reportedId, $reporterId]);
        
        return true;
    }

    /**
     * Internal: The Auto-Block Logic
     */
    private function applyThreeDayBlock($userId) {
        $suspensionEnd = date('Y-m-d H:i:s', strtotime('+3 days'));
        $query = "UPDATE users SET account_status = 'suspended', suspension_end_datetime = :end WHERE id = :uid";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['end' => $suspensionEnd, 'uid' => $userId]);

        session_destroy();
    }

/**
     * Internal: 24-Hour Streak Logic
     * Fixed: Handles the 0 to 1 transition and Daily Login logic
     */
    private function processStreak($matchId) {
        $stmt = $this->db->prepare("SELECT last_message_at, streak_count FROM matches WHERE id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch();

        if (!$match) return;

        $lastMessage = new DateTime($match['last_message_at']);
        $now = new DateTime();
        $diff = $now->diff($lastMessage);

        // 1. CHAT STREAK LOGIC (matches table)
        // If it's a new match (streak is 0), start it at 1 immediately
        if ($match['streak_count'] == 0) {
            $this->db->prepare("UPDATE matches SET streak_count = 1, last_message_at = NOW() WHERE id = ?")
                     ->execute([$matchId]);
        } 
        // If it's been exactly 1 day (24-48 hours): Increment
        elseif ($diff->days == 1) {
            $this->db->prepare("UPDATE matches SET streak_count = streak_count + 1, last_message_at = NOW() WHERE id = ?")
                     ->execute([$matchId]);
        } 
        // If it's been more than a day (48+ hours): Reset to 1
        elseif ($diff->days > 1) {
            $this->db->prepare("UPDATE matches SET streak_count = 1, last_message_at = NOW() WHERE id = ?")
                     ->execute([$matchId]);
        } 
        // Still the same day: Just refresh the timestamp
        else {
            $this->db->prepare("UPDATE matches SET last_message_at = NOW() WHERE id = ?")
                     ->execute([$matchId]);
        }

        // 2. DAILY LOGIN STREAK (users table)
        // Checks if the user has already messaged today to avoid double-counting
        $senderId = $_SESSION['user_id'];
        $this->db->prepare("UPDATE users SET total_streaks = total_streaks + 1 WHERE id = ? AND DATE(created_at) != CURDATE()")
                 ->execute([$senderId]);
    }


    // Count unread messages for a specific match for the logged-in user
public function getUnreadCount($matchId, $userId) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM chat_messages 
                                WHERE match_id = ? AND sender_id != ? AND is_read = 0");
    $stmt->execute([$matchId, $userId]);
    return $stmt->fetchColumn();
}

// Mark messages as read when entering the chat
public function markAsRead($matchId, $userId) {
    $stmt = $this->db->prepare("UPDATE chat_messages SET is_read = 1 
                                WHERE match_id = ? AND sender_id != ?");
    $stmt->execute([$matchId, $userId]);
}

// Get the total unread messages across ALL matches for a user
public function getTotalUnreadCount($userId) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM chat_messages 
                                WHERE sender_id != ? AND is_read = 0 
                                AND match_id IN (SELECT id FROM matches WHERE user_one_id = ? OR user_two_id = ?)");
    $stmt->execute([$userId, $userId, $userId]);
    return $stmt->fetchColumn();
}
}
 