<?php

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Nome:", "data[Nome]", array(
    "value" => $object->getNome(),
    "shortDesc" => 'O nome acima aparecerá na página de detalhes do produto.',
    "placeholder" => "Ex.: Você poderá gostar também..."
)));


$options = array(
    '' => 'Escolha o tipo de associação...',
    'produto.relacionado' => 'Produtos relacionados',
    'venda.cruzada' => 'Venda Cruzada',
);

$optionsDescription = array(
    '' => '<b>Opções disponíveis:</b>',
    'produto.relacionado' => '<b>Produtos Relacionados:</b><br><i>- São produtos que possuam qualquer tipo relação com o produto em questão, seja através de complemento ou de semelhança.</i>',
    'venda.cruzada' => '<b>Venda Cruzada:</b><br><i>- São produtos que poderão ser adicionados ao carrinho juntamente com o produto em questão. A relação pode ser através de complementos ao produto principal.</i>',
);

$form->addElement(new Element\Select("Tipo de associação:", "data[Type]", $options, array(
    "value" => $object->getType(),
    "required" => true,
    "shortDesc" => implode('<br>', $optionsDescription),
)));

$form->addElement(new Element\Number("Ordem:", "data[Ordem]", array(
    "value" => $object->getOrdem(),
    "required" => true
)));

$options = array(
    true => 'Sim',
    false => 'Não',
);

$form->addElement(new Element\Radio("Disponível?", "data[Disponivel]", $options, array(
    "value" => $object->getDisponivel(),
    "required" => true,
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('data[ProdutoOrigemId]', $reference));
$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

?>

<?php include __DIR__ . '/../../config/menu.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-gray">
            <div class="panel-heading">
                <h4>
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="<?php echo $config['routes']['list'] ?>"><i class="icon-list"></i> Associações criadas</a>
                        </li>
                        <li class="active">
                            <a href="javascript:void(0)"><i class="icon-plus-sign"></i> Nova associação</a>
                        </li>
                    </ul>
                </h4>
            </div>
            <div class="panel-body">
                <?php $form->render(); ?>
            </div>
        </div>
    </div>
</div>
</div>





