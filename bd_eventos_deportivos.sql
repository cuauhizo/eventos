-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2025 a las 19:40:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_eventos_deportivos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`) VALUES
(8, 'Basquetbol Retas'),
(4, 'Flag Football Sports Lab '),
(2, 'Fútbol Sports Lab'),
(9, 'General'),
(6, 'Marcha Sports Lab '),
(1, 'Pádel Sports Lab '),
(3, 'Pickleball Sports Lab '),
(5, 'Running Distancia Sports Lab '),
(7, 'Siclo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` int(11) NOT NULL,
  `nombre_evento` varchar(255) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo_evento` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `cupo_disponible` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `nombre_evento`, `id_categoria`, `codigo_evento`, `descripcion`, `fecha`, `hora_inicio`, `hora_fin`, `ubicacion`, `cupo_maximo`, `cupo_disponible`) VALUES
(1, 'Bienvenida Doug Bowles', 9, 'GRL', 'Comenzaremos nuestro JDI Day con una bienvenida de parte de nuestro VP/GM,\nDoug Bowles seguida de nuestra Ceremonia de Reconomiento de Maxims.', '2025-07-28', '10:15:00', '10:50:00', 'Gimnasio', 250, 225),
(2, 'Team Building in Motion', 9, 'TBM', 'Sesión de calentamiento y construcción de equipo liderada por el Nike Trainer Gabriel Rojo de la Vega.', '2025-07-28', '11:00:00', '11:45:00', 'Cancha Fut A', 250, 225),
(3, 'Pádel Sports Lab ', 1, 'PAD-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Canchas Pádel 1,2 y 3', 18, 4),
(4, 'Fútbol Retas', 2, 'FUT-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Cancha Futbol A', 64, 0),
(5, 'Pickleball Sports Lab ', 3, 'PKB-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Frontón B', 12, 0),
(6, 'Flag Football Sports Lab ', 4, 'FLF-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Cancha Futbol B', 34, 0),
(7, 'Marcha Sports Lab ', 6, 'MCH-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Frontón trasero y pista de arcilla', 36, 0),
(8, 'Siclo', 7, 'SIC-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Frontón Jai-Alai', 35, 4),
(9, 'Basquetbol Retas', 8, 'BSK-1', NULL, '2025-07-28', '11:45:00', '12:30:00', 'Área Triángulo Básquet', 24, 2),
(10, 'Pádel Sports Lab ', 1, 'PAD-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Canchas Pádel 1,2 y 3', 18, 2),
(11, 'Fútbol Torneo Fase 1', 2, 'FUT-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Cancha Futbol A', 28, 0),
(12, 'Pickleball Sports Lab ', 3, 'PKB-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Frontón B', 12, 4),
(13, 'Flag Football Sports Lab ', 4, 'FLF-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Cancha Futbol B', 34, 0),
(14, 'Running Sports Lab ', 5, 'RUN-1', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Milla', 32, 0),
(15, 'Siclo', 7, 'SIC-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Frontón Jai-Alai', 35, 4),
(16, 'Basquetbol Retas', 8, 'BSK-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Área Triángulo Básquet', 24, 0),
(17, 'Pádel Sports Lab ', 1, 'PAD-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Canchas Pádel 1,2 y 3', 18, 3),
(18, 'Fútbol Torneo Fase 2', 2, 'FUT-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Cancha Futbol A', 28, 0),
(19, 'Pickleball Sports Lab', 3, 'PKB-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Frontón B', 12, 4),
(20, 'Flag Football Retas', 4, 'FLF-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Cancha Futbol B', 34, 0),
(21, 'Siclo', 7, 'SIC-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Frontón Jai-Alai', 35, 1),
(22, 'Running Sports Lab ', 9, 'RUN-2', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Milla', 32, 0),
(23, 'Basquetbol Retas', 8, 'BSK-3', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Área Triángulo Básquet', 24, 4),
(24, 'Movement and Stretching', 9, 'NIK-1', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Cancha Basquetbol', 50, 0),
(25, 'Pádel Torneo', 1, 'PAD-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Canchas Pádel 1,2 y 3', 36, 2),
(26, 'Fútbol Retas', 2, 'FUT-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Cancha Futbol A', 64, 1),
(27, 'Pickleball Sports Lab', 3, 'PKB-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Frontón B', 12, 3),
(28, 'Flag Football Retas', 4, 'FLF-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Cancha Futbol B', 34, 0),
(29, 'Siclo', 7, 'SIC-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Frontón Jai-Alai', 35, 1),
(30, 'Basquetbol Retas', 8, 'BSK-4', NULL, '2025-07-28', '14:00:00', '14:45:00', 'Área Triángulo Básquet', 24, 0),
(31, 'Marcha Sports Lab ', 6, 'MCH-2', NULL, '2025-07-28', '12:30:00', '13:15:00', 'Frontón trasero y pista de arcilla', 36, 0),
(32, 'Baño de Gong', 9, 'NIK-2', NULL, '2025-07-28', '13:15:00', '14:00:00', 'Cancha Basquetbol', 50, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_reservas`
--

