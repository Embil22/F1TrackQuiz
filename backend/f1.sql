-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Ápr 26. 23:35
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `f1_quiz`
--
CREATE DATABASE IF NOT EXISTS `f1_quiz` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `f1_quiz`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score_percent` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_answers` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `score_percent`, `total_questions`, `correct_answers`, `completed_at`) VALUES
(1, 1, 0, 24, 0, '2026-04-26 20:37:03'),
(2, 1, 0, 24, 0, '2026-04-26 20:37:23'),
(3, 1, 0, 24, 0, '2026-04-26 20:37:39'),
(4, 1, 0, 24, 0, '2026-04-26 20:49:18'),
(5, 1, 0, 24, 0, '2026-04-26 20:51:16'),
(6, 2, 4, 24, 1, '2026-04-26 21:13:13'),
(7, 2, 0, 24, 0, '2026-04-26 21:19:04'),
(8, 3, 96, 24, 23, '2026-04-26 21:34:18');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tracks`
--

CREATE TABLE `tracks` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `tracks`
--

INSERT INTO `tracks` (`id`, `name`, `country`, `image_url`, `created_at`) VALUES
(1, 'Bahrain International Circuit', 'Bahrain', '../backend/uploads/bahrain.jpg', '2026-04-26 20:27:06'),
(2, 'Jeddah Corniche Circuit', 'Saudi Arabia', '../backend/uploads/jeddah.jpg', '2026-04-26 20:27:06'),
(3, 'Australia Albert Park Circuit', 'Australia', '../backend/uploads/australia.jpg', '2026-04-26 20:27:06'),
(4, 'Japan Suzuka International Racing Course', 'Japan', '../backend/uploads/japan.jpg', '2026-04-26 20:27:06'),
(5, 'China Shanghai International Circuit', 'China', '../backend/uploads/china.jpg', '2026-04-26 20:27:06'),
(6, 'USA Miami International Autodrome', 'USA', '../backend/uploads/miami.jpg', '2026-04-26 20:27:06'),
(7, 'Italy Autodromo Enzo e Dino Ferrari (Imola)', 'Italy', '../backend/uploads/imola.jpg', '2026-04-26 20:27:06'),
(8, 'Circuit de Monaco', 'Monaco', '../backend/uploads/monaco.jpg', '2026-04-26 20:27:06'),
(9, 'Spain Circuit de Barcelona-Catalunya', 'Spain', '../backend/uploads/spain.jpg', '2026-04-26 20:27:06'),
(10, 'Canada Circuit Gilles Villeneuve', 'Canada', '../backend/uploads/canada.jpg', '2026-04-26 20:27:06'),
(11, 'Red Bull Ring Austria', 'Austria', '../backend/uploads/austria.jpg', '2026-04-26 20:27:06'),
(12, 'Britain Silverstone Circuit', 'United Kingdom', '../backend/uploads/silverstone.jpg', '2026-04-26 20:27:06'),
(13, 'Hungaroring', 'Hungary', '../backend/uploads/hungaroring.jpg', '2026-04-26 20:27:06'),
(14, 'Belgium Circuit de Spa-Francorchamps', 'Belgium', '../backend/uploads/spa.jpg', '2026-04-26 20:27:06'),
(15, 'Netherlands Circuit Zandvoort', 'Netherlands', '../backend/uploads/zandvoort.jpg', '2026-04-26 20:27:06'),
(16, 'Italy Autodromo Nazionale Monza', 'Italy', '../backend/uploads/monza.jpg', '2026-04-26 20:27:06'),
(17, 'Azerbaijan Baku City Circuit', 'Azerbaijan', '../backend/uploads/baku.jpg', '2026-04-26 20:27:06'),
(18, 'Singapore Marina Bay Street Circuit', 'Singapore', '../backend/uploads/singapore.jpg', '2026-04-26 20:27:06'),
(19, 'USA Circuit of the Americas', 'USA', '../backend/uploads/cota.jpg', '2026-04-26 20:27:06'),
(20, 'Mexico Autódromo Hermanos Rodríguez', 'Mexico', '../backend/uploads/mexico.jpg', '2026-04-26 20:27:06'),
(21, 'Brazil Sao Paulo Interlagos Circuit', 'Brazil', '../backend/uploads/interlagos.jpg', '2026-04-26 20:27:06'),
(22, 'USA Las Vegas Strip Circuit', 'USA', '../backend/uploads/las_vegas.jpg', '2026-04-26 20:27:06'),
(23, 'Qatar Losail International Circuit', 'Qatar', '../backend/uploads/losail.jpg', '2026-04-26 20:27:06'),
(24, 'UAE Yas Marina Circuit', 'UAE', '../backend/uploads/yas_marina.jpg', '2026-04-26 20:27:06');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Embil22', 'balazsmartin2008@gmail.com', '$2y$10$hIhdwKlG5l48V/HFl8f4tubr/t4uRP4/gNXjLLkJY/ZVCtLgq4ssS', '2026-04-26 20:36:19'),
(2, '123', 'balazsmartin20080@gmail.com', '$2y$10$m4CNkdXjcn1RQnG7x1OXLOeMxS8kQnV4MMloBttm.xuD7LglnMxky', '2026-04-26 20:56:19'),
(3, 'admin', 'admin@gmail.com', '$2y$10$aK5G.bFAzm73zihVT5UIMuBX6/Lx.rzzNqevpW0OnjC7QP.uR5DLK', '2026-04-26 21:25:03');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT a táblához `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
