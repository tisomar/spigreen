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
-- Copiando dados para a tabela qcommerce_3-x.qp1_usuario: ~1 rows (aproximadamente)
DELETE FROM `qp1_usuario`;
/*!40000 ALTER TABLE `qp1_usuario` DISABLE KEYS */;
INSERT INTO `qp1_usuario` (`ID`, `NOME`, `EMAIL`, `LOGIN`, `SENHA`, `MASTER`, `ERROS_LOGIN`, `TOKEN`) VALUES
	(1, 'Suporte - QualityPress', 'suporte@qualitypress.com.br', 'qpress', '762f57ec7c7cde1f918cbcf934ff68ec7fb84689', 1, NULL, NULL);
/*!40000 ALTER TABLE `qp1_usuario` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
