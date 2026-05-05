<?php
// app/Models/User.php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $age;
    public $course;
    public $year_level;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Check if email ends with @chmsu.edu.ph
    public function isValidEmail($email) {
        return str_ends_with($email, "@chmsu.edu.ph");
    }

    // Check if email already exists
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    // Create initial user record (Step 1 of Signup)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET full_name = :full_name, 
                      email = :email, 
                      password = :password, 
                      age = :age, 
                      course = :course, 
                      year_level = :year_level";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        // Hashing password for security
        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':course', $this->course);
        $stmt->bindParam(':year_level', $this->year_level);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update Goal (Step 2 of Signup)
    public function updateGoal($userId, $goalId) {
        $query = "UPDATE " . $this->table_name . " SET current_goal_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$goalId, $userId]);
    }

    public function getGoals() {
        $query = "SELECT * FROM goals";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get interests based on a specific goal
    public function getInterestsByGoal($goalId) {
        $query = "SELECT * FROM interests WHERE goal_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$goalId]);
        return $stmt->fetchAll();
    }

    // Save the 5 chosen interests
    public function saveInterests($userId, $interestIds) {
        // Clear existing interests first (as per your reset logic)
        $queryDelete = "DELETE FROM user_interests WHERE user_id = ?";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->execute([$userId]);

        // Insert new ones
        $queryInsert = "INSERT INTO user_interests (user_id, interest_id) VALUES (?, ?)";
        $stmtInsert = $this->conn->prepare($queryInsert);
        
        foreach ($interestIds as $interestId) {
            $stmtInsert->execute([$userId, $interestId]);
        }
        return true;
    }
    
    // Complete onboarding
    public function completeOnboarding($userId) {
        $query = "UPDATE users SET onboarding_completed = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId]);
    }

    public function getInterestsByUserId($userId) {
    $query = "SELECT i.interest_name, i.id as interest_id 
              FROM user_interests ui
              JOIN interests i ON ui.interest_id = i.id
              WHERE ui.user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

    public function getUserDetails($userId) {
    $query = "SELECT u.*, g.goal_name 
              FROM users u 
              LEFT JOIN goals g ON u.current_goal_id = g.id 
              WHERE u.id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

    public function getFilteredStudents($currentUserId, $filters) {
    $sql = "SELECT u.*, g.goal_name 
            FROM users u 
            JOIN goals g ON u.current_goal_id = g.id 
            WHERE u.id != ? AND u.account_status != 'suspended'";
    $params = [$currentUserId];

    if (!empty($filters['course'])) {
        $sql .= " AND u.course LIKE ?";
        $params[] = "%" . $filters['course'] . "%";
    }
    if (!empty($filters['age'])) {
        $sql .= " AND u.age = ?";
        $params[] = $filters['age'];
    }
    if (!empty($filters['year_level'])) {
        $sql .= " AND u.year_level = ?";
        $params[] = $filters['year_level'];
    }
    
    // Interest filtering logic
    if (!empty($filters['interest_ids'])) {
        $placeholders = implode(',', array_fill(0, count($filters['interest_ids']), '?'));
        $sql .= " AND u.id IN (SELECT user_id FROM user_interests WHERE interest_id IN ($placeholders))";
        foreach($filters['interest_ids'] as $id) $params[] = $id;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();

    
}

    //2FA-Related Methods
public function verifyUser($userId) {
    // Only update is_verified here. 
    // We handle the two_fa_enabled logic in index.php to avoid conflicts.
    $query = "UPDATE users SET is_verified = 1 WHERE id = ?";
    $stmt = $this->conn->prepare($query); // Use $this->conn as defined in your constructor
    return $stmt->execute([$userId]);
}

// 2. Track failed attempts for your "Bank-Style" trigger
public function incrementFailedAttempts($email) {
    $query = "UPDATE " . $this->table_name . " SET failed_login_attempts = failed_login_attempts + 1 WHERE email = ?";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$email]);
}

// 3. Reset attempts after a successful 2FA or Password login
public function resetFailedAttempts($userId) {
    $query = "UPDATE " . $this->table_name . " SET failed_login_attempts = 0 WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$userId]);
}
}