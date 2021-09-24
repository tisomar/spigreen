<?php
/* @var $pedido Pedido */

/* @var $cliente Cliente */
$cliente = $pedido->getCliente();

/* @var $endereco Endereco */
$endereco = $pedido->getEndereco();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
<head>
    <title>Spigreen</title>
    <meta name="author" content="Quality Press" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta name="language" content="pt-br" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel='stylesheet' type='text/css' href="<?php echo asset('/admin/assets/css/styles.min.css') ?>" media='screen,print' />
    <script type='text/javascript' src="<?php echo asset('/admin/assets/js/jquery-1.10.2.min.js') ?>"></script>
    <script type="text/javascript"> var root_path = '<?php echo BASE_PATH; ?>';</script>
</head>
<body style="background: none;">
<form method="POST" action="<?php echo escape(ClearSale::getUrlIntegracao()) ?>">
    <input type="hidden" name="CodigoIntegracao" value="<?php echo escape(ClearSale::getToken()) ?>" />
    <input type="hidden" name="PedidoID" value="<?php echo escape($pedido->getId()) ?>" />
    <input type="hidden" name="Data" value="<?php echo escape($pedido->getCreatedAt('Y-m-d H:i:s')) ?>" />
    <input type="hidden" name="IP" value="" />
    <input type="hidden" name="Total" value="<?php echo escape(number_format($pedido->getValorTotal(false), 2, '.', '')) ?>" />
    <input type="hidden" name="TipoPagamento" value="<?php echo escape($pedido->getTipoPagamentoClearSale()) ?>" />
    <input type="hidden" name="Parcelas" value="<?php echo ($formaPag = $pedido->getPedidoFormaPagamento()) ? (int) $formaPag->getNumeroParcelas() : 1 ?>" />
    <input type="hidden" name="Cartao_Bin" value="" />
    <input type="hidden" name="Cartao_Fim" value="" />
    <input type="hidden" name="Cartao_Numero_Mascarado" value="" />

    <!-- DADOS DA COMPRA -->
    <input type="hidden" name="Cobranca_Nome" value="<?php echo escape($pedido->getNomeClienteClearSale()) ?>" />
    <input type="hidden" name="Cobranca_Nascimento" value="<?php echo escape($cliente ? $cliente->getDataNascimento('Y-m-d H:i:s') : '') ?>" />
    <input type="hidden" name="Cobranca_Email" value="<?php echo escape($cliente ? $cliente->getEmail() : '') ?>" />
    <input type="hidden" name="Cobranca_Documento" value="<?php echo escape($pedido->getCobrancaDocumentoClearSale()) ?>" />
    <input type="hidden" name="Cobranca_Logradouro" value="<?php echo escape($endereco ? $endereco->getLogradouro() : '') ?>" />
    <input type="hidden" name="Cobranca_Logradouro_Numero" value="<?php echo escape($endereco ? $endereco->getNumero() : '') ?>" />
    <input type="hidden" name="Cobranca_Logradouro_Complemento" value="<?php echo escape($endereco ? $endereco->getComplemento() : '') ?>" />
    <input type="hidden" name="Cobranca_Bairro" value="<?php echo escape($endereco ? $endereco->getBairro() : '') ?>" />
    <input type="hidden" name="Cobranca_Cidade" value="<?php echo escape(($endereco && ($cidade = $endereco->getCidade())) ? $cidade->getNome() : '') ?>" />
    <input type="hidden" name="Cobranca_Estado" value="<?php echo escape(($endereco && ($cidade = $endereco->getCidade()) && ($estado = $cidade->getEstado())) ? ($estado->getSigla()) : '') ?>" />
    <input type="hidden" name="Cobranca_CEP" value="<?php echo escape($endereco ? only_digits($endereco->getCep()) : '') ?>" />
    <input type="hidden" name="Cobranca_Pais" value="Bra" />
    <input type="hidden" name="Cobranca_DDD_Telefone_1" value="<?php echo escape($cliente ? only_digits($cliente->getTelefoneDDD()) : '') ?>" />
    <input type="hidden" name="Cobranca_Telefone_1" value="<?php echo escape($cliente ? only_digits($cliente->getTelefoneSemDDD()) : '') ?>" />
    <input type="hidden" name="Cobranca_DDD_Celular" value="" />
    <input type="hidden" name="Cobranca_Celular" value="" />

    <!-- ENTREGA -->
    <input type="hidden" name="Entrega_Nome" value="<?php echo escape($pedido->getNomeClienteClearSale()) ?>" />
    <input type="hidden" name="Entrega_Nascimento" value="<?php echo escape($cliente ? $cliente->getDataNascimento('Y-m-d H:i:s') : '') ?>" />
    <input type="hidden" name="Entrega_Email" value="<?php echo escape($cliente ? $cliente->getEmail() : '') ?>" />
    <input type="hidden" name="Entrega_Documento" value="<?php echo escape($pedido->getCobrancaDocumentoClearSale()) ?>" />
    <input type="hidden" name="Entrega_Logradouro" value="<?php echo escape($endereco ? $endereco->getLogradouro() : '') ?>" />
    <input type="hidden" name="Entrega_Logradouro_Numero" value="<?php echo escape($endereco ? $endereco->getNumero() : '') ?>" />
    <input type="hidden" name="Entrega_Logradouro_Complemento" value="<?php echo escape($endereco ? $endereco->getComplemento() : '') ?>" />
    <input type="hidden" name="Entrega_Bairro" value="<?php echo escape($endereco ? $endereco->getBairro() : '') ?>" />
    <input type="hidden" name="Entrega_Cidade" value="<?php echo escape(($endereco && ($cidade = $endereco->getCidade())) ? $cidade->getNome() : '') ?>" />
    <input type="hidden" name="Entrega_Estado" value="<?php echo escape(($endereco && ($cidade = $endereco->getCidade()) && ($estado = $cidade->getEstado())) ? ($estado->getSigla()) : '') ?>" />
    <input type="hidden" name="Entrega_CEP" value="<?php echo escape($endereco ? only_digits($endereco->getCep()) : '') ?>" />
    <input type="hidden" name="Entrega_Pais" value="Bra" />
    <input type="hidden" name="Entrega_DDD_Telefone_1" value="<?php echo escape($cliente ? only_digits($cliente->getTelefoneDDD()) : '') ?>" />
    <input type="hidden" name="Entrega_Telefone_1" value="<?php echo escape($cliente ? only_digits($cliente->getTelefoneSemDDD()) : '') ?>" />
    <input type="hidden" name="Entrega_DDD_Celular" value="" />
    <input type="hidden" name="Entrega_Celular" value="" />

    <!-- PRODUTOS -->
    <?php $itemCount = 1;  ?>
    <?php foreach ($pedido->getPedidoItems() as $item) :  ?>
        <?php
        /* @var $item PedidoItem */
        /* @var $produto Produto */
        $produto = $item->getProdutoVariacao()->getProduto();
        $categoria = ($produto) ? $produto->getCategoria() : null;
        ?>
        <input type="hidden" name="Item_ID_<?php echo $itemCount ?>" value="<?php echo escape($item->getId()) ?>" />
        <input type="hidden" name="Item_Nome_<?php echo $itemCount ?>" value="<?php echo escape($produto ? $produto->getNome() : '') ?>" />
        <input type="hidden" name="Item_Qtd_<?php echo $itemCount ?>" value="<?php echo escape($item->getQuantidade()) ?>" />
        <input type="hidden" name="Item_Valor_<?php echo $itemCount ?>" value="<?php echo escape(number_format($item->getValorUnitario(), 2, '.', '')) ?>" />
        <input type="hidden" name="Item_Categoria_<?php echo $itemCount ?>" value="<?php echo escape($categoria ? $categoria->getNome() : '') ?>" />
        <?php $itemCount++  ?>
    <?php endforeach ?>

    <?php if ($pedido->getIntegrouClearSale()) : ?>
        <button class="btn btn-sm btn-success proximo-status" id="reenviar-dados-clear-sale"><span>Reenviar Dados Pedido</span></button>
    <?php else : ?>
        <button class="btn btn-sm btn-success proximo-status" id="enviar-dados-clear-sale"><span>Enviar Dados Pedido</span></button>
    <?php endif ?>

