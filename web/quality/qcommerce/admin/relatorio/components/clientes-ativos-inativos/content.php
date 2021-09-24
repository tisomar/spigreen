<?php

use PFBC\Element;

?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Situação</th>
            <th>Total de Pontos</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Plano</th>
            <th>Data de Cadastro</th>
            <th>Data de Ativação</th>
        </tr>
        </thead>
        <tbody>
        <?php

        /** @var  $object Cliente */
        foreach ($pager as $object) :
            $cliente = ClientePeer::getClienteAtivoMensal($object->getId());
            $pontosPessoais = $object->getControlePontuacaoMes($mesPesquisa, $anoPesquisa)->getPontosPessoais();
            ?>
            <tr>
                <td data-target="Situação">
                    <?= $cliente ? 'Ativo' : 'Inativo'; ?>
                </td>
                <td data-target="Total de Pontos">
                    <?= number_format($pontosPessoais, 0, ',', '.') ?>
                </td>
                <td data-target="Nome">
                    <?= $object->getNomeCompleto(); ?>
                </td>
                <td data-target="E-mail">
                    <?= $object->getEmail(); ?>
                </td>
                <td data-target="Telefone">
                    <?= $object->getTelefone(); ?>
                </td>
                <td data-target="Plano">
                    <?= $object->getPlano()->getNome(); ?>
                </td>
                <td data-target="Data de Cadastro">
                    <?= $object->getCreatedAt('d/m/Y H:i:s'); ?>
                </td>
                <td data-target="Data de Ativação">
                    <?= $object->getDataAtivacao('d/m/Y H:i:s'); ?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($pager->count() == 0) : ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>

<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
