-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2026 a las 15:33:55
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
-- Estructura de tabla para la tabla `usuarios_alborada`
--

CREATE TABLE `usuarios_alborada` (
  `id` int(11) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Nombre` varchar(255) DEFAULT NULL,
  `Telefono` varchar(50) DEFAULT NULL,
  `addmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_alborada`
--

INSERT INTO `usuarios_alborada` (`id`, `Correo`, `Contraseña`, `Nombre`, `Telefono`, `addmin`) VALUES
(1, 'juan@ejemplo.com', '1234', NULL, NULL, 0),
(2, 'kuzmarpolo9@gmail.com', '$2y$10$Hex9RVc44qJ3JADSSyHRpONNrB/43Yr.iGxYBPi5Mdsd6zbbpRLv6', NULL, NULL, 0),
(3, 'jhiijij@gmail.com', '$2y$10$zA11cuJm9aPrgFS3sJNeTORnf9w022yPcn9n84y3jLVcrM2d.SIpi', NULL, NULL, 0),
(4, 'adriansantiagoninobeltran@gmail.com', '$2y$10$zf7HP5OdluVT1LSbh9ZUau1MN/stFV1EUVLvLEPI500u6q2/dymcq', 'Santiago Nino Beltran', '3125089170', 0),
(5, 'adriran@gmail.com', '$2y$10$5VEKpLZSdzttXPPmFDuDK.bzz/5C6iLIyOlnqDKmOC4VACNVsgJ0u', 'jjj', '3125079170', 0),
(6, 'jola@gmail.com', '$2y$10$mk5UMoSqGPrOEJvXvFJfF.Bx0QiOLfLrXyFv/At48w/Mb6FFmVn/C', 'kkkk', '3125089177', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios_alborada`
--
ALTER TABLE `usuarios_alborada`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios_alborada`
--
ALTER TABLE `usuarios_alborada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
