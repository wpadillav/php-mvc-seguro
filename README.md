## 🔐 PHP MVC Seguro

Aplicación web modular construida con PHP 8.3, basada en el patrón **Modelo-Vista-Controlador (MVC)**, enfocada en **seguridad web moderna**: cifrado con `libsodium`, protección CSRF, sesiones reforzadas, políticas de cookies seguras, rate limiting y más.

> 🌐 Proyecto en GitHub: [github.com/wpadillav/php-mvc-seguro](https://github.com/wpadillav/php-mvc-seguro)

---

### ⚙️ Tecnologías utilizadas

* **PHP 8.3** (`php8.3-fpm`, `php8.3-mysql`, `libapache2-mod-php8.3`)
* **MariaDB 10.11**
* **Apache 2.4 con HTTPS (SSL/TLS)**
* **mod\_security** activado
* **libsodium** (`libsodium-dev`) para cifrado moderno
* **Composer** para gestión de dependencias
* **Bootstrap 5** para la interfaz

---

### 📁 Estructura del proyecto

```
.
├── assets/           # Estáticos (CSS, JS, imágenes)
├── config/           # Archivos de configuración (.env, BD, seguridad)
├── controllers/      # Controladores MVC
├── models/           # Lógica de datos, cifrado y usuarios
├── views/            # Vistas del sistema
├── components/       # Fragmentos reutilizables (navbar, etc.)
├── index.php         # Punto de entrada (Front Controller)
├── .env              # Variables de entorno
├── .env.example      # Plantilla base para `.env`
├── composer.json     # Dependencias del proyecto
└── vendor/           # Librerías Composer (ignorado por Git)
```

---

### 🛠 Requisitos

* Ubuntu 24.04 LTS
* Apache 2.4 con SSL y `mod_rewrite`, `mod_security`
* PHP 8.3 (`fpm`, `mysql`, `libsodium`)
* MariaDB
* Composer

---

### 🚀 Instalación paso a paso

1. **Clona el repositorio:**

```bash
git clone https://github.com/wpadillav/php-mvc-seguro.git
cd php-mvc-seguro
```

2. **Instala dependencias:**

```bash
composer install
```

3. **Copia el archivo `.env` de ejemplo y edítalo:**

```bash
cp .env.example .env
nano .env
```

Rellena con tus datos de conexión y una clave segura:

```env
APP_SECRET_KEY=tu_clave_hexadecimal_segura
```

Genera una clave con:

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

4. **Crea la base de datos y tabla de usuarios:**

```sql
CREATE DATABASE app_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE app_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  salt VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 🔐 Seguridad aplicada

* ✅ HTTPS forzado (`security.php` + Apache redirect)
* ✅ `libsodium` para cifrado seguro simétrico
* ✅ Protección CSRF en formularios
* ✅ Rate limiting por sesión en herramientas
* ✅ Regeneración de ID de sesión
* ✅ Cookies con flags `Secure`, `HttpOnly`, `SameSite=Strict`
* ✅ Escapado de salida (`htmlspecialchars`)
* ✅ mod\_security activado (Apache)

---

### 🖥️ Apache VirtualHost (resumen)

**Redirección HTTP a HTTPS:**

```apache
<VirtualHost *:80>
    ServerName localhost
    Redirect permanent / https://localhost/
</VirtualHost>
```

**VirtualHost HTTPS (default-ssl.conf):**

```apache
<VirtualHost _default_:443>
    ServerAdmin webmaster@localhost
    ServerName localhost
    DocumentRoot /var/www/html

    SSLEngine on
    SSLCertificateFile    /etc/ssl/certs/apache-selfsigned.crt
    SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key
</VirtualHost>
```

---

### 💡 Uso y desarrollo

Puedes iniciar un servidor local (sin Apache) para pruebas rápidas:

```bash
php -S localhost:8000
```

---

### 👤 Autor

* **Wilmer Padilla**
* GitHub: [@wpadillav](https://github.com/wpadillav)
* Contacto: [willipadilla@proton.me](mailto:willipadilla@proton.me)

---

### ⚖️ Licencia

MIT License - libre para usar, modificar y distribuir.
