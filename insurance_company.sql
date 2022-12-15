-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Stř 14. pro 2022, 23:33
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_address` (IN `pstreet` VARCHAR(255), IN `pZIP` VARCHAR(5), IN `pcity` VARCHAR(255), IN `pstate` INT, OUT `id` INT)   BEGIN
    SELECT addresses_id INTO id 
    	FROM addresses 
        WHERE 
              street_and_number = pstreet and
              ZIP = pZIP AND
              city = pcity AND
              state = pstate;
    IF(id IS NULL) THEN
        INSERT INTO addresses (street_and_number, ZIP, city, state) 			
        VALUES (pstreet, pZIP, pcity, pstate);
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
  `street_and_number` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `ZIP` varchar(5) COLLATE utf8_czech_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `addresses`
--

INSERT INTO `addresses` (`addresses_id`, `street_and_number`, `ZIP`, `city`, `state`) VALUES
(365, 'Budějovická 4', '14000', 'Praha 4', 0),
(371, 'Hvězdová 6', '14000', '14000', 0),
(374, 'Moyzesova 972/10', '01001', 'Žilina', 4),
(380, 'Bartákova 10', '14000', 'Praha 4', 0),
(381, 'Ruská 1', '12000', 'Praha 2', 0),
(382, 'Plzeňská 3', '15000', 'Praha 5', 0),
(383, 'Jagelonská 5', '13000', 'Praha 3', 0),
(387, 'Jeremenkova 20', '14000', 'Praha 4', 0),
(410, 'Horáčkova 1211/19', '14000', 'Praha 4', 0),
(411, 'Lipanská 2', '13000', 'Praha 3', 0),
(412, 'Na pankráci 5', '14000', 'Praha 4', 0),
(413, 'Šumavská 4', '12000', 'Praha 2', 0),
(414, 'Krymská', '12000', 'Praha 2', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `area_code`
--

CREATE TABLE `area_code` (
  `area_code_id` int(11) NOT NULL,
  `area_code` varchar(6) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `area_code`
--

INSERT INTO `area_code` (`area_code_id`, `area_code`) VALUES
(1, '+420'),
(2, '+421');

-- --------------------------------------------------------

--
-- Struktura tabulky `contact`
--

CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL,
  `area_code` int(11) NOT NULL,
  `phone` varchar(9) COLLATE utf8_czech_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `contact`
--

INSERT INTO `contact` (`contact_id`, `area_code`, `phone`, `mail`) VALUES
(10, 1, '735614777', 'martin@kikta.com'),
(11, 1, '525852654', 'lojzah@gmail.com'),
(13, 2, '000123456', 'Jozo@zoznam.cz'),
(16, 1, '123585128', 'alojz@seznam.cz'),
(18, 1, '222456654', 'jarekk@seznam.cz'),
(22, 1, '258897656', 'Jarda@zizka.cz'),
(24, 1, '852465879', 'kratochvil@gmail.com'),
(25, 1, '123456789', 'alenav@seznam.cz'),
(26, 1, '123852658', 'alsme@gmail.com'),
(27, 1, '456852123', 'vojtek@gmail.com'),
(28, 1, '52585264', 'broza@seznam.cz'),
(29, 1, '123525856', 'karelh@centrum.cz'),
(30, 1, '123523523', 'michaela.absolonova@seznam.cz');

-- --------------------------------------------------------

--
-- Struktura tabulky `persons`
--

CREATE TABLE `persons` (
  `persons_id` int(11) NOT NULL,
  `first_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `last_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `contact` int(11) NOT NULL,
  `address` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `identity_card_number` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `national_id_number` varchar(10) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `persons`
--

INSERT INTO `persons` (`persons_id`, `first_name`, `last_name`, `date_of_birth`, `contact`, `address`, `user`, `identity_card_number`, `national_id_number`) VALUES
(131, 'Jan', 'Bošek', '1984-05-18', 11, 371, 196, '147741Ab45', '8405185545'),
(133, 'Dežo', 'Harmanovský', '2001-06-18', 13, 374, 198, '123456789', '0106184565'),
(135, 'Martin', 'Kikta', '1990-06-18', 10, 410, 201, '485258569', '9006184428'),
(136, 'Jiří', 'Mayer', '1983-06-18', 16, 387, 202, '111222333', '8306181235'),
(138, 'Jarek', 'Krásný', '1993-01-15', 18, 365, 207, '222222333', '9315015555'),
(142, 'Jaroslav', 'Žižka', '2002-08-12', 22, 411, 204, '228665556', '0208125531'),
(144, 'Jozef', 'Kratochvíl', '2003-12-18', 24, 380, 214, '879213585', '0312184565'),
(145, 'Alice', 'Vránová', '1998-10-14', 25, 381, 346, '456654741', '9860145523'),
(146, 'Albert', 'Smékal', '2006-04-03', 26, 382, 208, '55446681', '0604036458'),
(147, 'Adam', 'Vojtek', '1992-06-15', 27, 383, 210, '858525454', '9206155253'),
(148, 'Petr', 'Brož', '1981-12-12', 28, 412, 224, '125235654', '8112125584'),
(149, 'Karel', 'Hašek', '2005-12-15', 29, 413, NULL, '585256485', '0512156548'),
(150, 'Michaela', 'Absolonová', '1979-01-12', 30, 414, 244, '258523654', '7901125147');

-- --------------------------------------------------------

--
-- Struktura tabulky `state`
--

CREATE TABLE `state` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `shortcut` varchar(5) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `state`
--

INSERT INTO `state` (`state_id`, `state_name`, `shortcut`) VALUES
(0, 'Česká republika', 'CZ'),
(4, 'Slovensko', 'SK');

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `user_name` varchar(40) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`users_id`, `user_name`, `password`, `admin`) VALUES
(196, 'Bóša', '$2y$10$8RX3W73ooQI.q7fpDcr0BO.PPax8C9tqSRpXLFiyTIVmpA0YtT3oK', 0),
(198, 'Dežo', '$2y$10$2S1Q0jLtEOyVJWkH0W2fBuO45uFZqZtilK2P/IZDbUp2gTxW4uG0K', 0),
(201, 'Martin', '$2y$10$J1z.9rPS/N8ott7ZZTT2wOG078ehbn3GlL.jGRJzfKmXgOsrVr8De', 1),
(202, 'mayer', '$2y$10$nVNz5aF/eZEM1fj5zSXs2.3w5AvUfKPmNFntS8WB/kuYYFbZFVvS.', 0),
(204, 'jarda', '$2y$10$5l.BSdIUtXcQsaRoVlX.gePL91F1mP.tv38f3M6v3sjRye9CQ/j7O', 0),
(207, 'Hezoun', '$2y$10$wRruqQF/8cVFIcgPkilmaO0F0xN.MaWCbb39T2hqpZqjmVpo59.Q2', 0),
(208, 'alsme', '$2y$10$4lNRCh93E1DBbI3zq4OQXuWLF6MzwEsrv7Y/7RUaGqzJTTgZHeDUK', 0),
(209, 'vojta', '$2y$10$KMlFGI9zK1coW5LVKLOBh.gqFjW3SODodSJsaPXaTc/rwx.hbcPPe', 0),
(210, 'vojtek', '$2y$10$rAjsNkSEuoLuLt7Yj9ezLeDhHtkJFeFXfm/ZyGxfrsbfUL3kQ2uEu', 0),
(214, 'krato', '$2y$10$pWqCRUajsR/xNASSsuKRb.VBi07fRCsiEJulBKjUIDb8UfKwlUjGO', 0),
(224, 'broza', '$2y$10$8AexjKsqo4hhdne1iCsT..QFB0170nsNvepNhfhdi9gifLAjYaSAa', 0),
(244, 'mapsol', '$2y$10$hdvjcS15pPedEIU7cCBoNOnKFMl8.vKPc53P4zzOXhW/EDbzUbam2', 0),
(346, 'alda', '$2y$10$uC70Uc3ggRHGEwkZ1jqULuNHJkU0W5kaOwr3s4RbLd8m1mcu3/jDS', 0);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`addresses_id`),
  ADD KEY `state` (`state`);

--
-- Indexy pro tabulku `area_code`
--
ALTER TABLE `area_code`
  ADD PRIMARY KEY (`area_code_id`);

--
-- Indexy pro tabulku `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `area_code` (`area_code`);