CREATE TABLE `grupos_reservas` (
  `id_grupo` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos_reservas`
--

INSERT INTO `grupos_reservas` (`id_grupo`, `fecha_creacion`, `qr_code`) VALUES
(1, '2025-07-23 13:14:40', 'qrcodes/grupo_reserva_1.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservaciones`
--

CREATE TABLE `reservaciones` (
  `id_reservacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `fecha_reservacion` datetime DEFAULT current_timestamp(),
  `qr_code` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada','usada') DEFAULT 'pendiente',
  `id_grupo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservaciones`
--

INSERT INTO `reservaciones` (`id_reservacion`, `id_usuario`, `id_evento`, `fecha_reservacion`, `qr_code`, `estado`, `id_grupo`) VALUES
(1, 36, 1, '2025-07-23 13:14:40', NULL, 'confirmada', 1),
(2, 36, 2, '2025-07-23 13:14:40', NULL, 'confirmada', 1),
(3, 36, 8, '2025-07-23 13:14:40', NULL, 'confirmada', 1),
(4, 36, 10, '2025-07-23 13:14:40', NULL, 'confirmada', 1),
(5, 36, 17, '2025-07-23 13:14:40', NULL, 'confirmada', 1),
(6, 36, 27, '2025-07-23 13:14:40', NULL, 'confirmada', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `id_empleado` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('user','admin') NOT NULL DEFAULT 'user',
  `acepta_contacto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellidos`, `telefono`, `correo`, `id_empleado`, `password`, `fecha_registro`, `rol`, `acepta_contacto`) VALUES
(1, 'Fabian', 'Rodriguez', '5536447745', 'frodriguez@tolkogroup.com', '127', '$2y$10$Rmi55dEiB/jDrHMGG9soHOlQAEuhG0sdxMzo8Gh6zD2o4VwPfn4Ay', '2025-07-16 23:52:49', 'admin', 0),
(31, 'Sofia', 'Rodriguez Canchola', '5536655985', 'tolko361@tolkogroup.com', '12656445', '', '2025-07-23 17:20:11', 'user', 0),
(32, 'Regina', 'Rodriguez Canchola', '5536449598', 'tolko362@tolkogroup.com', '65265', '', '2025-07-23 17:27:00', 'user', 0),
(33, 'Sofia', 'Rodriguez Canchola', '', 'sofia@nike.com', '5152156545', '', '2025-07-23 18:47:30', 'user', 0),
(34, 'María', 'Medina Pérez', '', 'maria@nike.com', '5413421564', '', '2025-07-23 18:53:32', 'user', 0),
(35, 'Nayeli', 'Canchola Garcia', '', 'tolko360@tolkogroup.com', '5642165415', '', '2025-07-23 18:59:02', 'user', 0),
(36, 'Fabian', 'Rodiguez cuauhizo', '', 'cuauhizo@gmail.com', '874541541', '', '2025-07-23 19:14:05', 'user', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD UNIQUE KEY `fecha` (`fecha`,`hora_inicio`,`nombre_evento`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `grupos_reservas`
--
ALTER TABLE `grupos_reservas`
  ADD PRIMARY KEY (`id_grupo`);

--
-- Indices de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD PRIMARY KEY (`id_reservacion`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_evento`),
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `id_grupo` (`id_grupo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `id_empleado` (`id_empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `grupos_reservas`
--
ALTER TABLE `grupos_reservas`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id_reservacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD CONSTRAINT `reservaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservaciones_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservaciones_ibfk_3` FOREIGN KEY (`id_grupo`) REFERENCES `grupos_reservas` (`id_grupo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
