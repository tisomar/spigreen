-- --------------------------------------------------------
-- Servidor:                     10.8.10.3
-- Versão do servidor:           5.6.10-log - MySQL Community Server (GPL)
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Copiando dados para a tabela qcommerce_3-x.qp1_rede: 7 rows
DELETE FROM `qp1_rede`;
/*!40000 ALTER TABLE `qp1_rede` DISABLE KEYS */;
INSERT INTO `qp1_rede` (`ID`, `NOME`, `IMAGEM`, `ICON`, `ATIVO`, `LINK`, `ORDEM`) VALUES
	(1, 'Facebook', '277749146cd82d64aa1b44a276df2a3d3f3c6f0b.png', 'facebook', 1, 'https://www.facebook.com/QualityPressSI', 2),
	(2, 'Twitter', 'd0ec571c7be80a1cde7be5dcecb8ee1c399966cd.png', 'twitter', 1, 'http://twitter.com/Quality_Press', 5),
	(3, 'Youtube', 'e05abf0d4ee8d872b656f5edc87d4cbe09b421da.png', 'youtube', 1, 'https://youtube.com.br', 1),
	(4, 'Linkedin', 'd3715f317582f4c73425772986eb4dc69b1b1d1b.png', 'linkedin', 1, 'http://www.linkedin.com/company/quality-press', 1),
	(5, 'Google Plus', 'd86313544046d71b723aa29271a1daa8ad083a5d.png', 'google-plus', 1, 'https://plus.google.com/', 3),
	(7, 'Instagram', '18d1416932df40509bf5b4a2c42dd50d41e980b7.png', 'instagram', 1, 'https://www.instagram.com.br/qualitypress', 1),
	(6, 'WordPress', 'e74c60a139b2efc7f25b246294be7b063a876f00.png', 'wordpress', 1, 'http://qualitypress.com.br', 6);
/*!40000 ALTER TABLE `qp1_rede` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