--
-- Indexy pro tabulku `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`persons_id`),
  ADD KEY `addresses` (`address`),
  ADD KEY `users` (`user`),
  ADD KEY `contact` (`contact`);

--
-- Indexy pro tabulku `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `user` (`user_name`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `addresses`
--
ALTER TABLE `addresses`
  MODIFY `addresses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=415;

--
-- AUTO_INCREMENT pro tabulku `area_code`
--
ALTER TABLE `area_code`
  MODIFY `area_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `contact`
--
ALTER TABLE `contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pro tabulku `persons`
--
ALTER TABLE `persons`
  MODIFY `persons_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT pro tabulku `state`
--
ALTER TABLE `state`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=347;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `state` FOREIGN KEY (`state`) REFERENCES `state` (`state_id`) ON UPDATE CASCADE;

--
-- Omezení pro tabulku `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `area_code` FOREIGN KEY (`area_code`) REFERENCES `area_code` (`area_code_id`) ON UPDATE CASCADE;

--
-- Omezení pro tabulku `persons`
--
ALTER TABLE `persons`
  ADD CONSTRAINT `addresses` FOREIGN KEY (`address`) REFERENCES `addresses` (`addresses_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `contact` FOREIGN KEY (`contact`) REFERENCES `contact` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `users` FOREIGN KEY (`user`) REFERENCES `users` (`users_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
