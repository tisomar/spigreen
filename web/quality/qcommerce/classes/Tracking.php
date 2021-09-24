<?php
class Tracking
{

    /*
     * Gera o codigo de tracking de e-commerce do Google
     * @param $objPedido O pedido que sera enviado ao rastreador
     * @param $force Se for true vai gerar o codigo de tracking mesmo quando nao detectar Google Analytics UA
     */
    public static function generateGoogleTracking(Pedido $objPedido, $force = false)
    {

        if (!$force) {
            if (strlen(\Config::get('google_analytics_ua')) < 3) {
                return '';
            }
        }

        if (\Config::get('google_track_ecommerce') == 0) {
            return '';
        }

        /* @var $enderecoCliente Endereco */
        $enderecoCliente = $objPedido->getEndereco();
        $cidadeNome = $enderecoCliente->getCidade()->getNome();
        $estadoNome = $enderecoCliente->getCidade()->getEstado()->getNome();

        $output = '';
        $output .= '<script type="text/javascript">';

        $output .= 'var _gaq = _gaq || [];';
        $output .= '_gaq.push(["_setAccount", "' . \Config::get('google_analytics_ua') . '"]);';
        $output .= '_gaq.push(["_trackPageview"]);';
        $output .= '_gaq.push(["_addTrans",
            "' . $objPedido->getId() . '",                      // transaction ID - required
            "' . \Config::get('empresa_razao_social') . '",     // affiliation or store name
            "' . $objPedido->getValorTotal() . '",              // total - required
            "0.00",                                         // tax
            "' . $objPedido->getValorEntrega() . '",            // shipping
            "' . $cidadeNome . '",                              // city
            "' . $estadoNome . '",                              // state or province
            "BRAZIL"                                        // country
            ]);';

        $itens = $objPedido->getPedidoItemsJoinProdutoVariacao();
        foreach ($itens as $itemPedido) {
            $output .= '_gaq.push(["_addItem",
                "' . $objPedido->getId() . '",                                              // transaction ID - required
                "' . $itemPedido->getProdutoVariacao()->getProduto()->getId() . '",         // SKU/code - required
                "' . $itemPedido->getProdutoVariacao()->getProdutoNomeCompleto(' | ') . '",      // product name
                "",                                                                     // category or variation
                "' . $itemPedido->getValorUnitario() . '",                                  // unit price - required
                "' . $itemPedido->getQuantidade() . '"                                      // quantity - required
            ]);';
        }

        $output .= '_gaq.push(["_trackTrans"]);';
        $output .= '(function() {';
        $output .= 'var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;';
        $output .= 'ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";';
        $output .= 'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);';
        $output .= '})();';
        $output .= '</script>';

        return $output;
    }

    public static function generateFacebookTracking(Pedido $objPedido)
    {
    }
}
