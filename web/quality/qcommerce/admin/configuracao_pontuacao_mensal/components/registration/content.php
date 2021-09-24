<?php
/* @var $object ConfiguracaoPontuacaoMensal */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

//$form->addElement(new Element\Textbox("Valor mínimo de compra (R$)", "data[VALOR_COMPRA]", array(
//    "value" => 'R$ ' . format_number($object->getValorCompra()),
//    "required" => true,
//    "class" => 'mask-money',
//)));

/*
* 426 - Ativação Mensal
*
* Adicionado campo para informar o valor em pontos,
* ao invés de informar um valor monetário.
*/
$form->addElement(new Element\Textbox('Valor mínimo de compra (pontos)', 'data[VALOR_PONTOS]', array(
    'value' => $object->getValorPontos(),
    'required' => true,
    'type' => 'number',
)));

$form->addElement(new Element\Textbox("Dia do mês para primeiro aviso:", "data[DIA_AVISO_1]", array(
    "value" => $object->getDiaAviso1(),
)));

$form->addElement(new Element\Textbox("Informação resgate pontos diretos", "data[MENSAGEM_RESGATE_PONTOS_DIRETO]", array(
    "value" => $object->getMensagemResgatePontosDireto(),
)));

$form->addElement(new Element\Textbox("Informação resgate pontos indiretos", "data[MENSAGEM_RESGATE_PONTOS_INDIRETO]", array(
    "value" => $object->getMensagemResgatePontosIndireto(),
)));

$form->addElement(new Element\Textbox("Informação resgate pontos recompra", "data[MENSAGEM_RESGATE_PONTOS_RECOMPRA]", array(
    "value" => $object->getMensagemResgatePontosRecompra(),
)));

$form->addElement(new Element\Select("Tipo do aviso 2", "data[TIPO_AVISO_2]", array('1' => 'Dia fixo', '2' => 'Dia dinâmico'), array(
    "value" => $object->getTipoAviso2(),
    'id' => 'tipo_aviso'
)));

$arrOptTpAviso1 = array(
    1 => 'Último',
    2 => 'Penúltimo',
    3 => 'Antepenúltimo',
);
$tipo = $object->getTipoAviso2();

$form->addElement(new Element\HTML('
        <div id="tipo_aviso_1" style="display: none;">
'));

$form->addElement(new Element\Select("Aviso 2", "data[DIA_AVISO_11]", $arrOptTpAviso1, array(
    "value" => $object->getTipoAviso2() == 1 ? $object->getDiaAviso2() : 1,
)));

$form->addElement(new Element\HTML('
        </div>
'));

$form->addElement(new Element\HTML('
        <div id="tipo_aviso_2" style="display: none;">
'));

$form->addElement(new Element\Textbox("Aviso 2", "data[DIA_AVISO_12]", array(
    "value" => $object->getDiaAviso2(),
)));

$form->addElement(new Element\HTML('
        </div>
'));


$form->addElement(new Element\Textbox("Descrição Extrato (limite 150 caracteres):", "data[DESCRICAO_EXTRATO]", array(
    "value" => $object->getDescricaoExtrato(),
    'minlength' => '1',
    'maxlength' => '150'
)));

$form->addElement(new Element\Textbox("Assunto e-mail de aviso 1", "data[ASSUNTO_AVISO_1]", array(
    "value" => $object->getAssuntoAviso1(),
    'minlength' => '1',
    'maxlength' => '50'
)));


$form->addElement(new Element\Textarea("Descrição do e-mail de Aviso 1:", "data[DESCRICAO_AVISO_1]", array(
    "value" => $object->getDescricaoAviso1(),
    'class' => 'mceEditor'
)));

$form->addElement(new Element\Textbox("Assunto e-mail de aviso 2", "data[ASSUNTO_AVISO_2]", array(
    "value" => $object->getAssuntoAviso2(),
    'minlength' => '1',
    'maxlength' => '50'
)));

$form->addElement(new Element\Textarea("Descrição do e-mail de Aviso 2:", "data[DESCRICAO_AVISO_2]", array(
    "value" => $object->getDescricaoAviso2(),
    'class' => 'mceEditor'
)));




$form->addElement(new Element\SaveButton());



$form->render();
?>

<script>
    $(function() {
        function validateTipo(value) {
            if(value == '1'){

                activeTipo('tipo_aviso_1');
                desactiveTipo('tipo_aviso_2');
            } else if(value == '2'){

                activeTipo('tipo_aviso_2');
                desactiveTipo('tipo_aviso_1');
            }
        }

        function activeTipo(valAc) {

            $('#'+valAc).show()
        }

        function desactiveTipo(valDs) {
            $('#'+valDs).hide();
        }

        validateTipo('<?php echo $tipo; ?>');

        $('#tipo_aviso').on('change', function (e) {
            validateTipo($(this).val());
        })

    });
</script>