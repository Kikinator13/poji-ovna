-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 24. lis 2022, 16:11
-- Verze serveru: 10.4.25-MariaDB
-- Verze PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `insurance_company`
--
CREATE DATABASE IF NOT EXISTS `insurance_company` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `insurance_company`;

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_address` (IN `pstreet` VARCHAR(255), IN `pbuilding_identification_number` INT, IN `phouse_number` INT, IN `pZIP` INT, IN `pcity` VARCHAR(255), OUT `id` INT)   BEGIN
    SELECT addresses_id INTO id 
    	FROM addresses 
        WHERE 
              street = pstreet and
              building_identification_number = pbuilding_identification_number and
              house_number = phouse_number AND
              ZIP = pZIP AND
              city = pcity;
    IF(id IS NULL) THEN
        INSERT INTO addresses (street, building_identification_number, house_number, ZIP, city) 			VALUES (pstreet, pbuilding_identification_number, phouse_number, pZIP, pcity);
        SET id=LAST_INSERT_ID();
   END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabulky `addresses`
--

CREATE TABLE `addresses` (
  `addresses_id` int(11) NOT NULL,
  `street` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `building_identification_number` int(11) NOT NULL,
  `house_number` int(11) NOT NULL,
  `ZIP` int(11) NOT NULL,
  `city` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `addresses`
--

INSERT INTO `addresses` (`addresses_id`, `street`, `building_identification_number`, `house_number`, `ZIP`, `city`) VALUES
(33, 'Hornoměcholupská', 128, 1, 14000, 'Praha 4'),
(34, 'Lipanská', 25, 4, 13000, 'Praha'),
(35, 'Šumavská', 45, 5, 12000, 'Praha 2'),
(36, 'Falešný', 123, 4, 14000, 'Petrovice'),
(37, 'Horáčkova', 1235, 85, 14000, 'Praha 4');

-- --------------------------------------------------------

--
-- Struktura tabulky `articles`
--

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `key_words` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `author` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `persons`
--

CREATE TABLE `persons` (
  `persons_id` int(11) NOT NULL,
  `first_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `last_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `address` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `identity_card_number` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `national_id_number` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `persons`
--

INSERT INTO `persons` (`persons_id`, `first_name`, `last_name`, `date_of_birth`, `phone`, `mail`, `address`, `user`, `identity_card_number`, `national_id_number`) VALUES
(12, 'Martin', 'Kikta', '1990-06-18', '+420735614776', 'martin@kikta.cz', 33, 58, '468483186', 9006180051),
(13, 'Jan', 'Novák', '1973-07-15', '+420546846846', 'Honza.n@seznam.cz', 34, 59, '546848945', 7307156468),
(14, 'Karel', 'Hošek', '1985-01-24', '+420854648648', 'karel@centrum.cz', 35, 60, '566868AD64', 8501245648),
(15, 'Pepa', 'Zdepa', '2004-06-18', '+420648618618', 'pepa@gmail.com', 36, 61, '789258654', 5012315164),
(16, 'Dan', 'Petrák', '2000-06-18', '+420648651864', 'danp@seznam.cz', 37, 62, '222313585', 18066456);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `user` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`users_id`, `user`, `password`, `admin`) VALUES
(58, 'martin', '$2y$10$wUYwBDbRBtfLLDx3TyoKFOpHSnKlQdU0y5IhObwe2Mrchraohd1aW', 1),
(59, 'novako', '$2y$10$F.jMjnL89NLYtUTnr.myxeklHbsehopZ8TIrVI5Um5q7EomOPktCS', 0),
(60, 'karlos', '$2y$10$/1F0a6y7CQ8PAYj1o41BVuXDJDI6OqVOwnqyfVBqSjZwOjuP0aOQu', 0),
(61, 'pepa', '$2y$10$wnv2a8rV.P/aHFBg.MNTreXiSbnav3WKvB251JNClxDSXqJEEbU3e', 0),
(62, 'danp', '$2y$10$E.2wtUwl7aZvobz57AUzQuvOHeQUzCw8Wzwz1JOJ1YNHCo9PBDMge', 0);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`addresses_id`);

--
-- Indexy pro tabulku `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`);

--
-- Indexy pro tabulku `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`persons_id`),
  ADD KEY `addresses` (`address`),
  ADD KEY `users` (`user`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `addresses`
--
ALTER TABLE `addresses`
  MODIFY `addresses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pro tabulku `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `persons`
--
ALTER TABLE `persons`
  MODIFY `persons_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `persons`
--
ALTER TABLE `persons`
  ADD CONSTRAINT `addresses` FOREIGN KEY (`address`) REFERENCES `addresses` (`addresses_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users` FOREIGN KEY (`user`) REFERENCES `users` (`users_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
