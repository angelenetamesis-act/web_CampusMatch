<?php
// app/Controllers/PostController.php

class PostController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getActivePosts() {
        // Only fetch posts created in the last 3 hours
        $query = "SELECT p.*, u.course, u.year_level,
                  (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'up') as ups,
                  (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'down') as downs
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.created_at >= NOW() - INTERVAL 3 HOUR
                  ORDER BY p.created_at DESC";
        return $this->db->query($query)->fetchAll();
    }

    public function createPost($userId, $content) {
        // Moderation Check first
        if (ModerationHelper::checkMessage($content)) {
            // Reusing the same 3-day block logic from ChatController
            $suspensionEnd = date('Y-m-d H:i:s', strtotime('+3 days'));
            $this->db->prepare("UPDATE users SET account_status = 'suspended', suspension_end_datetime = ? WHERE id = ?")
                     ->execute([$suspensionEnd, $userId]);
            return "BLOCKED";
        }

        $stmt = $this->db->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        return $stmt->execute([$userId, htmlspecialchars($content)]);
    }

    public function toggleReaction($postId, $userId, $type) {
        // Use UPSERT logic: If reaction exists, update it (switch). If not, insert it.
        $query = "INSERT INTO post_reactions (post_id, user_id, reaction_type) 
                  VALUES (:pid, :uid, :type)
                  ON DUPLICATE KEY UPDATE reaction_type = :type";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['pid' => $postId, 'uid' => $userId, 'type' => $type]);
    }

    public function deletePost($postId, $userId) {
    $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    return $stmt->execute([$postId, $userId]);
}

public function updatePost($postId, $userId, $newContent) {
    // Re-run moderation check on the new content
    if (ModerationHelper::checkMessage($newContent)) {
        return "BLOCKED";
    }
    $stmt = $this->db->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([htmlspecialchars($newContent), $postId, $userId]);
}
}