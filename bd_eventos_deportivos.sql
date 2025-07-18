-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2025 a las 23:14:35
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
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` int(11) NOT NULL,
  `nombre_evento` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `cupo_disponible` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `nombre_evento`, `descripcion`, `fecha`, `hora_inicio`, `hora_fin`, `cupo_maximo`, `cupo_disponible`) VALUES
(1, 'Partido de Fútbol', 'Disfruta de un emocionante partido de fútbol local.', '2025-07-25', '18:00:00', '20:00:00', 4, 7),
(2, 'Carrera 5K', 'Participa en nuestra carrera de 5 kilómetros.', '2025-08-01', '08:00:00', '09:00:00', 4, 7),
(3, 'Clase de Yoga', 'Sesión de yoga para principiantes y avanzados.', '2025-08-05', '09:30:00', '10:30:00', 4, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_reservas`
--

CREATE TABLE `grupos_reservas` (
  `id_grupo` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `rol` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellidos`, `telefono`, `correo`, `id_empleado`, `password`, `fecha_registro`, `rol`) VALUES
(1, 'Fabian', 'Rodriguez', '5536447745', 'frodriguez@tolkogroup.com', '127', '$2y$10$Rmi55dEiB/jDrHMGG9soHOlQAEuhG0sdxMzo8Gh6zD2o4VwPfn4Ay', '2025-07-16 23:52:49', 'admin'),
(2, 'Regina', 'Rodriguez', '5536447745', 'regina@gmail.com', '123456788', '$2y$10$Gcw1qAVS7qsFfMeZxxnFjOoWniy9SuU5t4RgOmOH9U3PGxrdQuZpO', '2025-07-17 00:07:38', 'user'),
(3, 'Nayeli', 'Garcia', '5536447745', 'naye@gmail.com', '147852', '$2y$10$lvoJ017lPXvM4Ta642gM/OZMqeh3TChiSr167PpcD1t64uCr8R6QW', '2025-07-17 01:31:54', 'user'),
(4, 'Sofia', 'Rodriguez', '5536479845', 'sofia@gmail.com', '123456', '$2y$10$u9lcLzGNvQGOmmi9UGTEauy5vV.bNwFUerUWZgcH7DBNBWJ10oRdq', '2025-07-17 19:58:01', 'user'),
(5, 'Tolko', 'Group', '5536445257', 'tolko360@tolkogroup.com', '789456', '$2y$10$RWJKXmdCSDQ6khMe3X8vRekHdM3oowv2pLGChjMdH1s1UtUiW2Mcq', '2025-07-17 23:33:40', 'user'),
(6, 'Alfredo', 'Gutiérrez Bayardi', '5611098155', 'bsalgado@tolkogroup.com', '1357', '$2y$10$DzPjByn9idPaw67Xo29sNezLj6x4XOT38AWQVQP2NIGsm2O1Dytea', '2025-07-18 19:07:52', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD UNIQUE KEY `fecha` (`fecha`,`hora_inicio`,`nombre_evento`);

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
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `grupos_reservas`
--
ALTER TABLE `grupos_reservas`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id_reservacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
