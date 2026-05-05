<?php
// app/Controllers/NotificationController.php

class NotificationController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

   // Change checkUnread to fetch ALL notifications for the user
public function checkUnread($userId) {
    $query = "SELECT n.*, u.full_name as actor_name 
              FROM notifications n
              JOIN users u ON n.actor_id = u.id
              WHERE n.user_id = :uid 
              ORDER BY n.created_at DESC 
              LIMIT 20"; // Limit to top 20 to keep it clean
    $stmt = $this->db->prepare($query);
    $stmt->execute(['uid' => $userId]);
    return $stmt->fetchAll();
}

public function deleteNotification($notifId, $userId) {
    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$notifId, $userId]);
}

    public function markAsRead($userId) {
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId]);
    }

    // app/Controllers/NotificationController.php
public function getUnreadCount($userId) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}
}