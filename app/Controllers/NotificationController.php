<?php
// app/Controllers/NotificationController.php

class NotificationController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // MODIFIED: Fetches only ACTIVE notifications for the user's primary activity panel
    public function checkUnread($userId) {
        $query = "SELECT n.*, u.full_name as actor_name 
                  FROM notifications n
                  JOIN users u ON n.actor_id = u.id
                  WHERE n.user_id = :uid AND n.status = 'active'
                  ORDER BY n.created_at DESC 
                  LIMIT 20"; // Limit to top 20 to keep it clean
        $stmt = $this->db->prepare($query);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    // UNCHANGED: Hard permanent deletion when hitting the Trash Can/Delete button
    public function deleteNotification($notifId, $userId) {
        $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$notifId, $userId]);
    }

    // UNCHANGED: Standard read verification handshake
    public function markAsRead($userId) {
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND status = 'active'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId]);
    }

    // MODIFIED: Counts only ACTIVE unread alerts for your dynamic counter badge
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0 AND status = 'active'");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    // NEW METHOD: Moves the active row context to 'accepted' or 'declined' category archive states
    public function updateStatus($notifId, $userId, $status) {
        $query = "UPDATE notifications SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $notifId, $userId]);
    }
}