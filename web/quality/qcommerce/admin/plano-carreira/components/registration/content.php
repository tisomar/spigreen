<?php

use PFBC\Form;
use PFBC\Element;

/** @var $object PlanoCarreira */

$graduacoesQuery = PlanoCarreiraQuery::create()
    ->orderByNivel()
    ->find();

$graduacoes = [
    '' => '--'
];

foreach ($graduacoesQuery as $graduacao) : /** @var $graduacao PlanoCarreira */
    $graduacoes[$graduacao->getId()] = $graduacao->getGraduacao();
endforeach;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
    'enctype' => 'multipart/form-data'
));

$form->addElement(new Element\Textbox('Nome', 'data[GRADUACAO]', [
    'value' => $object->getGraduacao(),
    'required' => true
]));

$form->addElement(new Element\Number('Nível', 'data[NIVEL]', [
    'value' => $object->getNivel(),
    'required' => true,
    'min'   => 1
]));

$form->addElement(new Element\Number('Pontos', 'data[PONTOS]', [
    'value' => $object->getPontos(),
    'required' => true,
    'min'   => 0
]));

// UPLOAD DE IMAGEM DO AVATAR
$form->addElement(new Element\HTML('
    <div class="form-group">
        <label class="col-sm-3 control-label" for="registrer-element-1">
        Imagem Avatar</label>
        <div class="col-sm-6">' .
            $object->getImagem('width=500&height=50&cropratio=10:1', array(
                'class' => 'thumbnail img-responsive',
                'style' => 'background: #555',
            )) .
            '<input type="file" id="avatar" name="avatar" value="Alterar Foto"> <br>
        </div>
    </div>
'));

// UPLOAD DE IMAGEM DO BANNER
$form->addElement(new Element\HTML('
    <div class="form-group">
        <label class="col-sm-3 control-label" for="registrer-element-1">
        Imagem Banner</label>
        <div class="col-sm-6">' .
            $object->getBannerGraduacao('width=500&height=50&cropratio=10:1', array(
                'class' => 'thumbnail img-responsive',
                'style' => 'background: #555',
            )) .
            '<input type="file" id="bannerGraduacao" name="bannerGraduacao" value="Alterar Banner"> <br>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Aproveitamento por Linha', 'data[APROVEITAMENTO_LINHA]', [
    'value' => $object->getAproveitamentoLinha(),
    'required' => true,
    'min'   => 0
]));

// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Requisitos</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Requisitos</h4>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Quantidade', 'data[REQU_QUANTIDADE]', [
    'value' => $object->getRequQuantidade(),
    'min'   => 0
]));

$form->addElement(new Element\Select('Graduação mínima', 'data[REQU_GRADUACAO]', $graduacoes, [
    'value' => $object->getRequGraduacao()
]));

$form->addElement(new Element\Radio(
    'Filhos diretos?',
    'data[REQU_DIRETO]',
    [
        '0' => 'Não',
        '1' => 'Sim'
    ],
    [
        'value' => $object->getRequDireto() ?? 0
    ]
));

// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Bônus de Liderança</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Bônus de Liderança</h4>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Percentual', 'data[PERC_BONUS_LIDERANCA]', [
    'value' => $object->getPercBonusLideranca(),
    'min'   => 0,
    'max'   => 100
]));

// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Bônus de Desempenho</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Bônus de Desempenho</h4>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Percentual', 'data[PERC_BONUS_DESEMPENHO]', [
    'value' => $object->getPercBonusDesempenho(),
    'min'   => 0,
    'max'   => 100
]));


// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Bônus Aceleração</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Bônus Aceleração</h4>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Valor primeiro período', 'data[VALOR_BONUS_ACELERACAO_PRIMEIRO_PERIODO]', [
    'value' => $object->getValorBonusAceleracaoPrimeiroPeriodo(),
    'min'   => 0,
]));

$form->addElement(new Element\Number('Valor segundo período', 'data[VALOR_BONUS_ACELERACAO_SEGUNDO_PERIODO]', [
    'value' => $object->getValorBonusAceleracaoSegundoPeriodo(),
    'min'   => 0,
]));


// Requisitos
$form->addElement(new Element\HTML('
    <div class="form-group">
        <div class="hidden-xs col-xs-3 col-xs-pull-9 text-right">
            <h4>Bônus Produtos</h4>
        </div>
        
        <div class="visible-xs col-xs-12">
            <h4>Bônus Produtos</h4>
        </div>
    </div>
'));

$form->addElement(new Element\Number('Valor premiação', 'data[VALOR_PREMIO_BONUS_PRODUTOS]', [
    'value' => $object->getValorPremioBonusProdutos(),
    'min'   => 0,
    'step' => "0.01"
]));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();

?>

<script>
    $('[name="data[REQU_QUANTIDADE]"]').on('blur', function(e) {
        $('[name="data[REQU_GRADUACAO]"]').attr('required', !!e.target.value);
    });

    $(function() {
        $('[name="data[REQU_GRADUACAO]"]').attr('required', !!$('[name="data[REQU_QUANTIDADE]"]').val());
    });
</script>
