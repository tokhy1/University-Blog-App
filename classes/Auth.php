<?php
class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($name, $email, $password)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'This email is already registered.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

        return ['success' => true, 'user_id' => $this->db->lastInsertId()];
    }

    public function login($email, $password, $remember)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_image'] = $user['profile_image'];

        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        return ['success' => true, 'role' => $user['role']];
    }

    private function setRememberToken($userId)
    {
        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        $hash = hash('sha256', $validator);
        $expires = date('Y-m-d H:i:s', time() + 30 * 24 * 3600);

        $this->db->prepare("DELETE FROM auth_tokens WHERE user_id = ?")->execute([$userId]);
        $this->db->prepare(
            "INSERT INTO auth_tokens (user_id, selector, token_hash, expires_at) VALUES (?, ?, ?, ?)"
        )->execute([$userId, $selector, $hash, $expires]);

        setcookie('remember', "$selector:$validator", [
            'expires' => time() + 30 * 24 * 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function checkRememberMe()
    {
        if (!isset($_COOKIE['remember'])) return false;

        [$selector, $validator] = explode(':', $_COOKIE['remember'], 2);
        $hash = hash('sha256', $validator);

        $stmt = $this->db->prepare(
            "SELECT t.user_id, u.name, u.role, u.profile_image
             FROM auth_tokens t
             JOIN users u ON t.user_id = u.id
             WHERE t.selector = ? AND t.token_hash = ? AND t.expires_at > NOW()"
        );
        $stmt->execute([$selector, $hash]);
        $row = $stmt->fetch();

        if (!$row) return false;

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_role'] = $row['role'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_image'] = $row['profile_image'];
        return true;
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            $this->db->prepare("DELETE FROM auth_tokens WHERE user_id = ?")
                ->execute([$_SESSION['user_id']]);
        }

        setcookie('remember', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true]);
        session_destroy();
        $_SESSION = [];
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header("Location: " . BASE_URL . "/login.php");
            exit;
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("Location: " . BASE_URL . "/index.php");
            exit;
        }
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
