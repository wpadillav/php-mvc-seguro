<?php
/**
 * Clase DashboardController
 * 
 * Controlador encargado de mostrar la vista principal del sistema tras el inicio de sesión.
 * Solo permite el acceso a usuarios autenticados.
 */
class DashboardController {
    /**
     * Método por defecto (acción index).
     * 
     * Verifica si el usuario ha iniciado sesión. Si no es así, redirige al formulario de login.
     * Si la sesión es válida, incluye la vista correspondiente al dashboard.
     */
    public function index() {
        if (!isset($_SESSION['user'])) {
            // Redirección a login si no hay usuario en sesión
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        // Carga la vista del dashboard
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
