-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-07-2025 a las 03:35:13
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
(1, 'Bienvenida Doug Bowles', 9, 'GRL', NULL, '2025-07-28', '10:15:00', '10:50:00', 'Gimnasio', 250, 250),
(2, 'Team Building in Motion', 9, 'TBM', NULL, '2025-07-28', '11:00:00', '11:45:00', 'Cancha Fut A', 250, 250),
(3, 'Pádel Sports Lab ', 1, 'PAD-1', 'lorem ipsum', '2025-07-28', '11:45:00', '12:30:00', 'Cancha pádel', 24, 4),
(4, 'Fútbol Sports Lab', 2, 'FUT-1', 'lorem ipsum', '2025-07-29', '11:45:00', '12:30:00', 'Cancha Fut A', 50, 2),
(5, 'Pickleball Sports Lab ', 3, 'PKB-1', 'lorem ipsum', '2025-07-30', '11:45:00', '12:30:00', 'Frontón A', 24, 4),
(6, 'Flag Football Sports Lab ', 4, 'FLF-1', 'lorem ipsum', '2025-07-31', '11:45:00', '12:30:00', 'Cancha Fut B', 30, 0),
(7, 'Running Distancia Sports Lab ', 5, 'RUN-1', 'lorem ipsum', '2025-08-01', '11:45:00', '12:30:00', 'Pista Atletismo', 30, 5),
(8, 'Marcha Sports Lab ', 6, 'MCH-1', 'lorem ipsum', '2025-08-02', '11:45:00', '12:30:00', 'Frontón B', 30, 5),
(9, 'Siclo', 7, 'SIC-1', 'lorem ipsum', '2025-08-03', '11:45:00', '12:30:00', 'Frontón Jai-Alai', 35, 1),
(10, 'Basquetbol Retas', 8, 'BSK-1', 'lorem ipsum', '2025-08-04', '11:45:00', '12:30:00', 'Básquet', 18, 5),
(11, 'Pádel Sports Lab ', 1, 'PAD-2', 'lorem ipsum', '2025-08-05', '12:30:00', '13:15:00', 'Cancha pádel', 24, 0),
(12, 'Fútbol Retas', 2, 'FUT-2', 'lorem ipsum', '2025-08-06', '12:30:00', '13:15:00', 'Cancha Fut A', 56, 4),
(13, 'Pickleball Retas', 3, 'PKB-2', 'lorem ipsum', '2025-08-07', '12:30:00', '13:15:00', 'Frontón A', 24, 2),
(14, 'Flag Football Sports Lab ', 4, 'FLF-2', 'lorem ipsum', '2025-08-08', '12:30:00', '13:15:00', 'Cancha Fut B', 30, 5),
(15, 'Running Velocidad Sports Lab ', 5, 'RUN-3', 'lorem ipsum', '2025-08-09', '12:30:00', '13:15:00', 'Pista Atletismo', 30, 5),
(16, 'Marcha Sports Lab ', 6, 'MCH-2', 'lorem ipsum', '2025-08-10', '12:30:00', '13:15:00', 'Frontón B', 30, 5),
(17, 'Siclo', 7, 'SIC-2', 'lorem ipsum', '2025-08-11', '12:30:00', '13:15:00', 'Frontón Jai-Alai', 35, 5),
(18, 'Basquetbol Retas', 8, 'BSK-2', 'lorem ipsum', '2025-08-12', '12:30:00', '13:15:00', 'Básquet', 18, 4),
(19, 'Pádel Sports Lab ', 1, 'PAD-3', 'lorem ipsum', '2025-08-13', '13:15:00', '14:00:00', 'Cancha pádel', 24, 5),
(20, 'Fútbol Torneo Fase 1', 2, 'FUT-3', 'lorem ipsum', '2025-08-14', '13:15:00', '14:00:00', 'Cancha Fut A', 28, 3),
(21, 'Pickleball Sports Lab', 3, 'PKB-3', 'lorem ipsum', '2025-08-15', '13:15:00', '14:00:00', 'Frontón A', 24, 5),
(22, 'Flag Football Retas', 4, 'FLF-3', 'lorem ipsum', '2025-08-16', '13:15:00', '14:00:00', 'Cancha Fut B', 30, 5),
(23, 'Siclo', 7, 'SIC-3', 'lorem ipsum', '2025-08-17', '13:15:00', '14:00:00', 'Frontón Jai-Alai', 35, 1),
(24, 'Basquetbol Retas', 8, 'BSK-3', 'lorem ipsum', '2025-08-18', '13:15:00', '14:00:00', 'Básquet', 18, 5),
(25, 'Pádel Torneo', 1, 'PAD-4', 'lorem ipsum', '2025-08-19', '14:00:00', '14:45:00', 'Cancha pádel', 24, 3),
(26, 'Fútbol Torneo Fase 2', 2, 'FUT-4', 'lorem ipsum', '2025-08-20', '14:00:00', '14:45:00', 'Cancha Fut A', 28, 4),
(27, 'Pickleball Retas', 3, 'PKB-4', 'lorem ipsum', '2025-08-21', '14:00:00', '14:45:00', 'Frontón A', 24, 4),
(28, 'Flag Football Retas', 4, 'FLF-4', 'lorem ipsum', '2025-08-22', '14:00:00', '14:45:00', 'Cancha Fut B', 30, 0),
(29, 'Siclo', 7, 'SIC-4', 'lorem ipsum', '2025-08-23', '14:00:00', '14:45:00', 'Frontón Jai-Alai', 35, 5),
(30, 'Basquetbol Retas', 8, 'BSK-4', 'lorem ipsum', '2025-08-24', '14:00:00', '14:45:00', 'Básquet', 18, 4);

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
(1, '2025-07-21 23:19:19', NULL),
(2, '2025-07-21 23:20:18', NULL),
(3, '2025-07-22 02:43:04', NULL),
(4, '2025-07-22 13:04:23', NULL),
(5, '2025-07-22 13:13:31', NULL),
(6, '2025-07-22 14:31:53', NULL),
(7, '2025-07-22 14:49:59', NULL),
(8, '2025-07-22 14:56:56', NULL),
(9, '2025-07-22 15:00:47', NULL),
(10, '2025-07-22 15:13:32', NULL),
(11, '2025-07-22 15:25:46', NULL),
(12, '2025-07-22 15:32:54', NULL),
(13, '2025-07-22 17:00:32', NULL),
(14, '2025-07-22 18:23:11', NULL);

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
(3, 21, 3, '2025-07-21 23:20:18', NULL, 'pendiente', 2),
(4, 21, 13, '2025-07-21 23:20:18', NULL, 'pendiente', 2),
(30, 26, 13, '2025-07-22 17:00:32', NULL, 'pendiente', 13),
(31, 26, 26, '2025-07-22 17:00:32', NULL, 'pendiente', 13),
(32, 1, 5, '2025-07-22 18:23:11', NULL, 'pendiente', 14);

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
(2, 'Regina', 'Rodriguez', '5536447745', 'regina@gmail.com', '123456788', '$2y$10$Gcw1qAVS7qsFfMeZxxnFjOoWniy9SuU5t4RgOmOH9U3PGxrdQuZpO', '2025-07-17 00:07:38', 'user', 0),
(3, 'Nayeli', 'Garcia', '5536447745', 'naye@gmail.com', '147852', '$2y$10$lvoJ017lPXvM4Ta642gM/OZMqeh3TChiSr167PpcD1t64uCr8R6QW', '2025-07-17 01:31:54', 'user', 0),
(4, 'Sofia', 'Rodriguez', '5536479845', 'sofia@gmail.com', '123456', '$2y$10$u9lcLzGNvQGOmmi9UGTEauy5vV.bNwFUerUWZgcH7DBNBWJ10oRdq', '2025-07-17 19:58:01', 'user', 0),
(7, 'Elizabeth', 'Rodiguez cuauhizo', '55659865689', 'cuauhizo@gmail.com', '15457878', '$2y$10$1C2KuLaJTbt720E5IgSvROr5MEbL0d9SR2e.48Fcstk3X8Mo1DJjO', '2025-07-19 18:17:16', 'user', 1),
(8, 'Felipa', 'Cuauhizo Neri', '5536447845', 'felipa@gmail.com', '148526', '$2y$10$s98RBLLvcPt4goqG9ofQ3uhNtx8HdUl73P2rGjw0siVSHCJgcdpDG', '2025-07-19 18:33:26', 'user', 1),
(9, 'karen', 'lopez peralta', '5536598998', 'karen@nike.com', '789452', '$2y$10$eQ3X5CWxI594k0sFtg3TAOA1nkHPO0ZNJ63bneOThcfvDd0hzdUIm', '2025-07-19 18:43:33', 'user', 1),
(10, 'María', 'Medina lópez', '5535659598898965656', 'tolko361@nike.com', '6545421', '$2y$10$b1G5Z3rBGQNnyArA0ilWju2YjhIW5NbV5yeoZqrzpguew42PBT0SS', '2025-07-19 19:46:17', 'user', 1),
(11, 'Miguel', 'acebeso lópez', '555555555', 'cuauhizo@nike.com', 'DFG543453', '$2y$10$K9oj232wgFJKvFGCdZ.qOu6Cv.e/ilSVjZySsCuVhmHm9XP4DTrKi', '2025-07-21 15:36:25', 'user', 1),
(12, 'Luis', 'lopez', '553656565', 'cuauhizo2@nike.com', 'EWFE345345', '$2y$10$VNOqFYhdR12W1cFopDDLjOCFd20HDau.RsVUdoW1am3oT1/Dtdrki', '2025-07-21 15:38:16', 'user', 1),
(26, 'Sofia', 'Rodriguez Canchola', '5536449576', 'tolko360@tolkogroup.com', '45565EWR', '', '2025-07-22 19:11:37', 'user', 0);

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
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `grupos_reservas`
--
ALTER TABLE `grupos_reservas`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id_reservacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
