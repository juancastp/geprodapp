-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2024 a las 13:01:03
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
-- Base de datos: `gluttiere`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `id` int(11) NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `referencia_entrada` varchar(100) NOT NULL,
  `articulo` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fecha_entrada` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`id`, `proveedor`, `referencia_entrada`, `articulo`, `cantidad`, `peso`, `lote`, `fecha_entrada`) VALUES
(1, 'Huevos pariente', 'Alb-3589', 'Azucar la azucarera', 2, 1000.00, '7896582', '2024-05-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes_recetas`
--

CREATE TABLE `ingredientes_recetas` (
  `id` int(11) NOT NULL,
  `receta_id` int(11) NOT NULL,
  `nombre_ingrediente` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingredientes_recetas`
--

INSERT INTO `ingredientes_recetas` (`id`, `receta_id`, `nombre_ingrediente`) VALUES
(1, 1, 'palmeras'),
(2, 1, 'kinder'),
(3, 1, 'chocolate con leche'),
(4, 2, 'caracolas'),
(5, 2, 'chcolate negro'),
(6, 2, 'crocanti'),
(7, 3, 'canolis'),
(8, 3, 'lotus'),
(9, 4, 'croisant'),
(10, 4, 'crema lotus'),
(11, 4, 'galleta lotus');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_ingredientes_usados`
--

CREATE TABLE `lotes_ingredientes_usados` (
  `id` int(11) NOT NULL,
  `produccion_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `lote` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotes_ingredientes_usados`
--

INSERT INTO `lotes_ingredientes_usados` (`id`, `produccion_id`, `ingrediente_id`, `lote`) VALUES
(1, 3, 4, '986532'),
(2, 3, 5, '963'),
(3, 3, 6, '258'),
(4, 4, 1, '123456789'),
(5, 4, 2, '65498731'),
(6, 4, 3, '753951'),
(7, 5, 4, '986532'),
(8, 5, 5, '8794654321'),
(9, 5, 6, '98765464');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion`
--

CREATE TABLE `produccion` (
  `id` int(11) NOT NULL,
  `receta_id` int(11) NOT NULL,
  `lote_produccion` varchar(100) NOT NULL,
  `fecha_produccion` date NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `produccion`
--

INSERT INTO `produccion` (`id`, `receta_id`, `lote_produccion`, `fecha_produccion`, `cantidad`) VALUES
(1, 1, '789456', '2024-05-28', 16),
(2, 3, '789456', '2024-05-28', 12),
(3, 2, '741', '2024-05-28', 25),
(4, 1, '159753', '2024-05-28', 24),
(5, 2, '898752465498', '2024-05-29', 32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

CREATE TABLE `recetas` (
  `id` int(11) NOT NULL,
  `nombre_producto_final` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id`, `nombre_producto_final`) VALUES
(1, 'Palmera Kinder'),
(2, 'caracolas'),
(3, 'canolis lotus'),
(4, 'croissant lotus');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `usuario`, `contrasena`) VALUES
(1, 'Juan Carlos Romero', 'juanca', '$2y$10$pQbLM6izEGeOZXCM7UYPA.FLofrdtQxH1Wi2YmYCIQSR4BfmrpG9O');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingredientes_recetas`
--
ALTER TABLE `ingredientes_recetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receta_id` (`receta_id`);

--
-- Indices de la tabla `lotes_ingredientes_usados`
--
ALTER TABLE `lotes_ingredientes_usados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produccion_id` (`produccion_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indices de la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receta_id` (`receta_id`);

--
-- Indices de la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ingredientes_recetas`
--
ALTER TABLE `ingredientes_recetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `lotes_ingredientes_usados`
--
ALTER TABLE `lotes_ingredientes_usados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `produccion`
--
ALTER TABLE `produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recetas`
--
ALTER TABLE `recetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ingredientes_recetas`
--
ALTER TABLE `ingredientes_recetas`
  ADD CONSTRAINT `ingredientes_recetas_ibfk_1` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`);

--
-- Filtros para la tabla `lotes_ingredientes_usados`
--
ALTER TABLE `lotes_ingredientes_usados`
  ADD CONSTRAINT `lotes_ingredientes_usados_ibfk_1` FOREIGN KEY (`produccion_id`) REFERENCES `produccion` (`id`),
  ADD CONSTRAINT `lotes_ingredientes_usados_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes_recetas` (`id`);

--
-- Filtros para la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD CONSTRAINT `produccion_ibfk_1` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
