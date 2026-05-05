<?php
// app/Controllers/MatchController.php

class MatchController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Action: Wave at a student (Merged logic)
    public function sendWave($senderId, $receiverId) {
        // 1. Check if the other person already waved at us (Check for Mutual Match)
        $query = "SELECT * FROM waves WHERE sender_id = :receiver_id AND receiver_id = :sender_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['receiver_id' => $receiverId, 'sender_id' => $senderId]);
        
        if ($stmt->rowCount() > 0) {
            // MUTUAL MATCH DETECTED!
            $matchResult = $this->createMatch($senderId, $receiverId);
            
            if ($matchResult === "Matched") {
                // Add notification for the receiver about the match
                $this->addNotification($receiverId, $senderId, "Matched with you!");
            }
            
            return "Matched";
        } else {
            // Single Wave: Insert into waves table
            $query = "INSERT IGNORE INTO waves (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['sender_id' => $senderId, 'receiver_id' => $receiverId]);
            
            // Add notification for the receiver about the wave
            $this->addNotification($receiverId, $senderId, "sent you a Hand Wave!");
            
            return "Waved";
        }
    }

    // Action: Unwave (Remove wave record)
    public function unwave($senderId, $receiverId) {
        $query = "DELETE FROM waves WHERE sender_id = :sender_id AND receiver_id = :receiver_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['sender_id' => $senderId, 'receiver_id' => $receiverId]);
    }

   // Internal Logic: Create Match and Clean Up Waves
    private function createMatch($userA, $userB) {
        try {
            $this->db->beginTransaction();

            // --- ADDED: START SNAPSHOT LOGIC ---
            // Find the intersection of interests between userA and userB
            $sqlInterest = "SELECT i.interest_name 
                            FROM user_interests ui1
                            JOIN user_interests ui2 ON ui1.interest_id = ui2.interest_id
                            JOIN interests i ON ui1.interest_id = i.id
                            WHERE ui1.user_id = :ua AND ui2.user_id = :ub";
            $stmtInt = $this->db->prepare($sqlInterest);
            $stmtInt->execute(['ua' => $userA, 'ub' => $userB]);
            $shared = $stmtInt->fetchAll(PDO::FETCH_COLUMN);
            
            // Format the interests as a string or a fallback if none found
            $substanceText = !empty($shared) ? implode(', ', $shared) : 'Universal Connection';
            // --- ADDED: END SNAPSHOT LOGIC ---

            // MODIFIED: Added match_substance column and :substance parameter
            $query = "INSERT INTO matches (user_one_id, user_two_id, matched_at, last_message_at, streak_count, match_substance) 
                      VALUES (:ua, :ub, NOW(), NOW(), 1, :substance)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'ua' => $userA, 
                'ub' => $userB,
                'substance' => $substanceText // Captured snapshot
            ]);

            $query = "DELETE FROM waves WHERE (sender_id = :ua AND receiver_id = :ub) OR (sender_id = :ub AND receiver_id = :ua)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['ua' => $userA, 'ub' => $userB]);

            $this->db->commit();
            return "Matched";
        } catch (Exception $e) {
            $this->db->rollBack();
            return "Error";
        }
    }

    // Get all matches for the Messages Page
    public function getMyMatches($userId) {
        $query = "SELECT m.*, u.full_name, u.course, u.year_level, g.goal_name
                  FROM matches m
                  JOIN users u ON (u.id = m.user_one_id OR u.id = m.user_two_id)
                  JOIN goals g ON u.current_goal_id = g.id
                  WHERE (m.user_one_id = :uid OR m.user_two_id = :uid)
                  AND u.id != :uid";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    // Notification Helper
    private function addNotification($userId, $actorId, $message) {
        $query = "INSERT INTO notifications (user_id, actor_id, message) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $actorId, $message]);
    }
}