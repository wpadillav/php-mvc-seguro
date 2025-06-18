<?php

class UserAdminController {
    private $userModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        $this->userModel = new UserModel();
    }

    public function index() {
        $stmt = Database::getInstance()->getConnection()->query("SELECT id, username, created_at FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/useradmin/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username && $password) {
                if ($this->userModel->getUserByUsername($username)) {
                    $error = "❌ El usuario ya existe.";
                } elseif ($this->userModel->createUser($username, $password)) {
                    header('Location: /?controller=UserAdmin&action=index');
                    exit;
                } else {
                    $error = "❌ Error al crear el usuario.";
                }
            } else {
                $error = "❌ Debes completar todos los campos.";
            }
        }

        require __DIR__ . '/../views/useradmin/create.php';
    }
}
