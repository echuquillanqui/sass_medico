-- ============================================================
--  Suite Web SaaS Médico Modular
--  Script de creación de la base de datos
--  Motor: MySQL 5.7+ / MariaDB 10.4+  (XAMPP / Laragon)
-- ============================================================
--
--  Este script SOLO crea la base de datos vacía.
--  El esquema (tablas) y los datos demo se generan con:
--
--      php artisan migrate --seed
--
--  Ejecuta este archivo en phpMyAdmin (pestaña "Importar")
--  o desde consola:  mysql -u root < suite_saas_medico_modular.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `suite_saas_medico_modular`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `suite_saas_medico_modular`;

-- Listo. Ahora ejecuta desde la carpeta del proyecto:
--   php artisan migrate --seed
