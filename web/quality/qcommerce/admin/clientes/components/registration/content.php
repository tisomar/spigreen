<?php
$statusDescricao = array(
    ClientePeer::STATUS_APROVADO => 'Permite ao cliente efetuar a autenticação no sistema e efetuar compras.',
    ClientePeer::STATUS_PENDENTE => 'Não permite a atutenticação do cliente (normalmente são novos clientes), consequentemente, não permite efetuar compras.',
    ClientePeer::STATUS_REPROVADO => 'Não permite a atutenticação do cliente, consequentemente, não permite efetuar compras.' .
        'Além disso, é possível descrever o motivo na qual o cliente teve o seu cadastro reprovado.',
);

/* @var $object Rede */

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
        <div class="col-xs-12 col-md-6">
'));

$form->addElement(new Element\HTML('
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Dados do cliente</h4>
        </div>
        <div class="panel-body">
        '));


$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$form->addElement(new Element\Textbox("CPF:", "data[CPF]", array(
    "value" => $object->getCPF(),
    "required" => true,
    "class" => "mask-cpf"
)));

$form->addElement(new Element\Textbox("Telefone:", "data[TELEFONE]", array(
    "value" => $object->getTelefone(),
    "required" => true,
    "class" => "mask-telefone"
)));

$form->addElement(new Element\Textbox("Data de Nascimento:", "data[DATA_NASCIMENTO]", array(
    "value" => $object->getDataNascimento('d/m/Y'),
    "required" => true,
    "class" => "mask-date"
)));

$arrList = [
    null => 'Não'
];

foreach ($listCombos as $combo) {
    /** @var $combo Plano */
    $arrList[$combo->getId()] = $combo->getNome();
}

$form->addElement(new Element\Select("Plano", "data[PLANO_ID]", $arrList, array(
    "value" => $object->getPlanoId()
)));

$form->addElement(new Element\Select("Livre de mensalidade", "data[LIVRE_MENSALIDADE]", array('1' => 'Sim', '0' => 'Não'), array(
    "value" => $object->getLivreMensalidade()
)));

$form->addElement(new Element\Select("Sem bonificação (recompra)", "data[NAO_COMPRA]", array('1' => 'Sim', '0' => 'Não'), array(
    "value" => $object->getNaoCompra()
)));

$form->addElement(new Element\Email("E-mail:", "data[EMAIL]", array(
    "value" => $object->getEmail(),
    "required" => true
)));

$form->addElement(new Element\Password("Senha:", "data[SENHA]", array(
    'shortDesc' => 'Deixe em branco para não alterar'
)));

/** VEFIFICA SE É PESSOA JURÍDICA */
$form->addElement(new Element\Hidden('data[REMOVE_CADASTRO_PJ]', 0, array("id" => "REMOVE_CADASTRO_PJ")));
//if ($object->isPessoaJuridica()) {

$form->addElement(new Element\HTML('
    <div class="panel panel-gray" id="dados-juridica">
        <div class="panel-heading">
            <h4>Dados Pessoa Jurídica</h4>
            <button type="button" class="pull-right removeCadastroPJ" style="border:0"> <i class="icon-trash"></i></button>
        </div>
    <div class="panel-body">
'));

$form->addElement(new Element\Textbox("Razão Social:", "data[RAZAO_SOCIAL]", array(
    "value" => $object->getRazaoSocial(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Nome Fantasia:", "data[NOME_FANTASIA]", array(
    "value" => $object->getNomeFantasia(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Inscrição Estadual:", "data[INSCRICAO_ESTADUAL]", array(
    "value" => $object->getInscricaoEstadual(),
// "required" => true
)));

$form->addElement(new Element\Textbox("CNPJ:", "data[CNPJ]", array(
    "value" => $object->getCnpj(),
    "required" => true,
    "class" => "mask-cnpj"
)));

$form->addElement(new Element\HTML('
</div>
'));

$form->addElement(new Element\HTML('
</div>
'));
//}

if ($object->getVago()) {
    $form->addElement(new Element\Select("Cadastro Vago", "data[VAGO]", array('1' => 'Sim', '0' => 'Não'), array(
        "value" => $object->getVago()
    )));
}

$form->addElement(new Element\HTML('
</div>
'));

$form->addElement(new Element\HTML('
</div>
'));


$enderecos = get_contents(__DIR__ . '/dados.endereco.php', array('collEndereco' => $object->getEnderecos()));

$form->addElement(new Element\HTML('
<div class="panel panel-gray">
<div class="panel-heading">
<h4>Endereços cadastrados</h4>
</div>
<div class="panel-body">' . $enderecos . '</div>
</div>'));

# /first-column
$form->addElement(new Element\HTML('
</div>
'));


$form->addElement(new Element\HTML('
<div class="col-xs-12 col-md-6">
'));

switch ($object->getStatus()) {
    case ClientePeer::STATUS_APROVADO:
        $class = 'success';
        break;
    case ClientePeer::STATUS_REPROVADO:
        $class = 'danger';
        break;
    case ClientePeer::STATUS_PENDENTE:
        $class = 'warning';
        break;
    default:
        $class = 'gray';
        break;
}


$panel = '

<div class="panel panel-' . $class . '">
<div class="panel-heading">
<h4>Status do cliente</h4>
</div>
<div class="panel-body">
<div class="col-xs-12 col-sm-6">
<h3>Status Atual</h3>
<h3><span data-toggle="tooltip" data-placement="bottom" title="' . $statusDescricao[$object->getStatus()] . '">' . $object->getStatusLabel() . '</span></h3>
' . ($object->getMotivoReprovacao()
        ? '<p><b>Motivo da reprovação:</b><br><span class="text-danger">' . $object->getMotivoReprovacao() . '</span></p>'
        : '') . '
</div>
<div class="col-xs-12 col-sm-6">
<p>Você pode altera o staus deste cliente clicando na ação desejada abaixo:</p>
<div class="clearfix"></div>
';


$a = '<a class="statusClient btn %s" data-toggle="tooltip" data-placement="bottom" title="%s" href="%s">%s</a>&nbsp;';
if ($object->getStatus() != ClientePeer::STATUS_APROVADO) {
    $url = get_url_admin() . '/clientes/status/?status=' . ClientePeer::STATUS_APROVADO . '&id=' . $_GET['id'];
    $panel .= sprintf($a, 'btn-green', $statusDescricao[ClientePeer::STATUS_APROVADO], $url, '<span class="icon-ok"></span> Aprovar');
}

if ($object->getStatus() != ClientePeer::STATUS_REPROVADO) {
    $urlReprovacao = get_url_admin() . '/clientes/status/?status=' . ClientePeer::STATUS_REPROVADO . '&id=' . $_GET['id'];
    $panel .= sprintf($a, 'btn-danger reprove', $statusDescricao[ClientePeer::STATUS_REPROVADO], $urlReprovacao, '<span class="icon-remove"></span> Reprovar');
}

if ($object->getStatus() != ClientePeer::STATUS_PENDENTE) {
    $url = get_url_admin() . '/clientes/status/?status=' . ClientePeer::STATUS_PENDENTE . '&id=' . $_GET['id'];
    $panel .= sprintf($a, 'btn-warning', $statusDescricao[ClientePeer::STATUS_PENDENTE], $url, '<span class="icon-ban-circle"></span> Bloquear');
}
# /panel e /panel-body
$panel .= '     </div>
</div>

</div>';

$form->addElement(new Element\HTML($panel));

if ($object->getPlano()) {

    $gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);

    $totalPontos = $gerenciador->getTotalPontosDisponiveisParaResgate($object);
    $panel = '
<div class="panel panel-gray">
<div class="panel-heading">
<h4>Pontos</h4>
</div>
<div class="panel-body">
<div class="form-group">

<h3>Total pontos:
<strong>' . number_format($totalPontos, 2, ',', '.') . '</strong></h3>                                        
';


    $panel .= '</div>
<div class="form-group">                            
<div class="form-group">                                           
<div class="row">
<div class="col-xs-12">
<div class="col-xs-4">
<h6 id="transferencia_puntos_titulo" class=" pull-right" for="quantidade_puntos"><b>* Adicionar pontos ao franqueado:</b></h6>
</div>
<div class="col-xs-4">                                                    
<input id="transferencia_puntos_quantidade_puntos" name="transferencia_puntos[QUANTIDADE]"
value="1" type="number" placeholder="0"
class="touch-spin text-center form-control"
min="1" max="<?php echo number_format($totalPontos, 2, \'.\', \'\') ?>"
data-touch-spin-min="1" data-touch-spin-max="<?php echo number_format($totalPontos, 2, \'.\', \'\') ?>"
data-touch-spin-step="0.01" data-touch-spin-decimals="2"
>
</div>
<div class="col-xs-3">
<a id="transferencia_puntos_boton" class="btn btn-success" title="Adicionar pontos ao franqueado" href="javascript:void(0);"  data-action="transferencia_pontos">Adicionar pontos</a>
</div>
</div>
</div>
<div class="row">
<div class="col-xs-12">
<div class="col-xs-3 col-md-offset-3">
<input type="radio" name="transferencia_puntos[TIPO_MOVIMENTO]" value="adicionar" checked> <b>Adicionar</b>
</div>
<div class="col-xs-3">
<input type="radio" name="transferencia_puntos[TIPO_MOVIMENTO]" value="diminuir"> <b>Diminuir</b>
</div>
</div>
</div>
</div>
</div>
</div>';

}

if (Config::get('meio_pagamento.faturamento_direto')) {
    $panel = '
<div class="panel panel-gray">
<div class="panel-heading">
<h4>Opções de parcelamento direto</h4>
</div>
<div class="panel-body">
';
    $form->addElement(new Element\HTML($panel));

    $opcoesPadroes = FaturamentoDiretoQuery::create()
        ->select(array('Id', 'Nome'))
        ->orderByNome()
        ->filterByPadrao(true)
        ->find();

    $hayOpcaoPadrao = array_column($opcoesPadroes->toArray(), 'Nome', 'Id');

    $form->addElement(new Element\Checkbox(
        'Opções de parcelamento padrões do sistema. Disponível para todos os clientes.',
        '',
        $hayOpcaoPadrao,
        array(
            'value' => array_keys($hayOpcaoPadrao),
            'disabled' => 'disabled'
        )
    ));


    $opcoesExclusivas = FaturamentoDiretoQuery::create()
        ->select(array('Id', 'Nome'))
        ->orderByNome()
        ->filterByPadrao(false)
        ->find();

    $hayOpcaoExclusiva = array_column($opcoesExclusivas->toArray(), 'Nome', 'Id');

    $opcoesExclusivasAssociadas = FaturamentoDiretoClienteQuery::create()
        ->select(array('FaturamentoDiretoId'))
        ->filterByClienteId($object->getId())
        ->find();

    $hayOpcaoExclusivaAssociada = $opcoesExclusivasAssociadas->toArray();

    $form->addElement(new Element\Checkbox(
        'Selecione quais opções exclusivas de parcelamento este cliente possuirá:',
        'FATURAMENTO_DIRETO[]',
        $hayOpcaoExclusiva,
        array(
            'value' => $hayOpcaoExclusivaAssociada,
        )
    ));

# /panel e /panel-body
    $panel = '</div>
</div>';

    $form->addElement(new Element\HTML($panel));
}

/** Painel de troca de cadastro. */
//$panel = '<div class="panel panel-gray" id="tipo-cadastro">
//<div class="panel-heading">
//<h4>Tipo de Cadastro</h4>
//</div>
//<div class="panel-body">';
//
//$form->addElement(new Element\HTML($panel));
//$form->addElement(new PFBC\Element\Select("Selecione o tipo de cadastro do cliente", "data[TIPO_CADASTRO]",
//    array("1" => "Pessoa Jurídica", "2" => "Pessoa Física"),
//    array("value" => $object->getCnpj(), "id" => "tipo-cadastro"
//    )));
//
//
//# /panel e /panel-body
//$panel = '</div>
//</div>';
//
//$form->addElement(new Element\HTML($panel));
/** FIM - Painel de troca de cadastro. */
  
if (Config::get('cliente.has_tabela_preco') == 1) {
    $panel = '
<div class="panel panel-gray">
<div class="panel-heading">
<h4>Tabela de Preços</h4>
</div>
<div class="panel-body">
';
    $form->addElement(new Element\HTML($panel));

    $hayTabelaPreco = TabelaPrecoQuery::create()
        ->select(array('Id', 'Nome'))
        ->orderByNome()
        ->find()
        ->toArray();

    $hayTabelaPreco = array('' => 'Selecione...') + array_column($hayTabelaPreco, 'Nome', 'Id');

    $form->addElement(new Element\Select('Selecione a tabela de preços a ser vinculado à este cliente.', 'data[TABELA_PRECO_ID]', $hayTabelaPreco, array(
        'value' => $object->getTabelaPrecoId(),
        'longDesc' => 'A tabela de preços permite ao administrador configurar diferentes valores de produtos em uma determinada ' .
            'tabela. Acima, é possível vincular este cliente à uma tabela com valores diferenciados.'
    )));

# /panel e /panel-body
    $panel = '</div>
</div>';

    $form->addElement(new Element\HTML($panel));
}


$form->addElement(new Element\HTML('
<div class="panel panel-primary">
<div class="panel-body">'));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId(), array("id" => "id_Cliente")));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->addElement(new Element\HTML('
</div>
</div>
'));


# /second-column
$form->addElement(new Element\HTML('
</div>
'));


# /row
$form->addElement(new Element\HTML('
</div>
'));


$form->addElement(new Element\HTML('
</div>
'));


$form->render();

if (isset($urlReprovacao)) {
    ?>
    <script type="text/javascript">
        $(function () {
            function openMotivoReprovacao() {
                bootbox.prompt("Informe o motivo da reprovação do cadastro deste cliente. Ele receberá a descrição do motivo " +
                    "através de um e-mail informando-o que seu cadastro foi reprovado.", function (response) {
                    if (response != null) {
                        if (response == "") {
                            openMotivoReprovacao();
                        } else {
                            window.location = '<?php echo $urlReprovacao ?>&motivo=' + encodeURI(response);
                        }
                    }
                });
            }

            $('.statusClient').click(function (ev) {
                ev.preventDefault();
                var link = $(this);
                bootbox.confirm("Tem certeza de que deseja alterar o status do cliente?", function (response) {
                    if (response == true) {
                        if (link.hasClass('reprove')) {
                            setTimeout(function () {
                                openMotivoReprovacao();
                            }, 500);
                        } else {
                            window.location = link.attr('href');
                        }
                    }
                });
            });

            /*
             * #389
             * Realizada mudança para que quando setado manualmente um plano,
             * seja setado automaticamente o cliente como livre de mensalidade
             */
            $('[name="data[PLANO_ID]"]').change(function () {
                var plano = $(this).val();

                if (plano) {
                    $('[name="data[LIVRE_MENSALIDADE]"').val(1);
                }
            });

        })

        /** Verificação de pessoa física e jurídica */
        $(function () {

            let fieldSocial = $("[name='data[RAZAO_SOCIAL]']").val().length;
            let fieldFantasia = $("[name='data[NOME_FANTASIA]']").val().length ;
            let fieldEstadual = $("[name='data[INSCRICAO_ESTADUAL]']").val().length;
            let fieldCnpj = $("[name='data[CNPJ]']").val().length;

            if (fieldSocial == 0 || fieldFantasia == 0 || fieldEstadual == 0 || fieldCnpj == 0) {
                $("[name='data[RAZAO_SOCIAL]']").prop('required', false);
                $("[name='data[NOME_FANTASIA]']").prop('required', false);
                $("[name='data[CNPJ]']").prop('required', false);
            } else {
                $("[name='data[RAZAO_SOCIAL]']").prop('required', true);
                $("[name='data[NOME_FANTASIA]']").prop('required', true);
                $("[name='data[INSCRICAO_ESTADUAL]']").prop('required', true);
                $("[name='data[CNPJ]']").prop('required', true);
            }
        });


        /** ALTERAR O FORMULÁRIO ENTRE PJ E PF */
        //$(function () {
        //    let isJuridica = "<?//= $object->isPessoaJuridica(); ?>//";
        //
        //    if(isJuridica == 1){
        //        $("#dados-juridica").removeClass('hidden');
        //        $("select[name='data[TIPO_CADASTRO]']").val(1);
        //    }else{
        //        $("select[name='data[TIPO_CADASTRO]']").val(2);
        //    }
        //
        //    $('[name="data[TIPO_CADASTRO]"]').change(function () {
        //        $("#dados-juridica").toggleClass('hidden');
        //    });
        //});

        $('.removeCadastroPJ').click(function() {
            var r = confirm('Você deseja realmente remover esse cadastro de pessoa jurídica?');
            if(r) {
                $("input[name='data[REMOVE_CADASTRO_PJ]']").val(1);
                $('#dados-juridica').attr('hidden', true);
            }
        })
    </script>
    <?php
}
?>


<div class="modal" id="legendas">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Status - Legendas</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled">
                    <li>&raquo; <label class="label label-success big">Aprovado</label>
                        <?php echo $statusDescricao[ClientePeer::STATUS_APROVADO] ?>
                        <hr>
                    </li>
                    <li>&raquo; <label class="label label-warning big">Pendente</label>
                        <?php echo $statusDescricao[ClientePeer::STATUS_PENDENTE] ?>
                        <hr>
                    </li>
                    <li>&raquo; <label class="label label-danger big">Reprovado</label>
                        <?php echo $statusDescricao[ClientePeer::STATUS_REPROVADO] ?>
                        <hr>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

