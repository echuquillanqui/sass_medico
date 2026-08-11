-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para suite_saas_medico_modular
CREATE DATABASE IF NOT EXISTS `suite_saas_medico_modular` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `suite_saas_medico_modular`;

-- Volcando estructura para tabla suite_saas_medico_modular.adjuntos
CREATE TABLE IF NOT EXISTS `adjuntos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano` bigint unsigned NOT NULL DEFAULT '0',
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adjuntos_paciente_id_foreign` (`paciente_id`),
  KEY `adjuntos_consulta_id_foreign` (`consulta_id`),
  KEY `adjuntos_user_id_foreign` (`user_id`),
  KEY `adjuntos_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `adjuntos_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `adjuntos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `adjuntos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `adjuntos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.adjuntos: ~0 rows (aproximadamente)
DELETE FROM `adjuntos`;

-- Volcando estructura para tabla suite_saas_medico_modular.auditorias
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo_id` bigint unsigned DEFAULT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_user_id_foreign` (`user_id`),
  KEY `auditorias_empresa_id_created_at_index` (`empresa_id`,`created_at`),
  CONSTRAINT `auditorias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `auditorias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.auditorias: ~0 rows (aproximadamente)
DELETE FROM `auditorias`;

-- Volcando estructura para tabla suite_saas_medico_modular.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla suite_saas_medico_modular.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla suite_saas_medico_modular.camas
CREATE TABLE IF NOT EXISTS `camas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `camas_empresa_id_index` (`empresa_id`),
  CONSTRAINT `camas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.camas: ~5 rows (aproximadamente)
