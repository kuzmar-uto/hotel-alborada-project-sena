-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2026 a las 15:33:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alborada`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `id_habitacion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `caracteristicas` int(11) NOT NULL,
  `tipo_habitaciones` varchar(500) NOT NULL,
  `habitaciones_disponibles` int(11) DEFAULT 0,
  `max_habitaciones` int(254) NOT NULL,
  `tipo_de_habitacion` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id_habitacion`, `nombre`, `descripcion`, `precio`, `imagen`, `caracteristicas`, `tipo_habitaciones`, `habitaciones_disponibles`, `max_habitaciones`, `tipo_de_habitacion`) VALUES
(1, 'ddddd', 'dddddd', 5000.00, 'uploads/1771337748_Captura de pantalla 2026-01-22 214225.png', 5, '', 4, 5, ''),
(2, 'HEISENBERG', 'ton', 3000.00, 'uploads/1770217652_Walter_White_S5B.png', 7, '', 0, 0, ''),
(3, 'aegis', 'sa', 300.00, 'uploads/1770218445_XY1_ES_86.png', 0, '', 0, 0, ''),
(4, 'jf,', 'ukgu,kg', 456.00, 'uploads/1770221067_WhatsApp_Image_2025-09-25_at_9.57.48_AM-removebg-preview.png', 5, '', 10, 60, ''),
(5, 'ljsncalk', 'askcaslkc', 227.00, 'uploads/1770645864_PicsArt_06-02.06.2367.png', 5, '', 10, 299, ''),
(6, 'ooooo', 'jjjjj', 98900.00, 'uploads/1771336870_Captura de pantalla 2025-10-23 193255.png', 0, '', 3, 7, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id_habitacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id_habitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
