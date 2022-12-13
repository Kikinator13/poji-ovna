-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Pon 05. pro 2022, 13:56
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_address` (IN `pstreet` VARCHAR(255), IN `pZIP` INT, IN `pcity` VARCHAR(255), OUT `id` INT)   BEGIN
    SELECT addresses_id INTO id 
    	FROM addresses 
        WHERE 
              street_and_number = pstreet and
              ZIP = pZIP AND
              city = pcity;
    IF(id IS NULL) THEN
        INSERT INTO addresses (street_and_number, ZIP, city) 			
        VALUES (pstreet, pZIP, pcity);
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
  `ZIP` int(11) NOT NULL,
  `city` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `addresses`
--

INSERT INTO `addresses` (`addresses_id`, `street_and_number`, `ZIP`, `city`) VALUES
(33, 'Hornoměcholupská', 14000, 'Praha 4'),
(34, 'Lipanská', 13000, 'Praha'),
(35, 'Šumavská', 12000, 'Praha 2'),
(36, 'Falešný', 14000, 'Petrovice'),
(37, 'Horáčkova', 14000, 'Praha 4'),
(38, 'Hornoměcholupská', 14000, 'Praha 4'),
(39, 'aad0', 0, 'dddsfa0'),
(40, 'aad1', 1, 'dddsfa1'),
(41, 'aad2', 2, 'dddsfa2'),
(44, 'aad5', 5, 'dddsfa5'),
(45, 'aad6', 6, 'dddsfa6'),
(46, 'aad7', 7, 'dddsfa7'),
(47, 'aad8', 8, 'dddsfa8'),
(48, 'aad9', 9, 'dddsfa9'),
(49, 'aad10', 10, 'dddsfa10'),
(50, 'aad11', 11, 'dddsfa11'),
(51, 'aad12', 12, 'dddsfa12'),
(52, 'aad13', 13, 'dddsfa13'),
(53, 'aad14', 14, 'dddsfa14'),
(54, 'aad15', 15, 'dddsfa15'),
(55, 'aad16', 16, 'dddsfa16'),
(56, 'aad17', 17, 'dddsfa17'),
(57, 'aad18', 18, 'dddsfa18'),
(58, 'aad19', 19, 'dddsfa19'),
(59, 'aad20', 20, 'dddsfa20'),
(60, 'aad21', 21, 'dddsfa21'),
(61, 'aad22', 22, 'dddsfa22'),
(62, 'aad23', 23, 'dddsfa23'),
(63, 'aad24', 24, 'dddsfa24'),
(64, 'aad25', 25, 'dddsfa25'),
(65, 'aad26', 26, 'dddsfa26'),
(66, 'aad27', 27, 'dddsfa27'),
(67, 'aad28', 28, 'dddsfa28'),
(68, 'aad29', 29, 'dddsfa29'),
(69, 'aad30', 30, 'dddsfa30'),
(70, 'aad31', 31, 'dddsfa31'),
(71, 'aad32', 32, 'dddsfa32'),
(72, 'aad33', 33, 'dddsfa33'),
(73, 'aad34', 34, 'dddsfa34'),
(74, 'aad35', 35, 'dddsfa35'),
(75, 'aad36', 36, 'dddsfa36'),
(76, 'aad37', 37, 'dddsfa37'),
(77, 'aad38', 38, 'dddsfa38'),
(78, 'aad39', 39, 'dddsfa39'),
(79, 'aad40', 40, 'dddsfa40'),
(80, 'aad41', 41, 'dddsfa41'),
(81, 'aad42', 42, 'dddsfa42'),
(82, 'aad43', 43, 'dddsfa43'),
(83, 'aad44', 44, 'dddsfa44'),
(84, 'aad45', 45, 'dddsfa45'),
(85, 'aad46', 46, 'dddsfa46'),
(86, 'aad47', 47, 'dddsfa47'),
(87, 'aad48', 48, 'dddsfa48'),
(88, 'aad49', 49, 'dddsfa49'),
(89, 'aad50', 50, 'dddsfa50'),
(90, 'aad51', 51, 'dddsfa51'),
(91, 'aad52', 52, 'dddsfa52'),
(92, 'aad53', 53, 'dddsfa53'),
(93, 'aad54', 54, 'dddsfa54'),
(94, 'aad55', 55, 'dddsfa55'),
(95, 'aad56', 56, 'dddsfa56'),
(96, 'aad57', 57, 'dddsfa57'),
(97, 'aad58', 58, 'dddsfa58'),
(98, 'aad59', 59, 'dddsfa59'),
(99, 'aad60', 60, 'dddsfa60'),
(100, 'aad61', 61, 'dddsfa61'),
(101, 'aad62', 62, 'dddsfa62'),
(102, 'aad63', 63, 'dddsfa63'),
(103, 'aad64', 64, 'dddsfa64'),
(104, 'aad65', 65, 'dddsfa65'),
(105, 'aad66', 66, 'dddsfa66'),
(106, 'aad67', 67, 'dddsfa67'),
(107, 'aad68', 68, 'dddsfa68'),
(108, 'aad69', 69, 'dddsfa69'),
(109, 'aad70', 70, 'dddsfa70'),
(110, 'aad71', 71, 'dddsfa71'),
(111, 'aad72', 72, 'dddsfa72'),
(112, 'aad73', 73, 'dddsfa73'),
(113, 'aad74', 74, 'dddsfa74'),
(114, 'aad75', 75, 'dddsfa75'),
(115, 'aad76', 76, 'dddsfa76'),
(116, 'aad77', 77, 'dddsfa77'),
(117, 'aad78', 78, 'dddsfa78'),
(118, 'aad79', 79, 'dddsfa79'),
(119, 'aad80', 80, 'dddsfa80'),
(120, 'aad81', 81, 'dddsfa81'),
(121, 'aad82', 82, 'dddsfa82'),
(122, 'aad83', 83, 'dddsfa83'),
(123, 'aad84', 84, 'dddsfa84'),
(124, 'aad85', 85, 'dddsfa85'),
(125, 'aad86', 86, 'dddsfa86'),
(126, 'aad87', 87, 'dddsfa87'),
(127, 'aad88', 88, 'dddsfa88'),
(128, 'aad89', 89, 'dddsfa89'),
(129, 'aad90', 90, 'dddsfa90'),
(131, 'aad92', 92, 'dddsfa92'),
(132, 'aad93', 93, 'dddsfa93'),
(133, 'aad94', 94, 'dddsfa94'),
(134, 'aad95', 95, 'dddsfa95'),
(135, 'aad96', 96, 'dddsfa96'),
(136, 'aad97', 97, 'dddsfa97'),
(137, 'aad98', 98, 'dddsfa98'),
(138, 'aad99', 99, 'dddsfa99'),
(139, 'safdsf', 45684, 'Praha'),
(140, 'Pujmanové', 14000, 'Praha 4'),
(141, 'Švehlova', 11000, 'Praha 10'),
(142, 'sdfasdfsd', 14000, 'Praha'),
(145, 'sněmovní', 11000, 'Praha 1'),
(147, 'Náměstí míru', 15000, 'dddsfa91'),
(151, 'Jateční', 13000, 'Praha 3'),
(152, 'Ječná', 12000, 'Praha 2'),
(153, 'Žitná', 12000, 'Praha 2'),
(155, 'Ruská', 12000, 'Praha 2');

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
(17, 'Martin', 'Kikta', '1990-06-18', '+420735614776', 'martin@kikta.cz', 38, 63, '546852586', 9053015648),
(21, 'Marcus', 'Cocotius', '2020-01-15', '+420465458864', 'marcus@gmail.com', 151, 67, '123456654', 2001150654),
(22, 'dežo', 'Okamura', '1954-05-25', '+420456654456', '4@seznam.cz', 155, 68, '445685236', 5405256565),
(25, 'aaddsss7', 'pdsfadf467', '0000-00-00', '486 4867', '7@seznam.cz', 46, 71, '7', 7),
(26, 'aaddsss8', 'pdsfadf468', '0000-00-00', '486 4868', '8@seznam.cz', 47, 72, '8', 8),
(27, 'aaddsss9', 'pdsfadf469', '0000-00-00', '486 4869', '9@seznam.cz', 48, 73, '9', 9),
(28, 'aaddsss10', 'pdsfadf4610', '0000-00-00', '486 48610', '10@seznam.cz', 49, 74, '10', 10),
(29, 'aaddsss11', 'pdsfadf4611', '0000-00-00', '486 48611', '11@seznam.cz', 50, 75, '11', 11),
(30, 'aaddsss12', 'pdsfadf4612', '0000-00-00', '486 48612', '12@seznam.cz', 51, 76, '12', 12),
(31, 'aaddsss13', 'pdsfadf4613', '0000-00-00', '486 48613', '13@seznam.cz', 52, 77, '13', 13),
(32, 'aaddsss14', 'pdsfadf4614', '0000-00-00', '486 48614', '14@seznam.cz', 53, 78, '14', 14),
(33, 'aaddsss15', 'pdsfadf4615', '0000-00-00', '486 48615', '15@seznam.cz', 54, 79, '15', 15),
(34, 'aaddsss16', 'pdsfadf4616', '0000-00-00', '486 48616', '16@seznam.cz', 55, 80, '16', 16),
(35, 'aaddsss17', 'pdsfadf4617', '0000-00-00', '486 48617', '17@seznam.cz', 56, 81, '17', 17),
(36, 'aaddsss18', 'pdsfadf4618', '0000-00-00', '486 48618', '18@seznam.cz', 57, 82, '18', 18),
(37, 'aaddsss19', 'pdsfadf4619', '0000-00-00', '486 48619', '19@seznam.cz', 58, 83, '19', 19),
(38, 'aaddsss20', 'pdsfadf4620', '0000-00-00', '486 48620', '20@seznam.cz', 59, 84, '20', 20),
(122, 'Petr', 'Fiala', '0001-01-01', '+420456123789', 'fiala@vlada.cz', 145, 178, '789456123', 101010080),
(124, 'Adolf', 'Babiš', '1970-02-15', '+420735852135', 'adolf@seznam.cz', 152, 182, '547854658', 7002154565),
(125, 'Jan', 'Trocký', '1945-12-18', '+420456654258', 'trocky@email.cz', 153, 183, '123321DF25', 4512180505),
(126, 'Milan', 'Nejedlý', '1999-02-14', '+420456852156', 'nejedly@centrum.cz', 155, 184, '123321789', 9902140051);

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
(63, 'martin', '$2y$10$npTt/tOfcd5soiKQfu2pOeaSC1HczsDb9OJAsYqyQ4mUU0NlARLpm', 1),
(67, 'Marcus_Cocotius', '$2y$10$a5vFkdy43KsWqrIa56Jvye4PMLMsGmmUyEDH7f9zWwtfBYdjhwXv2', 0),
(68, 'dežo', '$2y$10$XUmVfnIEspct7N97zch3qe5msWG4njbCBiYAomAdjgr3NCYskp6pS', 0),
(71, 'dddd7', 'ccc7', 0),
(72, 'dddd8', 'ccc8', 0),
(73, 'dddd9', 'ccc9', 0),
(74, 'dddd10', 'ccc10', 0),
(75, 'dddd11', 'ccc11', 0),
(76, 'dddd12', 'ccc12', 0),
(77, 'dddd13', 'ccc13', 0),
(78, 'dddd14', 'ccc14', 0),
(79, 'dddd15', 'ccc15', 0),
(80, 'dddd16', 'ccc16', 0),
(81, 'dddd17', 'ccc17', 0),
(82, 'dddd18', 'ccc18', 0),
(83, 'dddd19', 'ccc19', 0),
(84, 'dddd20', 'ccc20', 0),
(85, 'dddd21', 'ccc21', 0),
(86, 'dddd22', 'ccc22', 0),
(87, 'dddd23', 'ccc23', 0),
(88, 'dddd24', 'ccc24', 0),
(89, 'dddd25', 'ccc25', 0),
(90, 'dddd26', 'ccc26', 0),
(91, 'dddd27', 'ccc27', 0),
(92, 'dddd28', 'ccc28', 0),
(93, 'dddd29', 'ccc29', 0),
(94, 'dddd30', 'ccc30', 0),
(95, 'dddd31', 'ccc31', 0),
(96, 'dddd32', 'ccc32', 0),
(97, 'dddd33', 'ccc33', 0),
(98, 'dddd34', 'ccc34', 0),
(99, 'dddd35', 'ccc35', 0),
(100, 'dddd36', 'ccc36', 0),
(101, 'dddd37', 'ccc37', 0),
(102, 'dddd38', 'ccc38', 0),
(103, 'dddd39', 'ccc39', 0),
(104, 'dddd40', 'ccc40', 0),
(105, 'dddd41', 'ccc41', 0),
(106, 'dddd42', 'ccc42', 0),
(107, 'dddd43', 'ccc43', 0),
(108, 'dddd44', 'ccc44', 0),
(109, 'dddd45', 'ccc45', 0),
(110, 'dddd46', 'ccc46', 0),
(111, 'dddd47', 'ccc47', 0),
(112, 'dddd48', 'ccc48', 0),
(113, 'dddd49', 'ccc49', 0),
(114, 'dddd50', 'ccc50', 0),
(115, 'dddd51', 'ccc51', 0),
(116, 'dddd52', 'ccc52', 0),
(117, 'dddd53', 'ccc53', 0),
(118, 'dddd54', 'ccc54', 0),
(119, 'dddd55', 'ccc55', 0),
(120, 'dddd56', 'ccc56', 0),
(121, 'dddd57', 'ccc57', 0),
(122, 'dddd58', 'ccc58', 0),
(123, 'dddd59', 'ccc59', 0),
(124, 'dddd60', 'ccc60', 0),
(125, 'dddd61', 'ccc61', 0),
(126, 'dddd62', 'ccc62', 0),
(127, 'dddd63', 'ccc63', 0),
(128, 'dddd64', 'ccc64', 0),
(129, 'dddd65', 'ccc65', 0),
(130, 'dddd66', 'ccc66', 0),
(131, 'dddd67', 'ccc67', 0),
(132, 'dddd68', 'ccc68', 0),
(133, 'dddd69', 'ccc69', 0),
(134, 'dddd70', 'ccc70', 0),
(135, 'dddd71', 'ccc71', 0),
(136, 'dddd72', 'ccc72', 0),
(137, 'dddd73', 'ccc73', 0),
(138, 'dddd74', 'ccc74', 0),
(139, 'dddd75', 'ccc75', 0),
(140, 'dddd76', 'ccc76', 0),
(141, 'dddd77', 'ccc77', 0),
(142, 'dddd78', 'ccc78', 0),
(143, 'dddd79', 'ccc79', 0),
(144, 'dddd80', 'ccc80', 0),
(145, 'dddd81', 'ccc81', 0),
(146, 'dddd82', 'ccc82', 0),
(147, 'dddd83', 'ccc83', 0),
(148, 'dddd84', 'ccc84', 0),
(149, 'dddd85', 'ccc85', 0),
(150, 'dddd86', 'ccc86', 0),
(151, 'dddd87', 'ccc87', 0),
(152, 'dddd88', 'ccc88', 0),
(153, 'dddd89', 'ccc89', 0),
(154, 'dddd90', 'ccc90', 0),
(155, 'dddd91', 'ccc91', 0),
(156, 'dddd92', 'ccc92', 0),
(157, 'dddd93', 'ccc93', 0),
(158, 'dddd94', 'ccc94', 0),
(159, 'dddd95', 'ccc95', 0),
(160, 'dddd96', 'ccc96', 0),
(161, 'dddd97', 'ccc97', 0),
(162, 'dddd98', 'ccc98', 0),
(163, 'dddd99', 'ccc99', 0),
(178, 'premiér', '$2y$10$OA.AgBVtA47qCVc6QzE.2.8hXWVr8lcIu3gZgEt6VvUf/CChH4R0u', 0),
(182, 'Velký P', '$2y$10$Ae8F1UMjVDj3lGvkLC.DoO44FqP1R9Vh5Rl.oq2f5jUwAjV2ki9.a', 0),
(183, 'honza', '$2y$10$Xuce10YB3Ohv.27TDwnDoeI.BjQFLvL5B.6cWJ1cEQbGM8pmmOqkW', 0),
(184, 'milarepa', '$2y$10$cFlzDaJIbQmAYDAfp89DAOMPjgnVvqfHTve5TcsaZ/GQnlX91Twq.', 0);

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
  MODIFY `addresses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT pro tabulku `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `persons`
--
ALTER TABLE `persons`
  MODIFY `persons_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

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
