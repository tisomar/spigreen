<?php
/* @var $object BaseRetiradaLoja */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
));

$form->addElement(new Element\Textbox("Nome da Loja:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true,
)));

$form->addElement(new Element\Radio("Disponível?", "data[HABILITADO]", array(
    1 => 'Sim', 0 => 'Não'
), array(
    "value" => $object->getHabilitado(),
    "required" => true,
)));

$form->addElement(new Element\Textbox("Telefone:", "data[TELEFONE]", array(
    "value" => $object->getTelefone(),
    "required" => true,
    "class" => "mask-tel",
)));

$form->addElement(new Element\Textarea("Endereço:", "data[ENDERECO]", array(
    "value" => $object->getEndereco(),
    "required" => true,
)));

$cidade = $object->getCidade();
$estado = !is_null($cidade) ? $cidade->getEstado() : null;

$cidadeId = !is_null($cidade) ? $cidade->getId() : null;
$estadoId = !is_null($estado) ? $estado->getId() : null;

$centroDistribuicaoId = !is_null($object->getCentroDistribuicaoId()) ? $object->getCentroDistribuicaoId() : null;

$estadosQuery = EstadoQuery::create()
    ->orderByNome()
    ->find();

$estados = [];
foreach ($estadosQuery as $obj) : /* @var $obj Estado */
    $estados[$obj->getId()] = $obj->getNome();
endforeach;

$form->addElement(new Element\Select("Estado:", "data[ESTADO_ID]", $estados, array(
    "value" => $estadoId,
    "required" => true,
    "id" => "optionEstado"
)));

$cidades = [];

reset($estados);

if ($estadoId = ($estadoId ?? key($estados) ?? false)) :
    $cidadesQuery = CidadeQuery::create()
        ->filterByEstadoId($estadoId)
        ->orderByNome()
        ->find();

    foreach ($cidadesQuery as $obj) : /* @var $obj Cidade */
        $cidades[$obj->getId()] = $obj->getNome();
    endforeach;
endif;

$form->addElement(new Element\Select("Cidade:", "data[CIDADE_ID]", $cidades, array(
    "value" => $cidadeId,
    "required" => true,
    "id" => "optionCidade"
)));

$form->addElement(new Element\Textbox("Valor cobrado para retirada (R$)", "data[VALOR]", array(
    "value" => 'R$ ' . format_money($object->getValor()),
    "required" => true,
    "class" => "mask-money",
)));

$form->addElement(new Element\Textbox("Prazo para retirada (dias úteis)", "data[PRAZO]", array(
    "value" => $object->getPrazo(),
    "required" => true,
)));

$centroDistribuicaoQuery = CentroDistribuicaoQuery::create()->filterByStatus(1)->find();

$centrodeDistribuicao = [];
foreach ($centroDistribuicaoQuery as $obj) : /* @var $obj Estado */
    $centrodeDistribuicao[$obj->getId()] = $obj->getDescricao();
endforeach;

$form->addElement(new Element\Select("Centro de distribuição:", "data[CENTRO_DISTRIBUICAO_ID]", $centrodeDistribuicao, array(
    "value" => $centroDistribuicaoId,
    "required" => true,
    "id" => "optionCentroDistribuicao"
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();

?>

<script>
    $(function() {
        $('#optionEstado').on('change', function() {
            let estado_id = $(this).val();

            $.get(window.root_path + '/ajax/ajax_estado_cidade_registration', {
                estado_id: estado_id
            },
            function(data) {
                $('#optionCidade').html(data);
            });
        });
    });
</script>
