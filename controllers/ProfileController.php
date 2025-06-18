<?php
/**
 * Clase ProfileController
 *
 * Encargada de gestionar el acceso a la sección de perfil del usuario.
 * Aplica control de acceso básico basado en la sesión iniciada.
 */
class ProfileController {
    /**
     * Acción por defecto: muestra la vista del perfil del usuario autenticado.
     * Si el usuario no está autenticado, lo redirige al login.
     */
    public function index() {
        if (!isset($_SESSION['user'])) {
            // Bloquea el acceso no autenticado y redirige al formulario de login
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        // Incluye la vista del perfil
        require_once __DIR__ . '/../views/profile/index.php';
    }
}
