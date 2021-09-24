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
-- Copiando dados para a tabela qcommerce_3-x.qp1_estado: ~27 rows (aproximadamente)
DELETE FROM `qp1_estado`;
/*!40000 ALTER TABLE `qp1_estado` DISABLE KEYS */;
INSERT INTO `qp1_estado` (`ID`, `SIGLA`, `NOME`, `CAPITAL_ID`) VALUES
	(1, 'AC', 'Acre', 16),
	(2, 'AL', 'Alagoas', 68),
	(3, 'AP', 'Amapá', 130),
	(4, 'AM', 'Amazonas', 177),
	(5, 'BA', 'Bahia', 535),
	(6, 'CE', 'Ceará', 675),
	(7, 'DF', 'Distrito Federal', 801),
	(8, 'ES', 'Espiríto Santo', 878),
	(9, 'GO', 'Goiás', 971),
	(10, 'MA', 'Maranhão', 1306),
	(11, 'MT', 'Mato Grosso', 1372),
	(12, 'MS', 'Mato Grosso do Sul', 1483),
	(13, 'MG', 'Minas Gerais', 1606),
	(14, 'PA', 'Pará', 2412),
	(15, 'PB', 'Paraíba', 2631),
	(16, 'PR', 'Paraná', 2853),
	(17, 'PE', 'Pernambuco', 3291),
	(18, 'PI', 'Piauí', 3556),
	(19, 'RJ', 'Rio de Janeiro', 3631),
	(20, 'RN', 'Rio Grande do Norte', 3742),
	(21, 'RS', 'Rio Grande do Sul', 5811),
	(22, 'RO', 'Rondônia', 4325),
	(23, 'RR', 'Roraima', 4343),
	(24, 'SC', 'Santa Catarina', 4443),
	(25, 'SP', 'São Paulo', 5213),
	(26, 'SE', 'Sergipe', 5296),
	(27, 'TO', 'Tocantins', 5457);
/*!40000 ALTER TABLE `qp1_estado` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
