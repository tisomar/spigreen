<?php
/* @var $object TabelaPreco */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
    'view' => new \PFBC\View\Vertical()
));

$form->addElement(new Element\HTML('
    <div class="row">
        <div class="col-xs-12 ' . ($object->isNew() ? '' : "col-md-6" ) . '">
'));

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Informações da Tabela</h4>
        </div>
        <div class="panel-body">
        '));


$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$form->addElement(new Element\Radio(
    'Defina se esta tabela aplicará um índice de variação nos valores dos produtos.',
    'data[TIPO_OPERACAO]',
    array(
        TabelaPrecoPeer::TIPO_OPERACAO_DESCONTAR => 'Desejo aplicar um <b>desconto</b> nos valores dos produtos.',
        TabelaPrecoPeer::TIPO_OPERACAO_ACRESCENTAR => 'Desejo aplicar um <b>acréscimo</b> nos valores dos produtos.'
    ),
    array(
        'value' => $object->getTipoOperacao(),
    )
));

$form->addElement(new Element\Textbox("Variação padrão (%):", "data[PORCENTAGEM]", array(
    "value" => format_money($object->getPorcentagem()) . '%',
    "class" => "mask-percent-float",
    "required" => true,
)));

$form->addElement(new Element\Radio('
    Ao inserir um novo produto ou modificar o valor de um produto existente, desejo que este valor seja atualizado ' .
    'automaticamente nesta tabela com base no índice padrão definido acima?', 'data[ATUALIZAR_AUTOMATICAMENTE]', array(
    1 => 'Sim, desejo que o sistema gerencie isso.',
    0 => 'Não, vou alterar manualmente os valores dos produtos para esta tabela.'
), array(
    'value' => $object->getAtualizarAutomaticamente(),
)));

$form->addElement(new Element\Textarea("Observação interna:", "data[OBSERVACAO]", array(
    "value" => $object->getObservacao(),
)));

$form->addElement(new Element\HTML('
    </div>
    <div class="panel-footer">
'));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->addElement(new Element\HTML('
    </div>
    </div>
'));


# /first-column
$form->addElement(new Element\HTML('
    </div>
'));

if (!$object->isNew()) {
    $form->addElement(new Element\HTML('
            <div class="col-xs-12 col-md-6">
    '));


    $panel = '

        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Atualização em massa</h4>
            </div>
            <div class="panel-body">
                Desejo atualizar os valores de todos os produtos desta tabela com base na configuração de
                porcentagem, índice padrão, definida.<br>
                <b>' . ($object->getTipoOperacao() == TabelaPrecoPeer::TIPO_OPERACAO_DESCONTAR ? 'DESCONTAR ' : 'ACRESCENTAR ') . '</b>
                <span class="lead"><b>' . format_money($object->getPorcentagem()) . '</b>%</span> do valor dos produtos.
                <br>
                <br>
                <a href="' . get_url_admin() . '/tabela-preco/atualizar-valores-produtos/?id=' . $object->getId() . '" class="btn btn-brown" id="atualizarTudo"><i class="icon-caret-right"></i> Atualizar valores</a>
            </div>
        </div>';

    $form->addElement(new Element\HTML($panel));

    # /second-column
    $form->addElement(new Element\HTML('
        </div>
    '));

    $form->addElement(new Element\HTML('
            <div class="col-xs-12 col-md-6">
    '));


    $panel = '
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>Produtos desta tabela</h4>
            </div>
            <div class="panel-body">
                <a href="' . get_url_admin() . '/tabela-preco-produto/list/?is_filter=true&id=' . $object->getId() . '" class=""><i class="icon-external-link"></i> Clique aqui </a>
                    para visualizar os produtos e seus valores contidos nesta tabela.
            </div>
        </div>';

    $form->addElement(new Element\HTML($panel));
}

# /row
$form->addElement(new Element\HTML('
    </div>
    </div>
'));


$form->render();



?>
<script type="text/javascript">
    $(function() {
        initMaskPercent('.mask-percent-float', {precision: 2});
        $('#atualizarTudo').click(function(e) {
            var $btn = $(this);
            e.preventDefault();
            var link = $(this).attr('href');
            bootbox.confirm("Você tem certeza que deseja atualizar os valores dos produtos desta tabela " +
            "com base no índice de <?php echo format_money($object->getPorcentagem()) ?>%?", function(result) {
                if (result == true) {
                    $btn.attr('disabled', true);
                    setTimeout(function() {
                        $('body').css('cursor', 'wait');
                        $('#blockModalUpdate').modal({backdrop: 'static'});
                        window.location = link;
                    }, 500);

                }
            });
        });
    });
</script>
