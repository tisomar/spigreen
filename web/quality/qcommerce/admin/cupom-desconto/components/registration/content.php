<?php
/* @var $object Cupom */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Textbox("Cupom:", "data[CUPOM]", array(
    "value" => $object->getCupom(),
    "required" => true
)));

$form->addElement(new Element\Radio("Tipo de desconto", "data[TIPO_DESCONTO]", CupomPeer::getTipoDescontoList(), array(
    "value" => $object->getTipoDesconto(),
    "required" => true,
    "class" => 'tipo_desconto',
)));

$form->addElement(new Element\Radio("Tipo de clientes para uso", "data[MODELO_CLIENTE_USO]", CupomPeer::getModeloUsoCupomList(), array(
    "value" => $object->getModeloClienteUso(),
    "required" => true,
    "class" => 'tipo_cliente',
)));

$clientsList = ClienteQuery::create()
                ->select(array('Id','nome_cpf'))
                ->withColumn('concat(NOME, " - ", CPF, " - ", EMAIL)', 'nome_cpf')
                ->find()
                ->toArray();

$form->addElement(new Element\Select("Cliente(s):", "CLIENTES[]", array_column($clientsList, 'nome_cpf','Id'), array(
    "value" => $object->getClientes(),
    'class' => 'client_list',
    'multiple' => true,
    'disabled' => 'disabled'

)));


$form->addElement(new Element\Textbox("Valor de desconto", "data[VALOR_DESCONTO]", array(
    "value" => $object->getValorDescontoFormatado(),
    "required" => true,
    "id" => 'valor_desconto'

)));

$form->addElement(new Element\HTML(
    '<div class="form-group">
        <label class="col-sm-3 control-label">Data Início</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" name="data[DATA_INICIAL]" value="' . $object->getDataInicial('d/m/Y') . '" class="form-control datepicker-today mask-date">
        </div>
    </div>
</div>'));

$form->addElement(new Element\HTML(
    '<div class="form-group">
        <label class="col-sm-3 control-label">Data Validade</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" name="data[DATA_FINAL]" value="' . $object->getDataFinal('d/m/Y') . '" class="form-control datepicker-today mask-date">
        </div>
    </div>
</div>'));

$form->addElement(new Element\Textbox("Para compras acima de", "data[VALOR_MINIMO_CARRINHO]", array(
    "value" => 'R$ ' . format_money($object->getValorMinimoCarrinho()),
    "required" => true,
    "class" => "mask-money"
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false)
{
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
?>


<script>
    $(function() {
        showFieldByTypeDiscount('<?php echo ($object->getTipoDesconto()) ?>');
        $('.tipo_desconto').on('change', function() {
            showFieldByTypeDiscount($(this).val());
            $('#valor_desconto').val(0).focus();
        });
        $('body').on('change', 'input[name="data[MODELO_CLIENTE_USO]"]',function(e) {
            getActiveClientesSelect($(this).val());
        });

        getActiveClientesSelect("<?php echo $object->getModeloClienteUso()?>");
        $('.client_list').select2();
        addValueList("<?php echo $object->getClientes() ?>")
    });
    function showFieldByTypeDiscount(type) {
        if (type == '<?php echo CupomPeer::TIPO_DESCONTO_REAIS ?>') {
            $('#valor_desconto').addClass('mask-money').removeAttr('maxLength');
            initMaskMoney();
        } else {
            $('#valor_desconto').removeClass('mask-money').addClass('mask-percent').attr('maxLength', 3);
            initMaskPercent();
        }
    }

    function getActiveClientesSelect(type) {
        if (type == '<?php echo CupomPeer::CUPOM_MODEL_ALL ?>'
            || type == '<?php echo CupomPeer::CUPOM_MODEL_FINAL_COSTUMER ?>'
            || type == '<?php echo CupomPeer::CUPOM_MODEL_DISTRIBUTOR ?>'
        ) {
            $('.client_list').attr('disabled', 'disabled');

        } else {
            $('.client_list').removeAttr('disabled');
        }
    }

    function addValueList(value){

        $('.client_list').val(value.split(',')).trigger('change')
    }


</script>

