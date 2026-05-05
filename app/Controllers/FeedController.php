<?php
// app/Controllers/FeedController.php

require_once __DIR__ . '/../Models/User.php';

class FeedController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    // REVERTED: University Feed (Restored to your original code)
    public function getUniversityFeed($currentUserId) {
        $query = "SELECT u.*, g.goal_name 
                  FROM users u
                  LEFT JOIN goals g ON u.current_goal_id = g.id
                  WHERE u.id != :current_user_id 
                  AND u.id NOT IN (SELECT receiver_id FROM waves WHERE sender_id = :current_user_id)
                  AND u.id NOT IN (SELECT user_one_id FROM matches WHERE user_two_id = :current_user_id)
                  AND u.id NOT IN (SELECT user_two_id FROM matches WHERE user_one_id = :current_user_id)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':current_user_id', $currentUserId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // MODIFIED: My Feed (Using the Funnel logic for specific filtering)
    public function getMyFeed($currentUserId, $goalId, $filters = []) {
        $sql = "SELECT DISTINCT u.*, g.goal_name 
                FROM users u
                LEFT JOIN goals g ON u.current_goal_id = g.id
                LEFT JOIN user_interests ui ON u.id = ui.user_id
                WHERE u.current_goal_id = :goal_id 
                AND u.id != :current_user_id
                AND u.id NOT IN (SELECT receiver_id FROM waves WHERE sender_id = :current_user_id)
                AND u.id NOT IN (SELECT user_one_id FROM matches WHERE user_two_id = :current_user_id)
                AND u.id NOT IN (SELECT user_two_id FROM matches WHERE user_one_id = :current_user_id)";

        // Apply Dynamic Filtering (Age, Year, Course) - Funneling with AND
        if (!empty($filters['course'])) {
            $sql .= " AND u.course = :course";
        }
        if (!empty($filters['year_level'])) {
            $sql .= " AND u.year_level = :year_level";
        }
        if (!empty($filters['age'])) {
            $sql .= " AND u.age = :age";
        }

        // Apply Interest Filtering - Funneling with AND
        if (!empty($filters['interest_ids'])) {
            $ids = implode(',', array_map('intval', $filters['interest_ids']));
            $sql .= " AND ui.interest_id IN ($ids)";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':goal_id', $goalId);
        $stmt->bindParam(':current_user_id', $currentUserId);
        
        if (!empty($filters['course'])) $stmt->bindParam(':course', $filters['course']);
        if (!empty($filters['year_level'])) $stmt->bindParam(':year_level', $filters['year_level']);
        if (!empty($filters['age'])) $stmt->bindParam(':age', $filters['age']);

        $stmt->execute();
        return $stmt->fetchAll();
    }
}