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
-- Copiando dados para a tabela qcommerce_3-x.qp1_pedido_status: ~5 rows (aproximadamente)
DELETE FROM `qp1_pedido_status`;
/*!40000 ALTER TABLE `qp1_pedido_status` DISABLE KEYS */;
INSERT INTO `qp1_pedido_status` (`ID`, `MENSAGEM`, `LABEL_PRE_CONFIRMACAO`, `LABEL_CONFIRMACAO`, `LABEL_POS_CONFIRMACAO`, `STATUS`, `METODO`, `ORDEM`) VALUES
	(1, 'Estamos no aguardo da confirmação de pagamento do seu pedido. Assim que confirmado, daremos sequencia no processo de expedição do seu pedido.', 'Aguardando Pagamento', 'Confirmar', 'Pagamento Confirmado', 'ANDAMENTO', 'OBRIGATORIO', 1),
	(2, 'Recebemos a informação de que o pagamento do seu pedido foi confirmado. Neste momento, daremos inicio a expedição dos itens do seu pedido para garantir que você receba seus produtos adequadamente.', 'Aguardando Expedição', 'Confirmar', 'Pedido Expedido', 'ANDAMENTO', 'OBRIGATORIO', 2),
	(3, 'Seus produtos foram devidamente separados e encaminhados ao meio de entrega escolhido por você. Em breve você receberá-los no endereço informado na finalização da compra.', 'Aguardando Entrega', 'Confirmar', 'Pedido Entregue', 'ANDAMENTO', 'ENTREGA', 3),
	(4, 'Seus produtos estão disponíveis para serem retirados em nossa loja.', 'Aguardando Retirada', 'Confirmar', 'Pedido Retirado', 'ANDAMENTO', 'RETIRADA', 3),
	(5, 'Finalizamos as etapas de entrega do seu pedido.', 'Aguardando Finalização', 'Confirmar', 'Pedido Finalizado', 'FINALIZADO', 'OBRIGATORIO', 4);
/*!40000 ALTER TABLE `qp1_pedido_status` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