</form>
<script type="text/javascript">
    $(document).ready(function(){

        $('#reenviar-dados-clear-sale').click(function(){

            var $this = $(this);
            $this.html('<i class="icon icon-spin icon-spinner"></i> Reenviando...');

            if (window.parent) {
                //aumenta o tamanho do frame
                var iframe = window.parent.document.getElementById('idFrameIntegracaoClearSale');
                if (iframe) {
                    iframe.height = 120;
                }

                //esconde o outro frame
                iframe = window.parent.document.getElementById('idFrameResultadoIntegracaoClearSale');
                if (iframe) {
                    iframe.style.display = 'none';
                }
            }

            $this.closest('form').submit();
            $this.prop('disabled', true);

            return false;
        });

        $('#enviar-dados-clear-sale').click(function(){

            var $this = $(this);
            $this.html('<i class="icon icon-spin icon-spinner"></i> Enviando...');

            if (window.parent) {
                //aumenta o tamanho do frame
                var iframe = window.parent.document.getElementById('idFrameIntegracaoClearSale');
                if (iframe) {
                    iframe.height = 120;
                }
            }

            var data = {
                id: '<?php echo $pedido->getId() ?>',
                object: 'Pedido',
                method: 'IntegrouClearSale',
                value: 'true'
            };

            //Primeiro vamos ligar o flag indicando que o pedido foi integrado. Depois será feito o submit a Clear Sale.
            $.ajax({
                url: window.root_path + "/admin/actions/update.field.boolean.php",
                type: 'POST',
                data: data,
                success: function(data){
                    $this.closest('form').submit();
                    $this.prop('disabled', true);
                }
            });

            return false;
        });
    });
</script>
</body>
</html>