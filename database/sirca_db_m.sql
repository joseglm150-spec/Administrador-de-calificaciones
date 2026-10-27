-- ============================================================
-- Proyecto: Modernización de plataforma de calificaciones ITESP
-- Base de datos compatible con XAMPP (MariaDB/MySQL)
-- Basado en la estructura original sirca_db.sql.
--
-- IMPORTANTE:
-- 1. Respalda tu base actual antes de importar este archivo.
-- 2. Este script crea una base nueva y conserva los campos originales.
-- 3. Los campos nuevos están marcados con comentarios "NUEVO".
-- 4. No incluye datos reales de estudiantes ni docentes.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sirca_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sirca_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar en orden de dependencias para que el script se pueda volver a ejecutar.
DROP TABLE IF EXISTS `calificacion_evaluacion`;
DROP TABLE IF EXISTS `inscripciones_grupo`;
DROP TABLE IF EXISTS `materias_carreras`;
DROP TABLE IF EXISTS `grupos`;
DROP TABLE IF EXISTS `materias`;
DROP TABLE IF EXISTS `alumnos`;
DROP TABLE IF EXISTS `personal`;
DROP TABLE IF EXISTS `carreras`;
DROP TABLE IF EXISTS `periodos_escolares`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `bitacora_accesos`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. CARRERAS (estructura original conservada + campos nuevos)
-- ============================================================
CREATE TABLE `carreras` (
  `carrera` CHAR(3) NOT NULL,
  `reticula` INT NOT NULL,
  `nombre_carrera` VARCHAR(100) NOT NULL,
  `nivel_escolar` CHAR(1) DEFAULT NULL,
  `fecha_inicio` DATETIME DEFAULT NULL,
  `estado` ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA', -- NUEVO
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- NUEVO
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- NUEVO
  PRIMARY KEY (`carrera`, `reticula`),
  CONSTRAINT `chk_carreras_nivel` CHECK (`nivel_escolar` IN ('L','M',' ') OR `nivel_escolar` IS NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. ALUMNOS (todos los campos originales conservados + nuevos)
-- ============================================================
CREATE TABLE `alumnos` (
  `no_de_control` CHAR(10) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `sexo` ENUM('M','F','') DEFAULT '',
  `estado_civil` ENUM('S','C','D','V','U','') DEFAULT '',
  `carrera` CHAR(3) NOT NULL,
  `reticula` INT NOT NULL,
  `semestre_actual` INT DEFAULT 1,

  `correo_institucional` VARCHAR(150) DEFAULT NULL, -- NUEVO
  `correo_personal` VARCHAR(150) DEFAULT NULL, -- NUEVO
  `telefono_contacto` VARCHAR(20) DEFAULT NULL, -- NUEVO
  `fecha_ingreso` DATE DEFAULT NULL, -- NUEVO
  `estatus_academico` ENUM('ACTIVO','BAJA_TEMPORAL','BAJA_DEFINITIVA','EGRESADO','TITULADO') NOT NULL DEFAULT 'ACTIVO', -- NUEVO
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- NUEVO
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- NUEVO

  PRIMARY KEY (`no_de_control`),
  KEY `idx_alumnos_carrera` (`carrera`, `reticula`),
  KEY `idx_alumnos_estatus` (`estatus_academico`),
  UNIQUE KEY `uq_alumnos_correo_institucional` (`correo_institucional`),
  CONSTRAINT `fk_alumnos_carrera` FOREIGN KEY (`carrera`, `reticula`)
    REFERENCES `carreras` (`carrera`, `reticula`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_alumnos_semestre` CHECK (`semestre_actual` IS NULL OR `semestre_actual` BETWEEN 1 AND 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. PERSONAL / DOCENTES (todos los campos originales + nuevos)
-- ============================================================
CREATE TABLE `personal` (
  `rfc` CHAR(13) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `sexo` ENUM('M','F','') DEFAULT '',
  `estado_civil` ENUM('S','C','D','V','U','') DEFAULT '',
  `clave_centro_trabajo` CHAR(10) DEFAULT NULL,

  `correo_institucional` VARCHAR(150) DEFAULT NULL, -- NUEVO
  `correo_personal` VARCHAR(150) DEFAULT NULL, -- NUEVO
  `telefono_contacto` VARCHAR(20) DEFAULT NULL, -- NUEVO
  `tipo_personal` ENUM('DOCENTE','ADMINISTRATIVO','DIRECTIVO','OTRO') NOT NULL DEFAULT 'DOCENTE', -- NUEVO
  `especialidad` VARCHAR(120) DEFAULT NULL, -- NUEVO
  `grado_academico` VARCHAR(100) DEFAULT NULL, -- NUEVO
  `estatus_laboral` ENUM('ACTIVO','INACTIVO','LICENCIA','BAJA') NOT NULL DEFAULT 'ACTIVO', -- NUEVO
  `fecha_ingreso` DATE DEFAULT NULL, -- NUEVO
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- NUEVO
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- NUEVO

  PRIMARY KEY (`rfc`),
  UNIQUE KEY `uq_personal_correo_institucional` (`correo_institucional`),
  KEY `idx_personal_estatus` (`estatus_laboral`),
  KEY `idx_personal_tipo` (`tipo_personal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. MATERIAS (estructura original conservada + campos nuevos)
-- ============================================================
CREATE TABLE `materias` (
  `materia` CHAR(7) NOT NULL,
  `nombre_materia` VARCHAR(100) NOT NULL,
  `creditos` INT NOT NULL,
  `horas_teoricas` INT NOT NULL,
  `horas_practicas` INT NOT NULL,
  `descripcion` VARCHAR(500) DEFAULT NULL, -- NUEVO
  `estado` ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA', -- NUEVO
  PRIMARY KEY (`materia`),
  CONSTRAINT `chk_materias_creditos` CHECK (`creditos` >= 0),
  CONSTRAINT `chk_materias_horas` CHECK (`horas_teoricas` >= 0 AND `horas_practicas` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PERIODOS ESCOLARES (tabla nueva)
-- ============================================================
CREATE TABLE `periodos_escolares` (
  `id_periodo` INT NOT NULL AUTO_INCREMENT,
  `clave_periodo` CHAR(5) NOT NULL,
  `nombre_periodo` VARCHAR(80) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `estado` ENUM('PLANEADO','EN_CURSO','CERRADO') NOT NULL DEFAULT 'PLANEADO',
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_periodo`),
  UNIQUE KEY `uq_periodos_clave` (`clave_periodo`),
  CONSTRAINT `chk_periodos_fechas` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. GRUPOS (estructura original conservada + campos nuevos)
-- `periodo` conserva su clave original CHAR(5) para compatibilidad.
-- ============================================================
CREATE TABLE `grupos` (
  `id_grupo` INT NOT NULL AUTO_INCREMENT,
  `materia` CHAR(7) NOT NULL,
  `rfc_docente` CHAR(13) NOT NULL,
  `periodo` CHAR(5) NOT NULL,
  `clave_grupo` CHAR(3) NOT NULL,
  `capacidad` INT NOT NULL,
  `aula` VARCHAR(30) DEFAULT NULL, -- NUEVO
  `turno` ENUM('MATUTINO','VESPERTINO','MIXTO','NOCTURNO') DEFAULT NULL, -- NUEVO
  `modalidad` ENUM('PRESENCIAL','EN_LINEA','HIBRIDA') NOT NULL DEFAULT 'PRESENCIAL', -- NUEVO
  `estado` ENUM('ABIERTO','CERRADO','CANCELADO') NOT NULL DEFAULT 'ABIERTO', -- NUEVO
  PRIMARY KEY (`id_grupo`),
  KEY `idx_grupos_periodo` (`periodo`),
  KEY `idx_grupos_materia` (`materia`),
  KEY `idx_grupos_docente` (`rfc_docente`),
  UNIQUE KEY `uq_grupo_periodo_clave` (`periodo`, `clave_grupo`, `materia`, `rfc_docente`),
  CONSTRAINT `fk_grupos_materia` FOREIGN KEY (`materia`) REFERENCES `materias` (`materia`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_grupos_docente` FOREIGN KEY (`rfc_docente`) REFERENCES `personal` (`rfc`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_grupos_capacidad` CHECK (`capacidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. RELACIÓN MATERIAS-CARRERAS (estructura original conservada)
-- ============================================================
CREATE TABLE `materias_carreras` (
  `carrera` CHAR(3) NOT NULL,
  `reticula` INT NOT NULL,
  `materia` CHAR(7) NOT NULL,
  `semestre_reticula` INT NOT NULL,
  `renglon` INT DEFAULT NULL,
  PRIMARY KEY (`carrera`, `reticula`, `materia`),
  KEY `idx_materias_carreras_materia` (`materia`),
  CONSTRAINT `fk_mc_carrera` FOREIGN KEY (`carrera`, `reticula`)
    REFERENCES `carreras` (`carrera`, `reticula`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_mc_materia` FOREIGN KEY (`materia`) REFERENCES `materias` (`materia`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `chk_mc_semestre` CHECK (`semestre_reticula` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. INSCRIPCIONES A GRUPOS (tabla nueva)
-- Permite registrar qué alumnos cursan cada grupo y periodo.
-- ============================================================
CREATE TABLE `inscripciones_grupo` (
  `id_inscripcion` BIGINT NOT NULL AUTO_INCREMENT,
  `no_de_control` CHAR(10) NOT NULL,
  `id_grupo` INT NOT NULL,
  `fecha_inscripcion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estatus` ENUM('INSCRITO','BAJA','CONCLUIDO') NOT NULL DEFAULT 'INSCRITO',
  PRIMARY KEY (`id_inscripcion`),
  UNIQUE KEY `uq_inscripcion_alumno_grupo` (`no_de_control`, `id_grupo`),
  KEY `idx_inscripciones_grupo` (`id_grupo`),
  CONSTRAINT `fk_inscripciones_alumno` FOREIGN KEY (`no_de_control`)
    REFERENCES `alumnos` (`no_de_control`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_inscripciones_grupo` FOREIGN KEY (`id_grupo`)
    REFERENCES `grupos` (`id_grupo`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. CALIFICACIONES (campos originales conservados + nuevos)
-- ============================================================
CREATE TABLE `calificacion_evaluacion` (
  `id_calificacion` INT NOT NULL AUTO_INCREMENT,
  `no_de_control` CHAR(10) NOT NULL,
  `id_grupo` INT NOT NULL,
  `tipo_evaluacion` CHAR(2) NOT NULL,
  `calificacion` INT DEFAULT NULL,
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `observaciones` VARCHAR(500) DEFAULT NULL, -- NUEVO
  `registrada_por_rfc` CHAR(13) DEFAULT NULL, -- NUEVO
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- NUEVO
  PRIMARY KEY (`id_calificacion`),
  KEY `idx_calificaciones_alumno` (`no_de_control`),
  KEY `idx_calificaciones_grupo` (`id_grupo`),
  KEY `idx_calificaciones_registrada_por` (`registrada_por_rfc`),
  CONSTRAINT `fk_calificaciones_alumno` FOREIGN KEY (`no_de_control`)
    REFERENCES `alumnos` (`no_de_control`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_calificaciones_grupo` FOREIGN KEY (`id_grupo`)
    REFERENCES `grupos` (`id_grupo`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_calificaciones_docente` FOREIGN KEY (`registrada_por_rfc`)
    REFERENCES `personal` (`rfc`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `chk_calificaciones_rango` CHECK (`calificacion` IS NULL OR `calificacion` BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. USUARIOS (tabla nueva para acceso; no guardar contraseñas en texto plano)
-- `password_hash` debe recibir un hash generado por la aplicación,
-- por ejemplo mediante password_hash() de PHP.
-- ============================================================
CREATE TABLE `usuarios` (
  `id_usuario` BIGINT NOT NULL AUTO_INCREMENT,
  `nombre_usuario` VARCHAR(80) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `rol` ENUM('ALUMNO','DOCENTE','ADMINISTRADOR') NOT NULL,
  `no_de_control` CHAR(10) DEFAULT NULL,
  `rfc` CHAR(13) DEFAULT NULL,
  `estado` ENUM('ACTIVO','BLOQUEADO','PENDIENTE') NOT NULL DEFAULT 'PENDIENTE',
  `ultimo_acceso` DATETIME DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuarios_nombre` (`nombre_usuario`),
  UNIQUE KEY `uq_usuarios_alumno` (`no_de_control`),
  UNIQUE KEY `uq_usuarios_rfc` (`rfc`),
  KEY `idx_usuarios_rol_estado` (`rol`, `estado`),
  CONSTRAINT `fk_usuarios_alumno` FOREIGN KEY (`no_de_control`)
    REFERENCES `alumnos` (`no_de_control`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_usuarios_personal` FOREIGN KEY (`rfc`)
    REFERENCES `personal` (`rfc`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_usuario_un_sujeto` CHECK (
    (`no_de_control` IS NOT NULL AND `rfc` IS NULL AND `rol` = 'ALUMNO')
    OR (`no_de_control` IS NULL AND `rfc` IS NOT NULL AND `rol` IN ('DOCENTE','ADMINISTRADOR'))
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. BITÁCORA DE ACCESOS (tabla nueva para auditoría básica)
-- No guardar contraseñas, tokens ni datos sensibles en esta tabla.
-- ============================================================
CREATE TABLE `bitacora_accesos` (
  `id_evento` BIGINT NOT NULL AUTO_INCREMENT,
  `id_usuario` BIGINT DEFAULT NULL,
  `tipo_evento` ENUM('INICIO_SESION','CIERRE_SESION','ACCESO_DENEGADO','CAMBIO_CREDENCIALES') NOT NULL,
  `fecha_evento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_origen` VARCHAR(45) DEFAULT NULL,
  `detalle` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_evento`),
  KEY `idx_bitacora_usuario_fecha` (`id_usuario`, `fecha_evento`),
  CONSTRAINT `fk_bitacora_usuario` FOREIGN KEY (`id_usuario`)
    REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FIN DEL ESQUEMA
-- ============================================================