DELETE FROM `camas`;
INSERT INTO `camas` (`id`, `empresa_id`, `nombre`, `area`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Cama 101', 'Hospitalizacion', 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, 'Cama 102', 'Hospitalizacion', 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(3, 1, 'Cama 103', 'Hospitalizacion', 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(4, 1, 'Cama UCI-1', 'UCI', 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(5, 1, 'Cama Ped-1', 'Pediatria', 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `duracion` int NOT NULL DEFAULT '30',
  `estado` enum('pendiente','confirmada','atendida','cancelada','no_asistio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `es_teleconsulta` tinyint(1) NOT NULL DEFAULT '0',
  `sala_video` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_sala` enum('sin_llegar','esperando','en_atencion','atendido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sin_llegar',
  `hora_llegada` timestamp NULL DEFAULT NULL,
  `hora_atencion` timestamp NULL DEFAULT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_paciente_id_foreign` (`paciente_id`),
  KEY `citas_medico_id_foreign` (`medico_id`),
  KEY `citas_especialidad_id_foreign` (`especialidad_id`),
  KEY `citas_empresa_id_fecha_index` (`empresa_id`,`fecha`),
  CONSTRAINT `citas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.citas: ~90 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id`, `empresa_id`, `paciente_id`, `medico_id`, `especialidad_id`, `fecha`, `hora`, `duracion`, `estado`, `es_teleconsulta`, `sala_video`, `estado_sala`, `hora_llegada`, `hora_atencion`, `motivo`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 3, 2, '2026-07-15', '09:00:00', 30, 'pendiente', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(2, 1, 3, 3, 5, '2026-07-16', '10:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 4, 3, 2, '2026-07-17', '11:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(4, 1, 2, 3, 3, '2026-07-18', '12:00:00', 30, 'pendiente', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(87, 1, 1, 3, 2, '2026-07-16', '09:00:00', 30, 'pendiente', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(88, 1, 2, 3, 3, '2026-07-17', '10:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(89, 1, 3, 3, 5, '2026-07-18', '11:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(90, 1, 4, 3, 2, '2026-07-19', '12:00:00', 30, 'pendiente', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta de control', NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(170, 1, 154, 3, 23, '2026-02-17', '10:00:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(171, 1, 158, 3, 24, '2026-02-23', '12:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(172, 1, 141, 3, 18, '2026-02-25', '10:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(173, 1, 127, 3, 14, '2026-02-05', '08:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(174, 1, 114, 3, 9, '2026-02-03', '14:00:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(175, 1, 93, 3, 3, '2026-02-13', '14:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(176, 1, 115, 3, 10, '2026-02-04', '09:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(177, 1, 88, 3, 2, '2026-02-11', '12:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(178, 1, 147, 3, 20, '2026-02-22', '09:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(179, 1, 129, 3, 14, '2026-03-17', '11:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(180, 1, 113, 3, 9, '2026-03-27', '15:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(181, 1, 117, 3, 10, '2026-03-01', '11:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(182, 1, 125, 3, 13, '2026-03-19', '15:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(183, 1, 138, 3, 17, '2026-03-02', '10:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(184, 1, 144, 3, 19, '2026-03-30', '14:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(185, 1, 150, 3, 21, '2026-03-17', '14:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(186, 1, 156, 3, 23, '2026-03-08', '08:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(187, 1, 95, 3, 3, '2026-03-20', '15:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(188, 1, 145, 3, 20, '2026-03-30', '18:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(189, 1, 133, 3, 16, '2026-03-18', '08:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(190, 1, 160, 3, 25, '2026-03-28', '18:00:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(191, 1, 115, 3, 10, '2026-03-29', '18:00:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(192, 1, 116, 3, 10, '2026-03-28', '16:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(193, 1, 1, 3, 2, '2026-04-20', '09:30:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(194, 1, 161, 3, 25, '2026-04-14', '08:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(195, 1, 140, 3, 18, '2026-04-26', '15:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(196, 1, 108, 3, 7, '2026-04-22', '17:00:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(197, 1, 137, 3, 17, '2026-04-23', '13:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(198, 1, 146, 3, 20, '2026-04-05', '13:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(199, 1, 87, 3, 2, '2026-04-15', '08:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(200, 1, 123, 3, 12, '2026-05-12', '14:00:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(201, 1, 128, 3, 14, '2026-05-23', '15:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(202, 1, 142, 3, 19, '2026-05-30', '11:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(203, 1, 132, 3, 15, '2026-05-10', '10:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(204, 1, 97, 3, 4, '2026-05-25', '13:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(205, 1, 153, 3, 22, '2026-05-28', '15:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(206, 1, 128, 3, 14, '2026-05-27', '15:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(207, 1, 127, 3, 14, '2026-05-21', '12:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(208, 1, 91, 3, 2, '2026-05-02', '10:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(209, 1, 100, 3, 5, '2026-05-25', '14:30:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(210, 1, 149, 3, 21, '2026-05-16', '08:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(211, 1, 161, 3, 25, '2026-05-18', '17:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(212, 1, 156, 3, 23, '2026-05-29', '11:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(213, 1, 148, 3, 21, '2026-05-30', '14:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(214, 1, 131, 3, 15, '2026-05-28', '12:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(215, 1, 162, 3, 25, '2026-05-06', '14:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(216, 1, 91, 3, 2, '2026-05-05', '13:00:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(217, 1, 158, 3, 24, '2026-05-13', '17:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(218, 1, 162, 3, 25, '2026-06-23', '15:30:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(219, 1, 102, 3, 5, '2026-06-14', '12:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(220, 1, 102, 3, 5, '2026-06-14', '12:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(221, 1, 97, 3, 4, '2026-06-04', '12:00:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(222, 1, 125, 3, 13, '2026-06-12', '11:30:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(223, 1, 95, 3, 3, '2026-06-05', '08:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(224, 1, 94, 3, 3, '2026-06-25', '08:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(225, 1, 109, 3, 8, '2026-06-23', '16:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(226, 1, 153, 3, 22, '2026-06-04', '13:00:00', 30, 'cancelada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(227, 1, 156, 3, 23, '2026-06-10', '08:30:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(228, 1, 136, 3, 17, '2026-06-12', '12:00:00', 30, 'atendida', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(229, 1, 126, 3, 13, '2026-06-20', '11:30:00', 30, 'no_asistio', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(230, 1, 105, 3, 6, '2026-07-03', '11:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(231, 1, 148, 3, 21, '2026-07-05', '16:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(232, 1, 84, 3, 1, '2026-07-02', '14:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(233, 1, 87, 3, 2, '2026-07-14', '10:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(234, 1, 139, 3, 18, '2026-07-16', '10:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(235, 1, 127, 3, 14, '2026-07-13', '08:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(236, 1, 134, 3, 16, '2026-07-02', '12:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(237, 1, 134, 3, 16, '2026-07-15', '10:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(238, 1, 124, 3, 13, '2026-07-13', '08:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(239, 1, 128, 3, 14, '2026-07-08', '14:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(240, 1, 104, 3, 6, '2026-07-03', '14:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(241, 1, 112, 3, 9, '2026-07-12', '16:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(242, 1, 128, 3, 14, '2026-07-13', '13:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(243, 1, 147, 3, 20, '2026-07-10', '12:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(244, 1, 157, 3, 24, '2026-07-01', '09:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(245, 1, 132, 3, 15, '2026-07-07', '15:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(246, 1, 110, 3, 8, '2026-07-09', '14:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(247, 1, 158, 3, 24, '2026-07-09', '17:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(248, 1, 88, 3, 2, '2026-07-03', '16:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(249, 1, 127, 3, 14, '2026-07-13', '16:30:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(250, 1, 4, 3, 2, '2026-07-15', '18:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(251, 1, 104, 3, 6, '2026-07-04', '08:00:00', 30, 'confirmada', 0, NULL, 'sin_llegar', NULL, NULL, 'Consulta', '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.comprobantes
CREATE TABLE IF NOT EXISTS `comprobantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `pago_id` bigint unsigned DEFAULT NULL,
  `resumen_id` bigint unsigned DEFAULT NULL,
  `resumen_baja_id` bigint unsigned DEFAULT NULL,
  `paciente_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('boleta','factura','nota_credito') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boleta',
  `ref_comprobante_id` bigint unsigned DEFAULT NULL,
  `tipo_nota` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correlativo` int unsigned NOT NULL,
  `cliente_tipo_doc` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `cliente_num_doc` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `afectacion` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10',
  `gravado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `exonerado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inafecto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `items` json DEFAULT NULL,
  `estado` enum('pendiente','emitido','aceptado','rechazado','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `baja_via_resumen` tinyint(1) NOT NULL DEFAULT '0',
  `baja_pendiente` tinyint(1) NOT NULL DEFAULT '0',
  `sunat_ticket` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_respuesta` text COLLATE utf8mb4_unicode_ci,
  `hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comprobantes_empresa_id_serie_correlativo_unique` (`empresa_id`,`serie`,`correlativo`),
  KEY `comprobantes_pago_id_foreign` (`pago_id`),
  KEY `comprobantes_paciente_id_foreign` (`paciente_id`),
  KEY `comprobantes_empresa_id_tipo_estado_index` (`empresa_id`,`tipo`,`estado`),
  KEY `comprobantes_ref_comprobante_id_foreign` (`ref_comprobante_id`),
  KEY `comprobantes_resumen_id_foreign` (`resumen_id`),
  KEY `comprobantes_resumen_baja_id_foreign` (`resumen_baja_id`),
  CONSTRAINT `comprobantes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comprobantes_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_ref_comprobante_id_foreign` FOREIGN KEY (`ref_comprobante_id`) REFERENCES `comprobantes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_resumen_baja_id_foreign` FOREIGN KEY (`resumen_baja_id`) REFERENCES `resumenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comprobantes_resumen_id_foreign` FOREIGN KEY (`resumen_id`) REFERENCES `resumenes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.comprobantes: ~0 rows (aproximadamente)
DELETE FROM `comprobantes`;

-- Volcando estructura para tabla suite_saas_medico_modular.consultas
CREATE TABLE IF NOT EXISTS `consultas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `cita_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `tratamiento` text COLLATE utf8mb4_unicode_ci,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `presion_arterial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia_cardiaca` int DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `datos_especialidad` json DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultas_paciente_id_foreign` (`paciente_id`),
  KEY `consultas_medico_id_foreign` (`medico_id`),
  KEY `consultas_especialidad_id_foreign` (`especialidad_id`),
  KEY `consultas_cita_id_foreign` (`cita_id`),
  KEY `consultas_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `consultas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consultas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consultas_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consultas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consultas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.consultas: ~6 rows (aproximadamente)
DELETE FROM `consultas`;
INSERT INTO `consultas` (`id`, `empresa_id`, `paciente_id`, `medico_id`, `especialidad_id`, `cita_id`, `fecha`, `motivo`, `diagnostico`, `tratamiento`, `peso`, `talla`, `presion_arterial`, `frecuencia_cardiaca`, `temperatura`, `datos_especialidad`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 3, 2, NULL, '2026-07-10', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 12.00, 90.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(2, 1, 3, 3, 5, NULL, '2026-07-05', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 32.00, 120.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 4, 3, 2, NULL, '2026-06-30', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 52.00, 150.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(4, 1, 1, 3, 2, NULL, '2026-07-11', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 12.00, 90.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(5, 1, 2, 3, 3, NULL, '2026-07-06', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 32.00, 120.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(6, 1, 3, 3, 5, NULL, '2026-07-01', 'Control de rutina', 'Paciente estable, sin hallazgos patologicos.', 'Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.', 52.00, 150.00, '120/80', 78, 36.6, NULL, NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07');

-- Volcando estructura para tabla suite_saas_medico_modular.controles_prenatales
CREATE TABLE IF NOT EXISTS `controles_prenatales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `embarazo_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `semanas` decimal(4,1) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `presion_arterial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura_uterina` decimal(4,1) DEFAULT NULL,
  `fcf` smallint unsigned DEFAULT NULL,
  `presentacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movimientos_fetales` tinyint(1) NOT NULL DEFAULT '1',
  `edema` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `controles_prenatales_embarazo_id_foreign` (`embarazo_id`),
  KEY `controles_prenatales_user_id_foreign` (`user_id`),
  CONSTRAINT `controles_prenatales_embarazo_id_foreign` FOREIGN KEY (`embarazo_id`) REFERENCES `embarazos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `controles_prenatales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.controles_prenatales: ~3 rows (aproximadamente)
DELETE FROM `controles_prenatales`;
INSERT INTO `controles_prenatales` (`id`, `embarazo_id`, `user_id`, `fecha`, `semanas`, `peso`, `presion_arterial`, `altura_uterina`, `fcf`, `presentacion`, `movimientos_fetales`, `edema`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, '2026-04-08', 12.0, 62.00, '110/70', 14.0, 150, 'Cefálica', 1, 0, NULL, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, 3, '2026-06-03', 20.0, 65.00, '112/72', 21.0, 145, 'Cefálica', 1, 0, NULL, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(3, 1, 3, '2026-07-15', 26.0, 67.50, '115/75', 27.0, 142, 'Cefálica', 1, 0, NULL, '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.dermatogramas
CREATE TABLE IF NOT EXISTS `dermatogramas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `lesiones` json DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dermatogramas_paciente_id_unique` (`paciente_id`),
  KEY `dermatogramas_user_id_foreign` (`user_id`),
  KEY `dermatogramas_empresa_id_index` (`empresa_id`),
  CONSTRAINT `dermatogramas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dermatogramas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dermatogramas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.dermatogramas: ~0 rows (aproximadamente)
DELETE FROM `dermatogramas`;
INSERT INTO `dermatogramas` (`id`, `empresa_id`, `paciente_id`, `user_id`, `lesiones`, `notas`, `created_at`, `updated_at`) VALUES
	(2, 1, 124, 3, '[{"x": 42, "y": 30, "tipo": "nevo", "vista": "frente", "descripcion": "Nevo de 4 mm, bordes regulares."}, {"x": 58, "y": 48, "tipo": "macula", "vista": "frente", "descripcion": "Mácula hiperpigmentada."}, {"x": 50, "y": 40, "tipo": "papula", "vista": "espalda", "descripcion": "Pápula eritematosa pruriginosa."}]', 'Fototipo III. Se recomienda fotoprotección y control anual de nevos.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.dispensaciones
CREATE TABLE IF NOT EXISTS `dispensaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned DEFAULT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispensaciones_paciente_id_foreign` (`paciente_id`),
  KEY `dispensaciones_consulta_id_foreign` (`consulta_id`),
  KEY `dispensaciones_user_id_foreign` (`user_id`),
  KEY `dispensaciones_empresa_id_fecha_index` (`empresa_id`,`fecha`),
  CONSTRAINT `dispensaciones_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispensaciones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispensaciones_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dispensaciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.dispensaciones: ~1 rows (aproximadamente)
DELETE FROM `dispensaciones`;
INSERT INTO `dispensaciones` (`id`, `empresa_id`, `paciente_id`, `consulta_id`, `user_id`, `fecha`, `total`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, NULL, 3, '2026-07-14', 23.60, 'Entrega segun receta.', '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.dispensacion_items
CREATE TABLE IF NOT EXISTS `dispensacion_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispensacion_id` bigint unsigned NOT NULL,
  `insumo_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `indicaciones` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispensacion_items_dispensacion_id_foreign` (`dispensacion_id`),
  KEY `dispensacion_items_insumo_id_foreign` (`insumo_id`),
  CONSTRAINT `dispensacion_items_dispensacion_id_foreign` FOREIGN KEY (`dispensacion_id`) REFERENCES `dispensaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dispensacion_items_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.dispensacion_items: ~2 rows (aproximadamente)
DELETE FROM `dispensacion_items`;
INSERT INTO `dispensacion_items` (`id`, `dispensacion_id`, `insumo_id`, `nombre`, `cantidad`, `precio`, `indicaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 'Alcohol 70%', 2.00, 9.00, '1 cada 8 horas', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, 5, 'Anestesia dental', 2.00, 2.80, '1 cada 8 horas', '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.donantes
CREATE TABLE IF NOT EXISTS `donantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ultima_donacion` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donantes_empresa_id_grupo_index` (`empresa_id`,`grupo`),
  CONSTRAINT `donantes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.donantes: ~4 rows (aproximadamente)
DELETE FROM `donantes`;
INSERT INTO `donantes` (`id`, `empresa_id`, `nombres`, `apellidos`, `documento`, `grupo`, `telefono`, `fecha_ultima_donacion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Pedro', 'Gutierrez', '40100000', 'O+', NULL, NULL, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, 'Maria', 'Lopez', '40100001', 'A+', NULL, NULL, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(3, 1, 'Jose', 'Ramos', '40100002', 'O-', NULL, NULL, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(4, 1, 'Carmen', 'Diaz', '40100003', 'B+', NULL, NULL, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.embarazos
CREATE TABLE IF NOT EXISTS `embarazos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fum` date DEFAULT NULL,
  `fpp` date DEFAULT NULL,
  `gestas` tinyint unsigned DEFAULT NULL,
  `partos` tinyint unsigned DEFAULT NULL,
  `abortos` tinyint unsigned DEFAULT NULL,
  `cesareas` tinyint unsigned DEFAULT NULL,
  `grupo_sanguineo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riesgo_alto` tinyint(1) NOT NULL DEFAULT '0',
  `estado` enum('activo','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `antecedentes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `embarazos_paciente_id_foreign` (`paciente_id`),
  KEY `embarazos_user_id_foreign` (`user_id`),
  KEY `embarazos_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `embarazos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `embarazos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `embarazos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.embarazos: ~0 rows (aproximadamente)
DELETE FROM `embarazos`;
INSERT INTO `embarazos` (`id`, `empresa_id`, `paciente_id`, `user_id`, `fum`, `fpp`, `gestas`, `partos`, `abortos`, `cesareas`, `grupo_sanguineo`, `riesgo_alto`, `estado`, `antecedentes`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 3, '2026-01-14', '2026-10-21', 2, 1, 0, 0, 'O+', 0, 'activo', 'Primer embarazo sin complicaciones. Sin antecedentes patológicos.', '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.empresas
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_primario` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#7c3aed',
  `moneda` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/',
  `separador_decimal` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.',
  `separador_miles` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ',',
  `decimales` tinyint unsigned NOT NULL DEFAULT '2',
  `moneda_posicion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'antes',
  `horario_inicio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario_fin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_atencion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` enum('basico','profesional','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basico',
  `plan_id` bigint unsigned DEFAULT NULL,
  `vence_suscripcion` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_slug_unique` (`slug`),
  KEY `empresas_plan_id_foreign` (`plan_id`),
  CONSTRAINT `empresas_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.empresas: ~1 rows (aproximadamente)
DELETE FROM `empresas`;
INSERT INTO `empresas` (`id`, `nombre`, `slug`, `ruc`, `email`, `telefono`, `direccion`, `logo`, `color_primario`, `moneda`, `separador_decimal`, `separador_miles`, `decimales`, `moneda_posicion`, `horario_inicio`, `horario_fin`, `dias_atencion`, `sitio_web`, `plan`, `plan_id`, `vence_suscripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Clinica Vida Sana', 'clinica-vida', '20481234567', 'contacto@clinicavida.test', '+51 987 654 321', 'Av. Salud 123, Lima', NULL, '#7c3aed', 'S/', '.', ',', 2, 'antes', '08:00', '20:00', 'Lun a Sab', NULL, 'profesional', 2, '2026-12-16', 1, '2026-07-16 01:31:24', '2026-07-17 04:55:05');

-- Volcando estructura para tabla suite_saas_medico_modular.empresa_especialidad
CREATE TABLE IF NOT EXISTS `empresa_especialidad` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `especialidad_id` bigint unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_especialidad_empresa_id_especialidad_id_unique` (`empresa_id`,`especialidad_id`),
  KEY `empresa_especialidad_especialidad_id_foreign` (`especialidad_id`),
  CONSTRAINT `empresa_especialidad_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `empresa_especialidad_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.empresa_especialidad: ~25 rows (aproximadamente)
DELETE FROM `empresa_especialidad`;
INSERT INTO `empresa_especialidad` (`id`, `empresa_id`, `especialidad_id`, `activo`, `created_at`, `updated_at`) VALUES
	(9, 1, 1, 1, '2026-07-16 01:31:24', '2026-08-09 06:43:32'),
	(33, 1, 5, 1, '2026-07-19 22:38:47', '2026-08-09 06:43:32'),
	(43, 1, 3, 1, '2026-08-02 13:50:45', '2026-08-09 06:43:32'),
	(44, 1, 4, 1, '2026-08-02 13:50:45', '2026-08-09 06:43:32'),
	(45, 1, 6, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(46, 1, 13, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(47, 1, 9, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(48, 1, 22, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(49, 1, 8, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(50, 1, 24, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(51, 1, 25, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(52, 1, 23, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(53, 1, 19, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(54, 1, 7, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(55, 1, 10, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(56, 1, 20, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(57, 1, 14, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(58, 1, 21, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(59, 1, 15, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(60, 1, 2, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(61, 1, 11, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(62, 1, 12, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(63, 1, 17, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(64, 1, 16, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32'),
	(65, 1, 18, 1, '2026-08-09 06:43:32', '2026-08-09 06:43:32');

-- Volcando estructura para tabla suite_saas_medico_modular.encuestas
CREATE TABLE IF NOT EXISTS `encuestas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `cita_id` bigint unsigned DEFAULT NULL,
  `puntuacion` tinyint unsigned NOT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `encuestas_paciente_id_foreign` (`paciente_id`),
  KEY `encuestas_cita_id_foreign` (`cita_id`),
  KEY `encuestas_empresa_id_created_at_index` (`empresa_id`,`created_at`),
  CONSTRAINT `encuestas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `encuestas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `encuestas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.encuestas: ~1 rows (aproximadamente)
DELETE FROM `encuestas`;
INSERT INTO `encuestas` (`id`, `empresa_id`, `paciente_id`, `cita_id`, `puntuacion`, `comentario`, `created_at`, `updated_at`) VALUES
	(1, 1, 4, 3, 5, 'Excelente atencion, muy amables.', '2026-07-16 01:31:25', '2026-07-16 01:31:25');

-- Volcando estructura para tabla suite_saas_medico_modular.especialidades
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-stethoscope',
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#7c3aed',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `especialidades_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.especialidades: ~25 rows (aproximadamente)
DELETE FROM `especialidades`;
INSERT INTO `especialidades` (`id`, `nombre`, `slug`, `icono`, `color`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Medicina General', 'medicina-general', 'fa-stethoscope', '#7c3aed', 'Consulta general y atencion primaria.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(2, 'Pediatria', 'pediatria', 'fa-baby', '#ec4899', 'Control del nino sano, crecimiento y vacunas.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(3, 'Ginecologia', 'ginecologia', 'fa-venus', '#a855f7', 'Salud femenina y control ginecologico.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(4, 'Obstetricia', 'obstetricia', 'fa-baby-carriage', '#d946ef', 'Control prenatal y atencion del embarazo.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(5, 'Odontologia', 'odontologia', 'fa-tooth', '#06b6d4', 'Odontograma, tratamientos y salud bucal.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(6, 'Cardiologia', 'cardiologia', 'fa-heart-pulse', '#ef4444', 'Evaluacion cardiovascular, ECG y presion.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(7, 'Neumologia', 'neumologia', 'fa-lungs', '#0891b2', 'Aparato respiratorio y funcion pulmonar.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(8, 'Gastroenterologia', 'gastroenterologia', 'fa-disease', '#d97706', 'Sistema digestivo y endoscopia.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(9, 'Endocrinologia', 'endocrinologia', 'fa-dna', '#db2777', 'Hormonas, tiroides y diabetes.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(10, 'Neurologia', 'neurologia', 'fa-brain', '#6d28d9', 'Sistema nervioso y neurologia clinica.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(11, 'Psicologia', 'psicologia', 'fa-brain', '#8b5cf6', 'Sesiones, evaluaciones y seguimiento.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(12, 'Psiquiatria', 'psiquiatria', 'fa-head-side-virus', '#6366f1', 'Salud mental y tratamiento psiquiatrico.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(13, 'Dermatologia', 'dermatologia', 'fa-hand-dots', '#f59e0b', 'Piel, cabello y unas.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(14, 'Oftalmologia', 'oftalmologia', 'fa-eye', '#0ea5e9', 'Salud visual y evaluacion ocular.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(15, 'Otorrinolaringologia', 'otorrinolaringologia', 'fa-ear-listen', '#14b8a6', 'Oido, nariz y garganta.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(16, 'Traumatologia', 'traumatologia', 'fa-bone', '#64748b', 'Huesos, articulaciones y lesiones.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(17, 'Reumatologia', 'reumatologia', 'fa-bone', '#94a3b8', 'Enfermedades articulares y autoinmunes.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(18, 'Urologia', 'urologia', 'fa-droplet', '#2563eb', 'Vias urinarias y salud masculina.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(19, 'Nefrologia', 'nefrologia', 'fa-hand-holding-droplet', '#0d9488', 'Rinon y funcion renal.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(20, 'Nutricion', 'nutricion', 'fa-apple-whole', '#22c55e', 'Evaluacion nutricional y dietas.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(21, 'Oncologia', 'oncologia', 'fa-ribbon', '#e11d48', 'Diagnostico y tratamiento del cancer.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(22, 'Fisioterapia', 'fisioterapia', 'fa-person-walking', '#16a34a', 'Rehabilitacion y terapia fisica.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(23, 'Medicina Interna', 'medicina-interna', 'fa-user-doctor', '#4f46e5', 'Adulto: diagnostico integral.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(24, 'Geriatria', 'geriatria', 'fa-person-cane', '#a16207', 'Salud del adulto mayor.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24'),
	(25, 'Infectologia', 'infectologia', 'fa-virus-covid', '#dc2626', 'Enfermedades infecciosas.', 1, '2026-07-16 01:31:24', '2026-07-16 01:31:24');

-- Volcando estructura para tabla suite_saas_medico_modular.evaluaciones_cardio
CREATE TABLE IF NOT EXISTS `evaluaciones_cardio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `pa_sistolica` smallint unsigned DEFAULT NULL,
  `pa_diastolica` smallint unsigned DEFAULT NULL,
  `fc` smallint unsigned DEFAULT NULL,
  `colesterol_total` smallint unsigned DEFAULT NULL,
  `hdl` smallint unsigned DEFAULT NULL,
  `ldl` smallint unsigned DEFAULT NULL,
  `trigliceridos` smallint unsigned DEFAULT NULL,
  `glucosa` smallint unsigned DEFAULT NULL,
  `fumador` tinyint(1) NOT NULL DEFAULT '0',
  `diabetes` tinyint(1) NOT NULL DEFAULT '0',
  `ecg_ritmo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ecg_hallazgos` text COLLATE utf8mb4_unicode_ci,
  `riesgo_pct` decimal(5,1) DEFAULT NULL,
  `riesgo_nivel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluaciones_cardio_paciente_id_foreign` (`paciente_id`),
  KEY `evaluaciones_cardio_user_id_foreign` (`user_id`),
  KEY `evaluaciones_cardio_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `evaluaciones_cardio_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_cardio_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_cardio_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.evaluaciones_cardio: ~2 rows (aproximadamente)
DELETE FROM `evaluaciones_cardio`;
INSERT INTO `evaluaciones_cardio` (`id`, `empresa_id`, `paciente_id`, `user_id`, `fecha`, `pa_sistolica`, `pa_diastolica`, `fc`, `colesterol_total`, `hdl`, `ldl`, `trigliceridos`, `glucosa`, `fumador`, `diabetes`, `ecg_ritmo`, `ecg_hallazgos`, `riesgo_pct`, `riesgo_nivel`, `observaciones`, `created_at`, `updated_at`) VALUES
	(3, 1, 103, 3, '2026-04-17', 145, 92, 78, 240, 38, 160, 180, 110, 1, 0, 'Sinusal', 'Sin alteraciones agudas del ST.', 20.0, 'alto', 'Se indica dieta hiposódica y control en 1 mes.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(4, 1, 103, 3, '2026-06-26', 138, 88, 74, 220, 42, 140, 165, 102, 1, 0, 'Sinusal', 'Sin alteraciones agudas del ST.', 11.0, 'moderado', 'Se indica dieta hiposódica y control en 1 mes.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.evaluaciones_especialidad
CREATE TABLE IF NOT EXISTS `evaluaciones_especialidad` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `especialidad_slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `datos` json DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluaciones_especialidad_paciente_id_foreign` (`paciente_id`),
  KEY `evaluaciones_especialidad_user_id_foreign` (`user_id`),
  KEY `eval_esp_emp_slug_pac_idx` (`empresa_id`,`especialidad_slug`,`paciente_id`),
  CONSTRAINT `evaluaciones_especialidad_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_especialidad_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_especialidad_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.evaluaciones_especialidad: ~4 rows (aproximadamente)
DELETE FROM `evaluaciones_especialidad`;
INSERT INTO `evaluaciones_especialidad` (`id`, `empresa_id`, `paciente_id`, `user_id`, `especialidad_slug`, `fecha`, `datos`, `notas`, `created_at`, `updated_at`) VALUES
	(5, 1, 112, 3, 'endocrinologia', '2026-05-17', '{"tsh": 2.1, "hba1c": 8.2, "glucosa": 165, "colesterol": 220, "diagnostico": "Diabetes tipo 2"}', 'Registro de demostración.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(6, 1, 112, 3, 'endocrinologia', '2026-07-15', '{"tsh": 2, "hba1c": 7.1, "glucosa": 128, "colesterol": 190, "diagnostico": "Diabetes tipo 2 controlada"}', 'Registro de demostración.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(7, 1, 106, 3, 'neumologia', '2026-07-06', '{"fvc": 3.1, "fev1": 2.4, "sato2": 95, "disnea": "1", "fev1_fvc": 77, "diagnostico": "Asma leve persistente"}', 'Registro de demostración.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(8, 1, 139, 3, 'urologia', '2026-06-26', '{"psa": 3.8, "ipss": 12, "residuo": 40, "flujo_max": 14.5, "diagnostico": "Hiperplasia prostática benigna"}', 'Registro de demostración.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.evaluaciones_nutricion
CREATE TABLE IF NOT EXISTS `evaluaciones_nutricion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `grasa` decimal(4,1) DEFAULT NULL,
  `cintura` decimal(5,1) DEFAULT NULL,
  `cadera` decimal(5,1) DEFAULT NULL,
  `musculo` decimal(5,1) DEFAULT NULL,
  `objetivo_kcal` smallint unsigned DEFAULT NULL,
  `peso_objetivo` decimal(5,2) DEFAULT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluaciones_nutricion_paciente_id_foreign` (`paciente_id`),
  KEY `evaluaciones_nutricion_user_id_foreign` (`user_id`),
  KEY `evaluaciones_nutricion_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `evaluaciones_nutricion_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_nutricion_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_nutricion_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.evaluaciones_nutricion: ~3 rows (aproximadamente)
DELETE FROM `evaluaciones_nutricion`;
INSERT INTO `evaluaciones_nutricion` (`id`, `empresa_id`, `paciente_id`, `user_id`, `fecha`, `peso`, `talla`, `imc`, `grasa`, `cintura`, `cadera`, `musculo`, `objetivo_kcal`, `peso_objetivo`, `plan`, `observaciones`, `created_at`, `updated_at`) VALUES
	(4, 1, 145, 3, '2026-05-17', 82.00, 170.00, 28.37, 30.0, 95.0, 102.0, 34.0, 1800, 72.00, 'Déficit calórico moderado, 5 comidas, aumento de proteína y fibra.', 'Buena adherencia; continuar plan.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(5, 1, 145, 3, '2026-06-16', 79.50, 170.00, 27.51, 28.0, 92.0, 101.0, 34.0, 1800, 72.00, 'Déficit calórico moderado, 5 comidas, aumento de proteína y fibra.', 'Buena adherencia; continuar plan.', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(6, 1, 145, 3, '2026-07-15', 77.00, 170.00, 26.64, 26.0, 90.0, 100.0, 34.0, 1800, 72.00, 'Déficit calórico moderado, 5 comidas, aumento de proteína y fibra.', 'Buena adherencia; continuar plan.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.evaluaciones_oftalmo
CREATE TABLE IF NOT EXISTS `evaluaciones_oftalmo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `od_av` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `od_esfera` decimal(4,2) DEFAULT NULL,
  `od_cilindro` decimal(4,2) DEFAULT NULL,
  `od_eje` smallint unsigned DEFAULT NULL,
  `od_pio` decimal(4,1) DEFAULT NULL,
  `os_av` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os_esfera` decimal(4,2) DEFAULT NULL,
  `os_cilindro` decimal(4,2) DEFAULT NULL,
  `os_eje` smallint unsigned DEFAULT NULL,
  `os_pio` decimal(4,1) DEFAULT NULL,
  `diagnostico` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluaciones_oftalmo_paciente_id_foreign` (`paciente_id`),
  KEY `evaluaciones_oftalmo_user_id_foreign` (`user_id`),
  KEY `evaluaciones_oftalmo_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `evaluaciones_oftalmo_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_oftalmo_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_oftalmo_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.evaluaciones_oftalmo: ~0 rows (aproximadamente)
DELETE FROM `evaluaciones_oftalmo`;
INSERT INTO `evaluaciones_oftalmo` (`id`, `empresa_id`, `paciente_id`, `user_id`, `fecha`, `od_av`, `od_esfera`, `od_cilindro`, `od_eje`, `od_pio`, `os_av`, `os_esfera`, `os_cilindro`, `os_eje`, `os_pio`, `diagnostico`, `observaciones`, `created_at`, `updated_at`) VALUES
	(2, 1, 127, 3, '2026-07-01', '20/25', -1.25, -0.50, 90, 15.0, '20/30', -1.50, -0.75, 85, 16.0, 'Miopía con astigmatismo leve', 'Se prescribe corrección óptica; control anual.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.evoluciones
CREATE TABLE IF NOT EXISTS `evoluciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hospitalizacion_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `presion_arterial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia_cardiaca` int DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `saturacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evoluciones_hospitalizacion_id_foreign` (`hospitalizacion_id`),
  KEY `evoluciones_user_id_foreign` (`user_id`),
  CONSTRAINT `evoluciones_hospitalizacion_id_foreign` FOREIGN KEY (`hospitalizacion_id`) REFERENCES `hospitalizaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evoluciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.evoluciones: ~0 rows (aproximadamente)
DELETE FROM `evoluciones`;
INSERT INTO `evoluciones` (`id`, `hospitalizacion_id`, `user_id`, `fecha`, `nota`, `presion_arterial`, `frecuencia_cardiaca`, `temperatura`, `saturacion`, `created_at`, `updated_at`) VALUES
	(2, 2, 3, '2026-07-16 17:55:09', 'Paciente estable, afebril. Continua tratamiento.', '110/70', 76, 36.8, '98%', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.facturacion_configs
CREATE TABLE IF NOT EXISTS `facturacion_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `habilitada` tinyint(1) NOT NULL DEFAULT '0',
  `emitir_automatico` tinyint(1) NOT NULL DEFAULT '1',
  `driver` enum('ninguno','greenter') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ninguno',
  `entorno` enum('beta','produccion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beta',
  `ruc` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_comercial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_fiscal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sol_usuario` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sol_clave` text COLLATE utf8mb4_unicode_ci,
  `certificado_ruta` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie_boleta` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B001',
  `serie_factura` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F001',
  `serie_nota` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FC01',
  `serie_nota_boleta` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BC01',
  `correlativo_boleta` int unsigned NOT NULL DEFAULT '0',
  `correlativo_factura` int unsigned NOT NULL DEFAULT '0',
  `correlativo_nota` int unsigned NOT NULL DEFAULT '0',
  `correlativo_nota_boleta` int unsigned NOT NULL DEFAULT '0',
  `igv_porcentaje` decimal(5,2) NOT NULL DEFAULT '18.00',
  `afectacion_igv` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10',
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturacion_configs_empresa_id_unique` (`empresa_id`),
  CONSTRAINT `facturacion_configs_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.facturacion_configs: ~0 rows (aproximadamente)
DELETE FROM `facturacion_configs`;

-- Volcando estructura para tabla suite_saas_medico_modular.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla suite_saas_medico_modular.horarios_medico
CREATE TABLE IF NOT EXISTS `horarios_medico` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `dia_semana` tinyint unsigned NOT NULL,
  `hora_inicio` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_fin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `horarios_medico_empresa_id_foreign` (`empresa_id`),
  KEY `horarios_medico_user_id_dia_semana_index` (`user_id`,`dia_semana`),
  CONSTRAINT `horarios_medico_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `horarios_medico_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.horarios_medico: ~5 rows (aproximadamente)
DELETE FROM `horarios_medico`;
INSERT INTO `horarios_medico` (`id`, `empresa_id`, `user_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 1, '09:00', '13:00', 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(2, 1, 3, 2, '09:00', '13:00', 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 3, 3, '09:00', '13:00', 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(4, 1, 3, 4, '09:00', '13:00', 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(5, 1, 3, 5, '09:00', '13:00', 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25');

-- Volcando estructura para tabla suite_saas_medico_modular.hospitalizaciones
CREATE TABLE IF NOT EXISTS `hospitalizaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `cama_id` bigint unsigned DEFAULT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `fecha_alta` datetime DEFAULT NULL,
  `estado` enum('activa','alta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `motivo_ingreso` text COLLATE utf8mb4_unicode_ci,
  `diagnostico_ingreso` text COLLATE utf8mb4_unicode_ci,
  `resumen_alta` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospitalizaciones_paciente_id_foreign` (`paciente_id`),
  KEY `hospitalizaciones_cama_id_foreign` (`cama_id`),
  KEY `hospitalizaciones_medico_id_foreign` (`medico_id`),
  KEY `hospitalizaciones_especialidad_id_foreign` (`especialidad_id`),
  KEY `hospitalizaciones_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `hospitalizaciones_cama_id_foreign` FOREIGN KEY (`cama_id`) REFERENCES `camas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hospitalizaciones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospitalizaciones_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hospitalizaciones_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hospitalizaciones_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.hospitalizaciones: ~0 rows (aproximadamente)
DELETE FROM `hospitalizaciones`;
INSERT INTO `hospitalizaciones` (`id`, `empresa_id`, `paciente_id`, `cama_id`, `medico_id`, `especialidad_id`, `fecha_ingreso`, `fecha_alta`, `estado`, `motivo_ingreso`, `diagnostico_ingreso`, `resumen_alta`, `created_at`, `updated_at`) VALUES
	(2, 1, 89, 1, 3, 2, '2026-07-15 23:55:09', NULL, 'activa', 'Observacion por cuadro febril.', 'Sindrome febril en estudio.', NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.imagen_estudios
CREATE TABLE IF NOT EXISTS `imagen_estudios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `radiologo_id` bigint unsigned DEFAULT NULL,
  `modalidad` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('solicitado','realizado','informado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solicitado',
  `indicacion` text COLLATE utf8mb4_unicode_ci,
  `hallazgos` text COLLATE utf8mb4_unicode_ci,
  `conclusion` text COLLATE utf8mb4_unicode_ci,
  `archivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivo_nombre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imagen_estudios_paciente_id_foreign` (`paciente_id`),
  KEY `imagen_estudios_medico_id_foreign` (`medico_id`),
  KEY `imagen_estudios_radiologo_id_foreign` (`radiologo_id`),
  KEY `imagen_estudios_empresa_id_fecha_index` (`empresa_id`,`fecha`),
  CONSTRAINT `imagen_estudios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `imagen_estudios_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `imagen_estudios_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `imagen_estudios_radiologo_id_foreign` FOREIGN KEY (`radiologo_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.imagen_estudios: ~0 rows (aproximadamente)
DELETE FROM `imagen_estudios`;
INSERT INTO `imagen_estudios` (`id`, `empresa_id`, `paciente_id`, `medico_id`, `radiologo_id`, `modalidad`, `region`, `fecha`, `estado`, `indicacion`, `hallazgos`, `conclusion`, `archivo`, `archivo_nombre`, `created_at`, `updated_at`) VALUES
	(2, 1, 89, 3, 3, 'Radiografia', 'Torax', '2026-07-13', 'informado', 'Tos persistente.', 'Campos pulmonares sin consolidaciones. Silueta cardiaca normal.', 'Estudio dentro de limites normales.', NULL, NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.insumos
CREATE TABLE IF NOT EXISTS `insumos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad',
  `stock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `insumos_empresa_id_nombre_index` (`empresa_id`,`nombre`),
  CONSTRAINT `insumos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.insumos: ~5 rows (aproximadamente)
DELETE FROM `insumos`;
INSERT INTO `insumos` (`id`, `empresa_id`, `nombre`, `categoria`, `unidad`, `stock`, `stock_minimo`, `precio`, `codigo`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Guantes de nitrilo', 'Material', 'caja', 25.00, 10.00, 18.00, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(2, 1, 'Paracetamol 500mg', 'Medicamento', 'blister', 8.00, 15.00, 4.50, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 'Alcohol 70%', 'Material', 'litro', 12.00, 5.00, 9.00, NULL, 1, '2026-07-16 01:31:25', '2026-07-17 04:55:08'),
	(4, 1, 'Jeringas 5ml', 'Material', 'unidad', 120.00, 50.00, 0.60, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(5, 1, 'Anestesia dental', 'Medicamento', 'cartucho', 6.00, 20.00, 2.80, NULL, 1, '2026-07-16 01:31:25', '2026-07-17 04:55:08');

-- Volcando estructura para tabla suite_saas_medico_modular.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla suite_saas_medico_modular.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla suite_saas_medico_modular.lab_examenes
CREATE TABLE IF NOT EXISTS `lab_examenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_examenes_empresa_id_nombre_index` (`empresa_id`,`nombre`),
  CONSTRAINT `lab_examenes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.lab_examenes: ~6 rows (aproximadamente)
DELETE FROM `lab_examenes`;
INSERT INTO `lab_examenes` (`id`, `empresa_id`, `nombre`, `categoria`, `unidad`, `valor_referencia`, `precio`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Hemoglobina', 'Hematologia', 'g/dL', '12-16', 15.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, 'Glucosa', 'Bioquimica', 'mg/dL', '70-110', 12.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(3, 1, 'Colesterol total', 'Bioquimica', 'mg/dL', '<200', 18.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(4, 1, 'Trigliceridos', 'Bioquimica', 'mg/dL', '<150', 18.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(5, 1, 'Creatinina', 'Bioquimica', 'mg/dL', '0.6-1.2', 14.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(6, 1, 'Examen de orina', 'Uroanalisis', '', 'Normal', 20.00, 1, '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.lab_ordenes
CREATE TABLE IF NOT EXISTS `lab_ordenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('solicitada','en_proceso','completada','entregada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solicitada',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_ordenes_paciente_id_foreign` (`paciente_id`),
  KEY `lab_ordenes_medico_id_foreign` (`medico_id`),
  KEY `lab_ordenes_consulta_id_foreign` (`consulta_id`),
  KEY `lab_ordenes_empresa_id_fecha_index` (`empresa_id`,`fecha`),
  CONSTRAINT `lab_ordenes_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lab_ordenes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_ordenes_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lab_ordenes_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.lab_ordenes: ~0 rows (aproximadamente)
DELETE FROM `lab_ordenes`;
INSERT INTO `lab_ordenes` (`id`, `empresa_id`, `paciente_id`, `medico_id`, `consulta_id`, `fecha`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(2, 1, 89, 3, NULL, '2026-07-14', 'completada', 'Chequeo de rutina', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.lab_orden_items
CREATE TABLE IF NOT EXISTS `lab_orden_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lab_orden_id` bigint unsigned NOT NULL,
  `lab_examen_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_referencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resultado` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuera_rango` tinyint(1) NOT NULL DEFAULT '0',
  `notas` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_orden_items_lab_orden_id_foreign` (`lab_orden_id`),
  KEY `lab_orden_items_lab_examen_id_foreign` (`lab_examen_id`),
  CONSTRAINT `lab_orden_items_lab_examen_id_foreign` FOREIGN KEY (`lab_examen_id`) REFERENCES `lab_examenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lab_orden_items_lab_orden_id_foreign` FOREIGN KEY (`lab_orden_id`) REFERENCES `lab_ordenes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.lab_orden_items: ~3 rows (aproximadamente)
DELETE FROM `lab_orden_items`;
INSERT INTO `lab_orden_items` (`id`, `lab_orden_id`, `lab_examen_id`, `nombre`, `unidad`, `valor_referencia`, `resultado`, `fuera_rango`, `notas`, `created_at`, `updated_at`) VALUES
	(4, 2, NULL, 'Hemoglobina', 'g/dL', '12-16', '13.5', 0, NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(5, 2, NULL, 'Glucosa', 'mg/dL', '70-110', '128', 1, 'Repetir en ayunas', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(6, 2, NULL, 'Colesterol total', 'mg/dL', '<200', '185', 0, NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.migrations: ~55 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0000_01_01_000000_create_especialidades_table', 1),
	(2, '0000_01_01_000001_create_empresas_table', 1),
	(3, '0001_01_01_000000_create_users_table', 1),
	(4, '0001_01_01_000001_create_cache_table', 1),
	(5, '0001_01_01_000002_create_jobs_table', 1),
	(6, '2026_01_01_000000_create_pacientes_table', 1),
	(7, '2026_01_01_000001_create_citas_table', 1),
	(8, '2026_01_01_000002_create_consultas_table', 1),
	(9, '2026_02_01_000000_create_pagos_table', 1),
	(10, '2026_02_01_000001_add_config_to_empresas_table', 1),
	(11, '2026_03_01_000000_add_portal_to_pacientes_table', 1),
	(12, '2026_03_01_000001_create_insumos_table', 1),
	(13, '2026_03_01_000002_create_movimientos_insumo_table', 1),
	(14, '2026_04_01_000000_create_notificaciones_table', 1),
	(15, '2026_04_01_000001_create_auditorias_table', 1),
	(16, '2026_04_01_000002_create_receta_items_table', 1),
	(17, '2026_04_01_000003_add_profesional_to_users_table', 1),
	(18, '2026_05_01_000000_add_teleconsulta_to_citas_table', 1),
	(19, '2026_05_01_000001_create_adjuntos_table', 1),
	(20, '2026_05_01_000002_create_horarios_medico_table', 1),
	(21, '2026_05_01_000003_create_servicios_table', 1),
	(22, '2026_06_01_000000_add_firma_to_users_table', 1),
	(23, '2026_06_01_000001_create_encuestas_table', 1),
	(24, '2026_06_01_000002_add_sala_to_citas_table', 1),
	(25, '2026_07_01_000000_create_vacunas_table', 1),
	(26, '2026_07_01_000001_add_preferencias_to_users_table', 1),
	(27, '2026_08_01_000000_create_lab_examenes_table', 1),
	(28, '2026_08_01_000001_create_lab_ordenes_table', 1),
	(29, '2026_08_01_000002_create_lab_orden_items_table', 1),
	(30, '2026_09_01_000000_create_camas_table', 1),
	(31, '2026_09_01_000001_create_hospitalizaciones_table', 1),
	(32, '2026_09_01_000002_create_evoluciones_table', 1),
	(33, '2026_10_01_000000_create_imagen_estudios_table', 1),
	(34, '2026_11_01_000000_create_triajes_table', 1),
	(35, '2026_12_01_000000_create_dispensaciones_table', 1),
	(36, '2026_12_01_000001_create_dispensacion_items_table', 1),
	(37, '2027_01_01_000000_create_donantes_table', 1),
	(38, '2027_01_01_000001_create_unidades_sangre_table', 1),
	(39, '2027_01_01_000002_create_solicitudes_sangre_table', 1),
	(40, '2027_02_01_000000_create_odontogramas_table', 1),
	(41, '2027_03_01_000000_create_embarazos_table', 1),
	(42, '2027_04_01_000000_create_evaluaciones_cardio_table', 1),
	(43, '2027_05_01_000000_create_dermatogramas_table', 1),
	(44, '2027_06_01_000000_create_sesiones_psicologicas_table', 1),
	(45, '2027_07_01_000000_create_evaluaciones_oftalmo_table', 1),
	(46, '2027_08_01_000000_create_evaluaciones_nutricion_table', 1),
	(47, '2027_09_01_000000_create_traumatogramas_table', 1),
	(48, '2027_10_01_000000_create_evaluaciones_especialidad_table', 1),
	(49, '2027_11_01_000000_add_formato_to_empresas_table', 1),
	(50, '2027_12_01_000000_create_planes_table', 2),
	(51, '2027_12_01_000001_create_suscripciones_table', 2),
	(52, '2027_12_01_000002_add_plan_id_to_empresas_table', 2),
	(53, '2028_01_01_000000_create_facturacion_configs_table', 3),
	(54, '2028_01_01_000001_create_comprobantes_table', 3),
	(55, '2028_01_01_000002_add_notas_to_facturacion', 4),
	(56, '2028_01_01_000003_create_resumenes_table', 5),
	(57, '2028_01_01_000004_add_afectacion_igv', 6),
	(58, '2028_01_01_000005_add_cdr_path_to_comprobantes', 6),
	(59, '2028_01_01_000006_add_baja_pendiente_and_nota_series', 6);

-- Volcando estructura para tabla suite_saas_medico_modular.movimientos_insumo
CREATE TABLE IF NOT EXISTS `movimientos_insumo` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `insumo_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('entrada','salida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_insumo_empresa_id_foreign` (`empresa_id`),
  KEY `movimientos_insumo_insumo_id_foreign` (`insumo_id`),
  KEY `movimientos_insumo_user_id_foreign` (`user_id`),
  CONSTRAINT `movimientos_insumo_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_insumo_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_insumo_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.movimientos_insumo: ~0 rows (aproximadamente)
DELETE FROM `movimientos_insumo`;

-- Volcando estructura para tabla suite_saas_medico_modular.notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `icono` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-bell',
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificaciones_user_id_foreign` (`user_id`),
  KEY `notificaciones_empresa_id_leido_index` (`empresa_id`,`leido`),
  CONSTRAINT `notificaciones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notificaciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.notificaciones: ~0 rows (aproximadamente)
DELETE FROM `notificaciones`;

-- Volcando estructura para tabla suite_saas_medico_modular.odontogramas
CREATE TABLE IF NOT EXISTS `odontogramas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `dientes` json DEFAULT NULL,
  `plan` json DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `odontogramas_paciente_id_unique` (`paciente_id`),
  KEY `odontogramas_user_id_foreign` (`user_id`),
  KEY `odontogramas_empresa_id_index` (`empresa_id`),
  CONSTRAINT `odontogramas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `odontogramas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `odontogramas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.odontogramas: ~0 rows (aproximadamente)
DELETE FROM `odontogramas`;
INSERT INTO `odontogramas` (`id`, `empresa_id`, `paciente_id`, `user_id`, `dientes`, `plan`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 3, '{"11": {"s": {"v": "obturado"}, "w": null}, "16": {"s": {"m": "caries", "o": "caries"}, "w": null}, "18": {"s": [], "w": "ausente"}, "21": {"s": {"v": "caries"}, "w": null}, "26": {"s": {"o": "obturado"}, "w": null}, "36": {"s": [], "w": "corona"}, "38": {"s": [], "w": "extraccion"}, "46": {"s": [], "w": "endodoncia"}, "47": {"s": [], "w": "implante"}}', '[{"costo": 90, "pieza": "16", "estado": "pendiente", "procedimiento": "Obturación con resina"}, {"costo": 380, "pieza": "46", "estado": "en_proceso", "procedimiento": "Tratamiento de conducto"}, {"costo": 120, "pieza": "38", "estado": "pendiente", "procedimiento": "Exodoncia"}, {"costo": 450, "pieza": "36", "estado": "realizado", "procedimiento": "Corona de porcelana"}]', 'Paciente con buena higiene. Se recomienda control cada 6 meses y profilaxis.', '2026-07-16 01:31:26', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.pacientes
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_documento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `documento` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F','O') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acceso_portal` tinyint(1) NOT NULL DEFAULT '0',
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `antecedentes` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pacientes_especialidad_id_foreign` (`especialidad_id`),
  KEY `pacientes_empresa_id_apellidos_index` (`empresa_id`,`apellidos`),
  CONSTRAINT `pacientes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pacientes_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.pacientes: ~83 rows (aproximadamente)
DELETE FROM `pacientes`;
INSERT INTO `pacientes` (`id`, `empresa_id`, `especialidad_id`, `nombres`, `apellidos`, `tipo_documento`, `documento`, `fecha_nacimiento`, `sexo`, `telefono`, `email`, `password`, `acceso_portal`, `direccion`, `grupo_sanguineo`, `alergias`, `antecedentes`, `activo`, `created_at`, `updated_at`, `remember_token`) VALUES
	(1, 1, 2, 'Mateo', 'Gomez Ruiz', 'DNI', '75319852', '2019-04-12', 'M', '+51 911 222 333', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25', NULL),
	(2, 1, 3, 'Valentina', 'Salazar Diaz', 'DNI', '44125879', '1992-09-30', 'F', '+51 933 444 555', 'valentina@paciente.test', '$2y$12$NqBCzpVeXRk4laV8v.uLNuk4DgqS.pxMetax0/aD7hssk3/AMgBqi', 1, NULL, NULL, NULL, NULL, 1, '2026-07-16 01:31:25', '2026-07-17 04:55:08', NULL),
	(3, 1, 5, 'Sofia', 'Peralta Leon', 'DNI', '48896231', '1988-01-15', 'F', '+51 955 666 777', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25', NULL),
	(4, 1, 2, 'Diego', 'Rios Campos', 'DNI', '70012345', '2015-11-05', 'M', '+51 977 888 999', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25', NULL),
	(84, 1, 1, 'Lucas', 'Vargas Rojas', 'DNI', '75973947', '2000-10-09', 'F', '+51 992883798', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(85, 1, 1, 'Emma', 'Flores Mendoza', 'DNI', '79990612', '2021-06-16', 'M', '+51 933036522', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(86, 1, 1, 'Santiago', 'Castro Aguilar', 'DNI', '74430854', '2024-05-30', 'M', '+51 912650208', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(87, 1, 2, 'Mia', 'Rojas Herrera', 'DNI', '74463842', '1996-10-31', 'M', '+51 984916498', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(88, 1, 2, 'Thiago', 'Mendoza Cordova', 'DNI', '76987001', '2019-05-20', 'M', '+51 934507561', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(89, 1, 2, 'Isabella', 'Aguilar Paredes', 'DNI', '78869974', '2006-11-26', 'M', '+51 924879494', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(90, 1, 2, 'Benjamin', 'Herrera Quispe', 'DNI', '72022259', '1966-03-10', 'F', '+51 987389676', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(91, 1, 2, 'Camila', 'Cordova Vargas', 'DNI', '77085326', '1986-09-28', 'F', '+51 939822631', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(92, 1, 2, 'Martin', 'Paredes Flores', 'DNI', '79491152', '1998-01-18', 'M', '+51 976781214', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(93, 1, 3, 'Renata', 'Quispe Castro', 'DNI', '77848528', '2006-04-04', 'M', '+51 957687997', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(94, 1, 3, 'Joaquin', 'Vargas Rojas', 'DNI', '77383266', '2000-02-05', 'M', '+51 996752261', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(95, 1, 3, 'Antonella', 'Flores Mendoza', 'DNI', '76768568', '2021-06-19', 'M', '+51 998800341', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(96, 1, 3, 'Gael', 'Castro Aguilar', 'DNI', '71623728', '1997-10-09', 'M', '+51 949450827', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(97, 1, 4, 'Luciana', 'Rojas Herrera', 'DNI', '71893165', '1969-02-26', 'F', '+51 995416043', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(98, 1, 4, 'Dylan', 'Mendoza Cordova', 'DNI', '71591455', '1982-11-26', 'F', '+51 967297744', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(99, 1, 4, 'Lucas', 'Aguilar Paredes', 'DNI', '72123754', '2016-07-06', 'F', '+51 966418064', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(100, 1, 5, 'Emma', 'Herrera Quispe', 'DNI', '76273879', '1982-10-29', 'M', '+51 985709868', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(101, 1, 5, 'Santiago', 'Cordova Vargas', 'DNI', '72656015', '2000-01-06', 'F', '+51 991904839', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(102, 1, 5, 'Mia', 'Paredes Flores', 'DNI', '73913342', '2012-07-08', 'M', '+51 999892176', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(103, 1, 6, 'Thiago', 'Quispe Castro', 'DNI', '78339069', '1983-10-22', 'F', '+51 932205674', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(104, 1, 6, 'Isabella', 'Vargas Rojas', 'DNI', '79141422', '2012-03-05', 'M', '+51 954230990', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(105, 1, 6, 'Benjamin', 'Flores Mendoza', 'DNI', '76696281', '1977-07-08', 'M', '+51 925318833', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(106, 1, 7, 'Camila', 'Castro Aguilar', 'DNI', '74427463', '2004-09-22', 'F', '+51 962044270', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(107, 1, 7, 'Martin', 'Rojas Herrera', 'DNI', '71727638', '1988-01-16', 'F', '+51 933509018', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(108, 1, 7, 'Renata', 'Mendoza Cordova', 'DNI', '78183019', '2008-11-05', 'F', '+51 976918164', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(109, 1, 8, 'Joaquin', 'Aguilar Paredes', 'DNI', '77600030', '2021-12-05', 'F', '+51 936387380', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(110, 1, 8, 'Antonella', 'Herrera Quispe', 'DNI', '71026869', '2016-11-28', 'F', '+51 983181540', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(111, 1, 8, 'Gael', 'Cordova Vargas', 'DNI', '78212693', '2000-02-06', 'M', '+51 971685157', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(112, 1, 9, 'Luciana', 'Paredes Flores', 'DNI', '74798566', '2024-05-05', 'M', '+51 979217048', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(113, 1, 9, 'Dylan', 'Quispe Castro', 'DNI', '75479711', '1992-12-26', 'M', '+51 962649612', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(114, 1, 9, 'Lucas', 'Vargas Rojas', 'DNI', '75432250', '1983-05-25', 'F', '+51 939416727', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(115, 1, 10, 'Emma', 'Flores Mendoza', 'DNI', '78738408', '1991-06-15', 'M', '+51 983567146', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(116, 1, 10, 'Santiago', 'Castro Aguilar', 'DNI', '76590949', '1985-03-12', 'F', '+51 911589006', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(117, 1, 10, 'Mia', 'Rojas Herrera', 'DNI', '71902790', '1987-12-19', 'F', '+51 965776780', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(118, 1, 11, 'Thiago', 'Mendoza Cordova', 'DNI', '75962068', '1975-06-19', 'M', '+51 985367763', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(119, 1, 11, 'Isabella', 'Aguilar Paredes', 'DNI', '74415743', '2022-09-23', 'M', '+51 969588545', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(120, 1, 11, 'Benjamin', 'Herrera Quispe', 'DNI', '77646063', '1966-04-29', 'F', '+51 983007087', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(121, 1, 12, 'Camila', 'Cordova Vargas', 'DNI', '71625651', '2010-12-10', 'M', '+51 947437076', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(122, 1, 12, 'Martin', 'Paredes Flores', 'DNI', '75425981', '1967-09-24', 'M', '+51 966202512', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(123, 1, 12, 'Renata', 'Quispe Castro', 'DNI', '71763915', '1994-01-02', 'F', '+51 921616264', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(124, 1, 13, 'Joaquin', 'Vargas Rojas', 'DNI', '71751641', '2003-01-10', 'M', '+51 951572773', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(125, 1, 13, 'Antonella', 'Flores Mendoza', 'DNI', '78896758', '2008-01-25', 'M', '+51 942720775', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(126, 1, 13, 'Gael', 'Castro Aguilar', 'DNI', '71119289', '1986-04-03', 'F', '+51 921176436', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(127, 1, 14, 'Luciana', 'Rojas Herrera', 'DNI', '74616052', '1978-11-13', 'F', '+51 915171310', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(128, 1, 14, 'Dylan', 'Mendoza Cordova', 'DNI', '73992596', '1987-02-25', 'M', '+51 922555666', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(129, 1, 14, 'Lucas', 'Aguilar Paredes', 'DNI', '71608380', '2016-05-06', 'F', '+51 998444422', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(130, 1, 15, 'Emma', 'Herrera Quispe', 'DNI', '79742954', '1978-04-24', 'M', '+51 951899056', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(131, 1, 15, 'Santiago', 'Cordova Vargas', 'DNI', '74263569', '1994-02-14', 'F', '+51 995296238', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(132, 1, 15, 'Mia', 'Paredes Flores', 'DNI', '75756137', '1985-06-26', 'F', '+51 988048781', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(133, 1, 16, 'Thiago', 'Quispe Castro', 'DNI', '73884242', '1983-12-09', 'M', '+51 937978245', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(134, 1, 16, 'Isabella', 'Vargas Rojas', 'DNI', '78903194', '1981-04-10', 'F', '+51 926267434', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(135, 1, 16, 'Benjamin', 'Flores Mendoza', 'DNI', '76088695', '1989-11-16', 'F', '+51 915033470', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(136, 1, 17, 'Camila', 'Castro Aguilar', 'DNI', '71806168', '2002-03-30', 'M', '+51 936894106', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(137, 1, 17, 'Martin', 'Rojas Herrera', 'DNI', '72341248', '1979-06-04', 'M', '+51 948268089', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(138, 1, 17, 'Renata', 'Mendoza Cordova', 'DNI', '77461963', '1975-01-20', 'F', '+51 951225322', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(139, 1, 18, 'Joaquin', 'Aguilar Paredes', 'DNI', '73572756', '2006-06-28', 'M', '+51 978902692', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(140, 1, 18, 'Antonella', 'Herrera Quispe', 'DNI', '78671146', '2017-03-19', 'M', '+51 924012611', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(141, 1, 18, 'Gael', 'Cordova Vargas', 'DNI', '75574711', '2006-04-26', 'F', '+51 985070464', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(142, 1, 19, 'Luciana', 'Paredes Flores', 'DNI', '75998503', '1976-10-20', 'M', '+51 911076204', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(143, 1, 19, 'Dylan', 'Quispe Castro', 'DNI', '78324419', '1977-12-13', 'M', '+51 937026835', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(144, 1, 19, 'Lucas', 'Vargas Rojas', 'DNI', '76878460', '1976-06-06', 'F', '+51 948841714', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(145, 1, 20, 'Emma', 'Flores Mendoza', 'DNI', '78672980', '1997-01-12', 'F', '+51 953177204', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(146, 1, 20, 'Santiago', 'Castro Aguilar', 'DNI', '73645032', '2005-03-03', 'M', '+51 944171339', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(147, 1, 20, 'Mia', 'Rojas Herrera', 'DNI', '71894597', '1984-03-20', 'M', '+51 936662959', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(148, 1, 21, 'Thiago', 'Mendoza Cordova', 'DNI', '75951349', '2022-03-29', 'F', '+51 970311827', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(149, 1, 21, 'Isabella', 'Aguilar Paredes', 'DNI', '73221427', '1991-09-30', 'M', '+51 958687155', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(150, 1, 21, 'Benjamin', 'Herrera Quispe', 'DNI', '72661912', '2013-01-23', 'M', '+51 946079105', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(151, 1, 22, 'Camila', 'Cordova Vargas', 'DNI', '78449559', '2000-09-29', 'M', '+51 945866279', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(152, 1, 22, 'Martin', 'Paredes Flores', 'DNI', '71032673', '2001-04-11', 'F', '+51 955213657', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(153, 1, 22, 'Renata', 'Quispe Castro', 'DNI', '73729721', '2013-03-15', 'F', '+51 958840591', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(154, 1, 23, 'Joaquin', 'Vargas Rojas', 'DNI', '75562673', '2024-04-04', 'F', '+51 964262498', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(155, 1, 23, 'Antonella', 'Flores Mendoza', 'DNI', '78326769', '1975-02-20', 'F', '+51 962534522', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(156, 1, 23, 'Gael', 'Castro Aguilar', 'DNI', '76775376', '1980-11-29', 'F', '+51 998237886', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(157, 1, 24, 'Luciana', 'Rojas Herrera', 'DNI', '73682272', '1970-12-11', 'M', '+51 921714530', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(158, 1, 24, 'Dylan', 'Mendoza Cordova', 'DNI', '73580066', '1991-06-10', 'M', '+51 968991240', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(159, 1, 24, 'Lucas', 'Aguilar Paredes', 'DNI', '75461356', '2011-12-15', 'F', '+51 986247488', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(160, 1, 25, 'Emma', 'Herrera Quispe', 'DNI', '73488038', '1980-07-02', 'M', '+51 952022351', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(161, 1, 25, 'Santiago', 'Cordova Vargas', 'DNI', '73298290', '2018-04-19', 'F', '+51 928996654', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL),
	(162, 1, 25, 'Mia', 'Paredes Flores', 'DNI', '75768693', '2018-06-24', 'F', '+51 973894636', NULL, NULL, 0, NULL, NULL, NULL, '[demo-hist]', 1, '2026-07-17 04:55:08', '2026-07-17 04:55:08', NULL);

-- Volcando estructura para tabla suite_saas_medico_modular.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `cita_id` bigint unsigned DEFAULT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `concepto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metodo` enum('efectivo','tarjeta','transferencia','yape_plin','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `estado` enum('pendiente','pagado','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagado',
  `fecha` date NOT NULL,
  `comprobante` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_paciente_id_foreign` (`paciente_id`),
  KEY `pagos_cita_id_foreign` (`cita_id`),
  KEY `pagos_consulta_id_foreign` (`consulta_id`),
  KEY `pagos_empresa_id_fecha_index` (`empresa_id`,`fecha`),
  CONSTRAINT `pagos_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pagos_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pagos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.pagos: ~45 rows (aproximadamente)
DELETE FROM `pagos`;
INSERT INTO `pagos` (`id`, `empresa_id`, `paciente_id`, `cita_id`, `consulta_id`, `concepto`, `monto`, `metodo`, `estado`, `fecha`, `comprobante`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL, 'Consulta medica', 80.00, 'efectivo', 'pendiente', '2026-07-16', NULL, NULL, '2026-07-16 01:31:25', '2026-07-17 04:55:07'),
	(2, 1, 3, NULL, NULL, 'Control', 120.00, 'tarjeta', 'pagado', '2026-07-12', NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 4, NULL, NULL, 'Tratamiento', 150.00, 'yape_plin', 'pagado', '2026-07-09', NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(4, 1, 2, NULL, NULL, 'Consulta medica', 60.00, 'efectivo', 'pagado', '2026-07-06', NULL, NULL, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(44, 1, 2, NULL, NULL, 'Control', 120.00, 'tarjeta', 'pagado', '2026-07-13', NULL, NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(45, 1, 3, NULL, NULL, 'Tratamiento', 150.00, 'yape_plin', 'pagado', '2026-07-10', NULL, NULL, '2026-07-17 04:55:07', '2026-07-17 04:55:07'),
	(114, 1, 158, 171, NULL, 'Consulta medica', 150.00, 'transferencia', 'pagado', '2026-02-23', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(115, 1, 141, 172, NULL, 'Consulta medica', 200.00, 'efectivo', 'pagado', '2026-02-25', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(116, 1, 93, 175, NULL, 'Consulta medica', 90.00, 'efectivo', 'pagado', '2026-02-13', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(117, 1, 115, 176, NULL, 'Consulta medica', 80.00, 'yape_plin', 'pagado', '2026-02-04', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(118, 1, 88, 177, NULL, 'Consulta medica', 80.00, 'tarjeta', 'pagado', '2026-02-11', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(119, 1, 147, 178, NULL, 'Consulta medica', 90.00, 'transferencia', 'pagado', '2026-02-22', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(120, 1, 129, 179, NULL, 'Consulta medica', 150.00, 'efectivo', 'pagado', '2026-03-17', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(121, 1, 113, 180, NULL, 'Consulta medica', 120.00, 'transferencia', 'pagado', '2026-03-27', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(122, 1, 117, 181, NULL, 'Consulta medica', 60.00, 'efectivo', 'pagado', '2026-03-01', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(123, 1, 138, 183, NULL, 'Consulta medica', 80.00, 'transferencia', 'pagado', '2026-03-02', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(124, 1, 144, 184, NULL, 'Consulta medica', 60.00, 'tarjeta', 'pagado', '2026-03-30', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(125, 1, 150, 185, NULL, 'Consulta medica', 200.00, 'tarjeta', 'pagado', '2026-03-17', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(126, 1, 156, 186, NULL, 'Consulta medica', 150.00, 'transferencia', 'pagado', '2026-03-08', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(127, 1, 95, 187, NULL, 'Consulta medica', 80.00, 'yape_plin', 'pagado', '2026-03-20', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(128, 1, 145, 188, NULL, 'Consulta medica', 60.00, 'transferencia', 'pagado', '2026-03-30', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(129, 1, 133, 189, NULL, 'Consulta medica', 80.00, 'yape_plin', 'pagado', '2026-03-18', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(130, 1, 116, 192, NULL, 'Consulta medica', 180.00, 'efectivo', 'pagado', '2026-03-28', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(131, 1, 161, 194, NULL, 'Consulta medica', 90.00, 'transferencia', 'pagado', '2026-04-14', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(132, 1, 140, 195, NULL, 'Consulta medica', 90.00, 'yape_plin', 'pagado', '2026-04-26', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(133, 1, 137, 197, NULL, 'Consulta medica', 120.00, 'efectivo', 'pagado', '2026-04-23', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(134, 1, 142, 202, NULL, 'Consulta medica', 200.00, 'tarjeta', 'pagado', '2026-05-30', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(135, 1, 132, 203, NULL, 'Consulta medica', 60.00, 'efectivo', 'pagado', '2026-05-10', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(136, 1, 153, 205, NULL, 'Consulta medica', 90.00, 'tarjeta', 'pagado', '2026-05-28', NULL, '[demo-hist]', '2026-07-17 04:55:08', '2026-07-17 04:55:08'),
	(137, 1, 128, 206, NULL, 'Consulta medica', 120.00, 'efectivo', 'pagado', '2026-05-27', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(138, 1, 127, 207, NULL, 'Consulta medica', 90.00, 'transferencia', 'pagado', '2026-05-21', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(139, 1, 91, 208, NULL, 'Consulta medica', 150.00, 'tarjeta', 'pagado', '2026-05-02', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(140, 1, 149, 210, NULL, 'Consulta medica', 200.00, 'efectivo', 'pagado', '2026-05-16', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(141, 1, 161, 211, NULL, 'Consulta medica', 90.00, 'transferencia', 'pagado', '2026-05-18', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(142, 1, 156, 212, NULL, 'Consulta medica', 60.00, 'tarjeta', 'pagado', '2026-05-29', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(143, 1, 148, 213, NULL, 'Consulta medica', 60.00, 'efectivo', 'pagado', '2026-05-30', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(144, 1, 131, 214, NULL, 'Consulta medica', 90.00, 'transferencia', 'pagado', '2026-05-28', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(145, 1, 162, 215, NULL, 'Consulta medica', 80.00, 'tarjeta', 'pagado', '2026-05-06', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(146, 1, 158, 217, NULL, 'Consulta medica', 60.00, 'efectivo', 'pagado', '2026-05-13', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(147, 1, 102, 219, NULL, 'Consulta medica', 60.00, 'tarjeta', 'pagado', '2026-06-14', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(148, 1, 102, 220, NULL, 'Consulta medica', 150.00, 'transferencia', 'pagado', '2026-06-14', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(149, 1, 95, 223, NULL, 'Consulta medica', 120.00, 'yape_plin', 'pagado', '2026-06-05', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(150, 1, 109, 225, NULL, 'Consulta medica', 60.00, 'transferencia', 'pagado', '2026-06-23', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(151, 1, 156, 227, NULL, 'Consulta medica', 150.00, 'tarjeta', 'pagado', '2026-06-10', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(152, 1, 136, 228, NULL, 'Consulta medica', 200.00, 'yape_plin', 'pagado', '2026-06-12', NULL, '[demo-hist]', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla suite_saas_medico_modular.planes
CREATE TABLE IF NOT EXISTS `planes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ciclo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limite_especialidades` smallint unsigned DEFAULT NULL,
  `limite_usuarios` smallint unsigned DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `planes_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.planes: ~2 rows (aproximadamente)
DELETE FROM `planes`;
INSERT INTO `planes` (`id`, `nombre`, `slug`, `precio`, `ciclo`, `descripcion`, `limite_especialidades`, `limite_usuarios`, `destacado`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
	(1, 'Básico', 'basico', 49.00, 'mensual', 'Ideal para consultorios pequeños.', 3, 5, 0, 1, 1, '2026-07-17 04:55:05', '2026-07-17 04:55:05'),
	(2, 'Profesional', 'profesional', 99.00, 'mensual', 'Para clínicas en crecimiento.', 10, 20, 1, 1, 2, '2026-07-17 04:55:05', '2026-07-17 04:55:05'),
	(3, 'Premium', 'premium', 199.00, 'mensual', 'Todas las especialidades y módulos.', NULL, NULL, 0, 1, 3, '2026-07-17 04:55:05', '2026-07-17 04:55:05');

-- Volcando estructura para tabla suite_saas_medico_modular.receta_items
CREATE TABLE IF NOT EXISTS `receta_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `consulta_id` bigint unsigned NOT NULL,
  `medicamento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `presentacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosis` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indicaciones` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receta_items_consulta_id_foreign` (`consulta_id`),
  CONSTRAINT `receta_items_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.receta_items: ~0 rows (aproximadamente)
DELETE FROM `receta_items`;

-- Volcando estructura para tabla suite_saas_medico_modular.resumenes
CREATE TABLE IF NOT EXISTS `resumenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `fecha_generacion` date NOT NULL,
  `fecha_resumen` date NOT NULL,
  `correlativo` int unsigned NOT NULL,
  `identificador` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','enviado','aceptado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `sunat_ticket` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_respuesta` text COLLATE utf8mb4_unicode_ci,
  `xml_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_documentos` int unsigned NOT NULL DEFAULT '0',
  `total_importe` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resumenes_empresa_id_fecha_resumen_correlativo_unique` (`empresa_id`,`fecha_resumen`,`correlativo`),
  KEY `resumenes_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `resumenes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.resumenes: ~0 rows (aproximadamente)
DELETE FROM `resumenes`;

-- Volcando estructura para tabla suite_saas_medico_modular.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servicios_especialidad_id_foreign` (`especialidad_id`),
  KEY `servicios_empresa_id_index` (`empresa_id`),
  CONSTRAINT `servicios_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `servicios_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.servicios: ~4 rows (aproximadamente)
DELETE FROM `servicios`;
INSERT INTO `servicios` (`id`, `empresa_id`, `especialidad_id`, `nombre`, `precio`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 'Consulta pediatrica', 80.00, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(2, 1, 3, 'Control ginecologico', 120.00, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(3, 1, 5, 'Limpieza dental', 90.00, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25'),
	(4, 1, NULL, 'Consulta general', 60.00, 1, '2026-07-16 01:31:25', '2026-07-16 01:31:25');

-- Volcando estructura para tabla suite_saas_medico_modular.sesiones_psicologicas
CREATE TABLE IF NOT EXISTS `sesiones_psicologicas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `numero` smallint unsigned DEFAULT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enfoque` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desarrollo` text COLLATE utf8mb4_unicode_ci,
  `tareas` text COLLATE utf8mb4_unicode_ci,
  `estado_animo` tinyint unsigned DEFAULT NULL,
  `progreso` tinyint unsigned DEFAULT NULL,
  `proxima_cita` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sesiones_psicologicas_paciente_id_foreign` (`paciente_id`),
  KEY `sesiones_psicologicas_user_id_foreign` (`user_id`),
  KEY `sesiones_psicologicas_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `sesiones_psicologicas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sesiones_psicologicas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sesiones_psicologicas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.sesiones_psicologicas: ~3 rows (aproximadamente)
DELETE FROM `sesiones_psicologicas`;
INSERT INTO `sesiones_psicologicas` (`id`, `empresa_id`, `paciente_id`, `user_id`, `fecha`, `numero`, `motivo`, `enfoque`, `desarrollo`, `tareas`, `estado_animo`, `progreso`, `proxima_cita`, `created_at`, `updated_at`) VALUES
	(4, 1, 118, 3, '2026-05-17', 1, 'Ansiedad generalizada', 'TCC', 'Se trabajó con técnicas de respiración y registro de pensamientos.', 'Registro diario de emociones.', 4, 20, '2026-07-23', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(5, 1, 118, 3, '2026-06-01', 2, 'Manejo de ansiedad', 'TCC', 'Se trabajó con técnicas de respiración y registro de pensamientos.', 'Registro diario de emociones.', 5, 40, '2026-07-23', '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(6, 1, 118, 3, '2026-06-16', 3, 'Reestructuración cognitiva', 'TCC', 'Se trabajó con técnicas de respiración y registro de pensamientos.', 'Registro diario de emociones.', 7, 65, '2026-07-23', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.sessions: ~10 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('20G42JnOia8OBzSPVZMbL1JnVBjGeb1nWstlgkrf', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRE0xYmpyUnFHSlVvSUVibU5zQnN2MDFldHZZbFpqeFM3MTNxekVYRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1786262401),
	('KEtwur9dZdct6mtY3FQvcuahPWHSoiMDgRp1lCAK', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV01BV2tnMmFCemhJNUd0c2VRY3Mza2k0STJVOFkzeU1PR3BKMjlLTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785538318),
	('lCuJ069SKYpNiilRCHTFJxukx35loAmvvtGmnGh1', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVBhQlAyZk9welJ0ZGljdnBzNUJSUml0T25lQnhhMEZWVExqWUVTeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785548655),
	('NagclWQzbT1UKwRTsIqYIHMZbh2yf0yc5rG1RBKh', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNXNsWXlobXR3SlhFcDBaUjdPYVVZSEtpRXdCRXZBdXpsSUhPUG1GYiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785678806),
	('OE6GXiMIo549z7xLvWa8OgsvVwypQVX4b1mMFIOb', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUDBJdEt2cVZrNGlIcDhGOEFuZzVRaVlKSXY2UVF3bXV0cEJaVFdpaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785687422),
	('PzFqIhpCixMafEOi7Zj328Cv3nGAtvu4S8hka17k', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTzZXcmNrUmw2TjFubEhneGRFZEtjWHBoQ0ptVVJiYUxYRkN6cmpwZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1786284008),
	('Rb7niLQ7JRqj2WwNkFprIZksylyZmp7zjXQSjuS7', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSXpCeFNHZ3FiUlBZZGRPYXVpNWs3UW1LSm9FUUpPRnNZT0ZnM09oeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785779252),
	('rEIWwezg7AP9Unrhx4oORsMdDfr4wCGprQ47trOg', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYUpCMUluNkZHUGtSYjNVWE9helJKcVZIUkpmbmZmSTcwVkNGMmo2SCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785610344),
	('tiAj9HulqD83IYV59Os1LqSCJFtLZZwQ8goiDI8A', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNENSbDkxSGtEbWdVYkI2Z216VjNMbWo0cjJNa2RCc1ZZRHdhSHdkNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785600587),
	('wID0XZchP9Wgu5QQhhaGNj0daK7LbZ0Hm7HgASrb', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWXF6bW1HRzZyVTdRVjFKUDNGR1BGTW5GVDlPNmlnTDhIVjdUMGV3ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA2MC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1785585371);

-- Volcando estructura para tabla suite_saas_medico_modular.solicitudes_sangre
CREATE TABLE IF NOT EXISTS `solicitudes_sangre` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `grupo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `fecha` date NOT NULL,
  `estado` enum('pendiente','atendida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitudes_sangre_paciente_id_foreign` (`paciente_id`),
  KEY `solicitudes_sangre_medico_id_foreign` (`medico_id`),
  KEY `solicitudes_sangre_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `solicitudes_sangre_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_sangre_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `solicitudes_sangre_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.solicitudes_sangre: ~0 rows (aproximadamente)
DELETE FROM `solicitudes_sangre`;
INSERT INTO `solicitudes_sangre` (`id`, `empresa_id`, `paciente_id`, `medico_id`, `grupo`, `cantidad`, `fecha`, `estado`, `motivo`, `created_at`, `updated_at`) VALUES
	(2, 1, 89, 3, 'A+', 2, '2026-07-16', 'pendiente', 'Anemia severa', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.suscripciones
CREATE TABLE IF NOT EXISTS `suscripciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ticket` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ciclo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `duracion` smallint unsigned NOT NULL DEFAULT '1',
  `unidad` enum('meses','anios') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'meses',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` enum('monto','porcentaje') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monto',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `metodo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suscripciones_plan_id_foreign` (`plan_id`),
  KEY `suscripciones_user_id_foreign` (`user_id`),
  KEY `suscripciones_empresa_id_fecha_fin_index` (`empresa_id`,`fecha_fin`),
  KEY `suscripciones_ticket_index` (`ticket`),
  CONSTRAINT `suscripciones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `suscripciones_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suscripciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.suscripciones: ~1 rows (aproximadamente)
DELETE FROM `suscripciones`;
INSERT INTO `suscripciones` (`id`, `empresa_id`, `plan_id`, `user_id`, `ticket`, `plan_nombre`, `plan_precio`, `ciclo`, `duracion`, `unidad`, `descuento`, `tipo_descuento`, `subtotal`, `total`, `fecha_inicio`, `fecha_fin`, `metodo`, `nota`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, NULL, 'SUS-000001', 'Profesional', 99.00, 'mensual', 6, 'meses', 0.00, 'monto', 594.00, 594.00, '2026-06-16', '2026-12-16', 'Ticket / manual', 'Suscripción inicial (demo).', '2026-07-17 04:55:05', '2026-07-17 04:55:05');

-- Volcando estructura para tabla suite_saas_medico_modular.traumatogramas
CREATE TABLE IF NOT EXISTS `traumatogramas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `lesiones` json DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `traumatogramas_paciente_id_unique` (`paciente_id`),
  KEY `traumatogramas_user_id_foreign` (`user_id`),
  KEY `traumatogramas_empresa_id_index` (`empresa_id`),
  CONSTRAINT `traumatogramas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `traumatogramas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `traumatogramas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.traumatogramas: ~0 rows (aproximadamente)
DELETE FROM `traumatogramas`;
INSERT INTO `traumatogramas` (`id`, `empresa_id`, `paciente_id`, `user_id`, `lesiones`, `notas`, `created_at`, `updated_at`) VALUES
	(2, 1, 133, 3, '[{"x": 40, "y": 74, "tipo": "fractura", "vista": "frente", "descripcion": "Fractura de muñeca izquierda."}, {"x": 44, "y": 88, "tipo": "esguince", "vista": "frente", "descripcion": "Esguince de tobillo grado II."}, {"x": 50, "y": 42, "tipo": "contusion", "vista": "espalda", "descripcion": "Contusión lumbar."}]', 'Fractura inmovilizada con yeso. Control en 4 semanas y fisioterapia.', '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.triajes
CREATE TABLE IF NOT EXISTS `triajes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `nivel` tinyint unsigned NOT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `presion_arterial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia_cardiaca` int DEFAULT NULL,
  `frecuencia_respiratoria` int DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `saturacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dolor` tinyint unsigned DEFAULT NULL,
  `estado` enum('en_espera','en_atencion','atendido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_espera',
  `hora_llegada` datetime NOT NULL,
  `hora_atencion` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `triajes_paciente_id_foreign` (`paciente_id`),
  KEY `triajes_user_id_foreign` (`user_id`),
  KEY `triajes_medico_id_foreign` (`medico_id`),
  KEY `triajes_empresa_id_estado_nivel_index` (`empresa_id`,`estado`,`nivel`),
  CONSTRAINT `triajes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `triajes_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `triajes_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `triajes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.triajes: ~2 rows (aproximadamente)
DELETE FROM `triajes`;
INSERT INTO `triajes` (`id`, `empresa_id`, `paciente_id`, `user_id`, `medico_id`, `nivel`, `motivo`, `presion_arterial`, `frecuencia_cardiaca`, `frecuencia_respiratoria`, `temperatura`, `saturacion`, `dolor`, `estado`, `hora_llegada`, `hora_atencion`, `observaciones`, `created_at`, `updated_at`) VALUES
	(3, 1, 89, 3, NULL, 2, 'Dolor toracico agudo', '130/85', 88, NULL, NULL, '97%', 8, 'en_espera', '2026-07-16 23:45:09', NULL, NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09'),
	(4, 1, 99, 3, NULL, 4, 'Dolor de garganta', '130/85', 88, NULL, NULL, '97%', 3, 'en_espera', '2026-07-16 23:35:09', NULL, NULL, '2026-07-17 04:55:09', '2026-07-17 04:55:09');

-- Volcando estructura para tabla suite_saas_medico_modular.unidades_sangre
CREATE TABLE IF NOT EXISTS `unidades_sangre` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `donante_id` bigint unsigned DEFAULT NULL,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `volumen` int NOT NULL DEFAULT '450',
  `fecha_extraccion` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('disponible','reservada','transfundida','descartada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unidades_sangre_donante_id_foreign` (`donante_id`),
  KEY `unidades_sangre_empresa_id_grupo_estado_index` (`empresa_id`,`grupo`,`estado`),
  CONSTRAINT `unidades_sangre_donante_id_foreign` FOREIGN KEY (`donante_id`) REFERENCES `donantes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `unidades_sangre_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.unidades_sangre: ~11 rows (aproximadamente)
DELETE FROM `unidades_sangre`;
INSERT INTO `unidades_sangre` (`id`, `empresa_id`, `donante_id`, `codigo`, `grupo`, `volumen`, `fecha_extraccion`, `fecha_vencimiento`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'U9480CA', 'O+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(2, 1, NULL, 'UA6CCB2', 'O+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(3, 1, NULL, 'U2E52B4', 'O+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(4, 1, NULL, 'U2E3711', 'O+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(5, 1, NULL, 'U0B4ACA', 'O+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(6, 1, NULL, 'U5806FF', 'A+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(7, 1, NULL, 'U90C9CD', 'A+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(8, 1, NULL, 'U39EB57', 'A+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(9, 1, NULL, 'U51BCE2', 'O-', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(10, 1, NULL, 'U3AD6FA', 'O-', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26'),
	(11, 1, NULL, 'UE57586', 'B+', 450, '2026-07-05', '2026-08-16', 'disponible', '2026-07-16 01:31:26', '2026-07-16 01:31:26');

-- Volcando estructura para tabla suite_saas_medico_modular.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned DEFAULT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','medico','recepcion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recepcion',
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulo_profesional` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma` longtext COLLATE utf8mb4_unicode_ci,
  `preferencias` json DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_empresa_id_foreign` (`empresa_id`),
  KEY `users_especialidad_id_foreign` (`especialidad_id`),
  CONSTRAINT `users_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.users: ~4 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `empresa_id`, `especialidad_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `telefono`, `cmp`, `titulo_profesional`, `firma`, `preferencias`, `avatar`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, NULL, NULL, 'Super Administrador', 'superadmin@suitesalud.test', NULL, '$2y$12$UicKUG4ivYk1lkW1uP97r.0sxSwvrRbiwqRXzsN0bcFnP0KLE2I/S', 'superadmin', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-07-16 01:31:24', '2026-07-17 04:55:05'),
	(2, 1, NULL, 'Ana Torres', 'admin@clinicavida.test', NULL, '$2y$12$TDLKT/5NfjLyHamBr3VziecYyYx91.3m0adlSziQvv0StckcXJ2j.', 'admin', '+51 900 111 222', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-07-16 01:31:25', '2026-07-17 04:55:06'),
	(3, 1, 2, 'Dr. Carlos Ramirez', 'medico@clinicavida.test', NULL, '$2y$12$dcoNd1OK0XCEB/uJjUBrsOtpFMhW1qLtKa1Ol4VFNdP16bnxhrInu', 'medico', '+51 900 333 444', '45821', 'Dr.', NULL, NULL, NULL, 1, NULL, '2026-07-16 01:31:25', '2026-07-17 04:55:06'),
	(4, 1, NULL, 'Lucia Fernandez', 'recepcion@clinicavida.test', NULL, '$2y$12$z8dQNZgsT88UAUZlJq36OuIgdHlTg.hML5xrHWrh1AR.6okhgJIJm', 'recepcion', '+51 900 555 666', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-07-16 01:31:25', '2026-07-17 04:55:06');

-- Volcando estructura para tabla suite_saas_medico_modular.vacunas
CREATE TABLE IF NOT EXISTS `vacunas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosis` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `fecha_aplicada` date DEFAULT NULL,
  `estado` enum('pendiente','aplicada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacunas_paciente_id_foreign` (`paciente_id`),
  KEY `vacunas_user_id_foreign` (`user_id`),
  KEY `vacunas_empresa_id_paciente_id_index` (`empresa_id`,`paciente_id`),
  CONSTRAINT `vacunas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vacunas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vacunas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla suite_saas_medico_modular.vacunas: ~0 rows (aproximadamente)
DELETE FROM `vacunas`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
