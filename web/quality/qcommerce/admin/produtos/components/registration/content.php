<?php
use PFBC\View;
use PFBC\Form;
use PFBC\Element;

$getProdutoTaxa = isset($getProdutoTaxa) && $getProdutoTaxa ? $getProdutoTaxa : false;

\QPress\Template\Widget::render(QCOMMERCE_DIR . '/admin/_2015/widget/menu.produtos.php', array(
    'context' => ProdutoPeer::OM_CLASS,
    'reference' => $object->getId(),
    'module' => $router->getModule(),
    'getProdutoTaxa' => $getProdutoTaxa
));

$form = new Form("registrer");

$form->configure(array(
    'class' => '',
    'action' => $request->server->get('REQUEST_URI'),
    "view" => new View\Vertical(),
));

$form->addElement(new Element\Hidden('produto_variacao[IS_MASTER]', true));

$form->addElement(new Element\HTML('
    <div class="row">
        <div class="col-lg-6">
'));

$form->addElement(new Element\Textbox("Nome do produto", "produto[NOME]", array(
    "value" => $object->getNome(),
    "required" => true,
    "placeholder" => 'Nome do produto...'
)));

$form->addElement(new Element\Textarea("Breve descrição sobre o produto", "produto[DESCRICAO]", array(
    "value" => $object->getDescricao(),
    'rows' => 3,
    'class' => 'mceEditorMini'
)));

if (!$getProdutoTaxa) {
    $form->addElement(new Element\Select("Marca", "produto[MARCA_ID]", ProdutoPeer::getMarcaList(), array(
        "value" => $object->getMarcaId(),
    )));


    $form->addElement(new Element\Textarea("Características e informações detalhadas sobre o produto", "produto[CARACTERISTICAS]", array(
        "value" => $object->getCaracteristicas(),
        'class' => 'mceEditor',
    )));

    $form->addElement(new Element\Textbox('TAGS', "produto[TAGS]", array(
        'value' => implode(',', $object->getTags()),
        'class' => 'input-token',
        'shortDesc' => 'As tags servirão para auxiliar na busca dos produtos. Em algumas vezes, o consumidor pode buscar por algum ' .
            'termo que não esteja presente no nome do produto. Com as tags preenchidas, será possível adicionar outros termos, tais ' .
            'como, a marca, as categorias e nomes similares relacionados ao produto.'
    )));


    /**
     * DIMENSÕES DO PRODUTO
     * Estas dimensãoes são utilizadas para a base de cálculo de frete para correios
     */

    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Entrega</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in">
                <p>Estas dimensãoes são utilizadas para a base de cálculo de frete para correios.</p>
    '));

    $form->addElement(new Element\Number("Peso (gramas)", "produto[PESO]", array(
        "value" => $object->getPeso(),
        "required" => true,
        "min" => 1,
    )));

    $form->addElement(new Element\HTML('
        <div class="row">
    '));

    $form->addElement(new Element\HTML('
        <div class="col-xs-12 col-sm-4">
    '));

    $form->addElement(new Element\Number("Altura (cm)", "produto[ALTURA]", array(
        "value" => $object->getAltura(),
        "required" => true,
        "min" => 1,
    )));

    $form->addElement(new Element\HTML('
        </div>
        <div class="col-xs-12 col-sm-4">
    '));

    $form->addElement(new Element\Number("Largura (cm)", "produto[LARGURA]", array(
        "value" => $object->getLargura(),
        "required" => true,
        "min" => 1,
    )));

    $form->addElement(new Element\HTML('
        </div>
        <div class="col-xs-12 col-sm-4">
    '));

    $form->addElement(new Element\Number("Comprimento (cm)", "produto[COMPRIMENTO]", array(
        "value" => $object->getComprimento(),
        "required" => true,
        "min" => 1,
    )));

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));
}


$form->addElement(new Element\HTML('
    </div>
    <div class="col-lg-3">
'));

/**
 * PREÇO DO PRODUTO
 */
$mensagemPreco = "";
if ($object->hasVariacoes()) {
    $mensagemPreco = '
    <div class="alert alert-warning">
        <i class="icon-info-sign"></i>
        Este produto possui variações. Por tanto, para alterar o valor de cada variação, você deverá acessar o menu (ao topo) "Variações".
        <br>
        <i class="icon-info-sign"></i>
        Os valores abaixo são utilizados para mostrar o preço na página de listagem e detalhes do produto quando não houver uma variação selecionada.
    </div>';
}

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Preços</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
            ' . $mensagemPreco . '
'));

$form->addElement(new Element\Textbox("Referência", "produto_variacao[SKU]", array(
    "value" => $object->getSku(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Preço normal (R$)", "produto_variacao[VALOR_BASE]", array(
    "value" => 'R$ ' . format_number($object->getValorBase()),
    "required" => true,
    "class" => 'mask-money',
)));
if (!$getProdutoTaxa) {
    $form->addElement(new Element\Textbox("Preço de oferta (R$)", "produto_variacao[VALOR_PROMOCIONAL]", array(
        "value" => 'R$ ' . format_number($object->getValorPromocional()),
        "required" => false,
        "class" => 'mask-money'
    )));

    // $form->addElement(new Element\Textbox("Preço Diferenciado para Distribuidor (R$)", "produto_variacao[VALOR_DISTRIBUIDOR]", array(
    //     "value" => 'R$ ' . format_number($object->getValorDistribuidor()),
    //     "required" => false,
    //     "class" => 'mask-money'
    // )));

    // $form->addElement(new Element\Textbox("Preço de Serviço (R$)", "produto[VALOR_SERVICO]", array(
    //     "value" => 'R$ ' . format_number($object->getValorServico()),
    //     "required" => true,
    //     "class" => 'mask-money',
    // )));

    // $form->addElement(new Element\Textbox("Preço de Custo (custo completo com serviço) (R$)", "produto[VALOR_CUSTO]", array(
    //     "value" => 'R$ ' . format_number($object->getValorCusto()),
    //     "required" => true,
    //     "class" => 'mask-money',
    // )));
}

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Integração e taxas</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
            ' . $mensagemPreco . '
'));

$form->addElement(new Element\Textbox("Preço integração admin (R$)", "produto_variacao[VALOR_INTEGRACAO_ADMIN]", array(
    "value" => 'R$ ' . format_number($object->getProdutoVariacao()->getValorIntegracaoAdmin()),
    "required" => false,
    "class" => 'mask-money',
)));

$arrFatorCorrecao = [
    'null' => '', 
    '1' => 'Grupo 1', 
    '2' => 'Grupo 2', 
    '3' => 'Grupo 3',
    '4' => 'Grupo 4', 
    '5' => 'Grupo 5', 
    '6' => 'Grupo 6', 
    '7' => 'Grupo 7', 
    '8' => 'Grupo 8', 
    '9' => 'Grupo 9', 
    '10' => 'Grupo 10'
];

$form->addElement(new Element\Select('Fator correção grupo', 'produto_variacao[FATOR_CORRECAO_GRUPO]', $arrFatorCorrecao, [
    'value' => $object->getProdutoVariacao()->getFatorCorrecaoGrupo()
]));


$form->addElement(new Element\HTML('
        </div>
    </div>
'));


if (!$getProdutoTaxa) {
    /**
     * Mensalidade
     */
//    $form->addElement(new Element\HTML('
//        <div class="panel panel-gray">
//            <div class="panel-heading">
//                <h4>Mensalidade</h4>
//                <div class="options">
//                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
//                </div>
//            </div>
//            <div class="panel-body collapse in">
//        '));
//
//    $form->addElement(new Element\Select("Mensalidade", "produto[MENSALIDADE]", array('1' => 'Sim', '0' => 'Não'), array(
//        "value" => $object->getMensalidade(),
//        'shortDesc' => 'Indica se este produto é referente a uma cobrança de mensalidade'
//    )));
//
//    $form->addElement(new Element\HTML('
//            </div>
//        </div>
//    '));


    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Pontuação</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in">
        '));

    /**
     * Pontuação
     */
    $form->addElement(new Element\Number("Pontos", "produto[VALOR_PONTOS]", array(
        "value" => $object->getValorPontos(),
        "required" => true,
        'min' => '0'
    )));

    $form->addElement(new Element\Select("Habilitado para participação resultados", "produto[PARTICIPACAO_RESULTADOS]", ProdutoPeer::getParticipacaoResultadosList(), array(
        "value" => $object->getParticipacaoResultados()
    )));

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));


    /**
     * KIT
     */
    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Plano</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in">
        '));

    $form->addElement(new Element\Select("Plano", "produto[PLANO_ID]", ProdutoPeer::getPlanoList(), array(
        "value" => $object->getPlanoId(),
    )));

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));

    /**
     * TIPO
     */
    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Tipo do produto</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in">
        '));

    $form->addElement(new Element\Select("Tipo do produto", "produto[TIPO_PRODUTO]", ProdutoPeer::getTipoProdutoList(), array(
        "value" => $object->getTipoProduto(), 'id' => "prod-tipo"
    )));

    /*$form->addElement(new Element\HTML('
        <div class="text-center">
            <a href="'.get_url_site().'/admin/produto-composto/registration?context=Produto&reference='.$object->getId().'" id="prod-tipo-link" style="display: none;">Cadastre os produtos da Composição clicando aqui</a>
        </div>
    '));*/


    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));

    /**
     * ESTOQUE DO PRODUTO
     */
    $mensagemEstoque = "";
    if ($object->hasVariacoes()) {
        $mensagemEstoque = '
        <div class="alert alert-warning">
            <i class="icon-info-sign"></i>
            Este produto possui variações. Por tanto, o controle de estoque é feito por variação no menu (ao topo) "Variações".
        </div>';
    }

    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Estoque</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in">
                ' . $mensagemEstoque . '
    '));

    foreach($centroDistribuicao as $centroDist) :
        $form->addElement(new Element\HTML("
            <h5><strong>Centro distribuição: </strong> {$centroDist->getDescricao($centroDist->getId())}</h5>
        "));

        if (!$object->hasVariacoes()) {
            $form->addElement(new Element\Number("Estoque atual", "produto_variacao[ESTOQUE_ATUAL]", array(
                "value" => $object->getProdutoVariacao()->getEstoqueAtualCD($centroDist->getId()),
                "required" => true,
                "disabled" => true,
            )));

            $form->addElement(new Element\Number("Estoque mínimo", "produto_variacao[ESTOQUE_MINIMO]", array(
                "value" => $object->getEstoqueMinimo(),
                "disabled" => ($object->hasVariacoes())
            )));
            if (!$object->hasVariacoes()) {
                $form->addElement(new Element\HTML('
                    <a target="_self" href="' . get_url_site() . '/admin/estoque/registration?produto_id=' . $object->getId() . '&produto_variacao_id=' . $object->getProdutoVariacao()->getId() . '">Editar estoque</a><br><br>
                '));
            }
        }
    endforeach;

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));

    /**
     * Categorias
     */

    $form->addElement(new Element\HTML('
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Categorias</h4>
                <div class="options">
                    <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
                </div>
            </div>
            <div class="panel-body collapse in" style="max-height: 500px; width: 100%; overflow-y: scroll;">
    '));

    $arrayCategorias = CategoriaQuery::create()->select(array('Id', 'Nome', 'NrLvl'))->orderByNrLft()->filterByNrLvl(array('min' => 1))->find()->toArray();
    $categories = array();
    foreach ($arrayCategorias as $categoria) {
        $categories[$categoria['Id']] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&minus; ', ($categoria['NrLvl'] - 1) * 1) . $categoria['Nome'];
    }
    $values = ProdutoCategoriaQuery::create()->select(array('CategoriaId'))->filterByProdutoId($object->getId())->find()->toArray();
    $form->addElement(new Element\Checkbox("Selecione as categorias associadas ao produto.", "produto_categoria", $categories, array(
        'value' => $values,
    )));

    $form->addElement(new Element\HTML('
                <!--<a href="#"><i class="icon-plus"></i> Cadastrar nova categoria</a>-->
            </div>
        </div>
    '));
}

$form->addElement(new Element\HTML('
    </div>
    <div class="col-lg-3">
'));

/**
 * Aplicação de desconto
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Desconto</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Select("Aplica desconto de plano de ativação", "produto[APLICA_DESCONTO_PLANO]", ProdutoPeer::getAplicaDescontoPlanoList(), array(
    "value" => $object->getAplicaDescontoPlano()
)));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));


/**
 * Adiciona produto no kit ouro
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Produto requerido no kit ouro</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Select("Adiciona este produto no kit ouro", "produto_variacao[IS_REQUIRED_PRODUCT]",  ProdutoPeer::getAplicaDescontoPlanoList(), array(
    "value" => $object->getProdutoVariacao()->getISRequiredProduct()
)));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

/**
 * É um kit especial onde o cliente seleciona os itens que deseja comprar
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Kit especial seleção de produtos</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Select("Kit especial onde o cliente seleciona itens de compra", "produto[IS_PLANO_SELECAO_ITENS_BY_CLIENTES]",  ProdutoPeer::getIsKitEspecialList(), array(
    "value" => $object->getIsPlanoSelecaoItensByClientes()
)));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

/**
 * Parcelamento Individual
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Parcelamento Individual</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Number("Quantidade máxima de parcelas", "produto[PARCELAMENTO_INDIVIDUAL]", [
    'value' => $object->getParcelamentoIndividual()
]));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

/**
 * Frete Grátis
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Frete Grátis</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Select('Elegível para frete grátis', 'produto[FRETE_GRATIS]', ProdutoPeer::getFreteGratisList(), [
    'value' => $object->getFreteGratis()
]));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Ordenação</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Number('Ordem de exibição do produto', 'produto[ORDEM]', [
    'value' => $object->getOrdem()
]));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

/**
 * Status do produto
 */

$form->addElement(new Element\HTML('
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4>Publicação</h4>
            <div class="options">
                <a href="javascript:;" class="panel-collapse"><i class="icon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
'));

$form->addElement(new Element\Select("Qual tipo de cliente pode visualizar", "produto[TIPO_CLIENTE_VISUALIZACAO]", array('AMBOS' => 'Ambos', 'COMBO' => 'Combo'), array(
    "value" => $object->getTipoClienteVisualizacao(),
    'shortDesc' => 'Tipo do cliente que pode visualizar os produtos, ambos ou somente com combo.'
)));

$form->addElement(new Element\Select("Disponível para venda", "produto_variacao[DISPONIVEL]", ProdutoVariacaoPeer::getValueSet(ProdutoVariacaoPeer::DISPONIVEL), array(
    "value" => $object->getDisponivel(),
    'shortDesc' => 'Selecione "Não" para ocultar o produto do site.'
)));

if (!$getProdutoTaxa) {
    $form->addElement(new Element\Select("Destaque", "produto[DESTAQUE]", ProdutoPeer::getDestaqueList(), array(
        "value" => $object->getDestaque(),
        'shortDesc' => 'Produtos em destaque aparecerão primeiro nas categorias em destaque da página inicial.'
    )));

    $form->addElement(new Element\Select("Deseja atualizar o SEO ao salvar?", "atualizar_seo", array(1 => 'Sim', 0 => 'Não'), array(
        "value" => $container->getRequest()->request->get('atualizar_seo'),
    )));
}
$form->addElement(new Element\HTML('
    </div>
'));

$form->addElement(new Element\HTML('
    <div class="panel-footer">
'));

if (!$object->isNew()) {
    $form->addElement(new Element\Select('Após salvar, desejo...', 'redirectToOnSuccess', array(
        'edit'  => 'continuar editando',
        'new'   => 'adicionar um novo',
        'list'  => 'ir para listagem',
    )));
}

$form->addElement(new Element\SaveButton('Salvar', 'submit', array('class' => 'pull-left')));
if (!$getProdutoTaxa) {
    if (!$object->isNew()) {
        $form->addElement(new Element\Html(sprintf('<a data-action="delete"  data-href="%s" href="javascript:void(0);" class="btn btn-link pull-right"><span class="text-danger"><i class="icon-trash"></i> Deletar</span></a>', delete($_class, $object->getId()))));
    }
}
$form->addElement(new Element\HTML('
        </div>
    </div>
'));

$form->addElement(new Element\HTML('
        </div>
    </div>
'));

$form->addElement(new Element\Hidden('data[ID]', $object->getId()));
//$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->addElement(new Element\HTML('
    <script>
        function liberarLinkComposto(val) {
            if(val == "COMPOSTO"){
                $("#prod-tipo-link").show();
            } else {
                $("#prod-tipo-link").hide();
            }
        }
        $(function() {
            $("#prod-tipo").on("change",function(e) {
                liberarLinkComposto($(this).val());
            });
            
            liberarLinkComposto("' . $object->getTipoProduto() . '");
        });
    </script>
'));

$form->render();
