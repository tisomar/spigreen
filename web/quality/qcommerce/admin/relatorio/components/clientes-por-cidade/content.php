
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Código</th>
                <th>Plano</th>
                <th>Telefone</th>
                <th>Data da Ativação</th>
                <th>Cidade</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>

        <?php
        foreach ($pager as $object) : /* @var $object Cliente */
            // $cidades = CidadeQuery::create()
            //     ->select([
            //         'NOME'
            //     ])
            //     ->addAsColumn('NOME', CidadePeer::NOME)
            //     ->useEnderecoQuery()
            //         ->filterByClienteId($object->getId())
            //     ->endUse()
            //     ->orderByNome()
            //     ->groupByNome()
            //     ->find()
            //     ->toArray();
            ?>
            <tr>
                <td data-title="Nome">
                    <?= $object->getNomeCompleto() ?>
                </td>
                <td data-title="Código">
                    <?= $object->getChaveIndicacao() ?>
                </td>
                <td data-title="Plano">
                    <?= $object->getPlano() ? $object->getPlano()->getNome() : '' ?>
                </td>
                <td data-title="Telefone">
                    <?= $object->getTelefone() ?>
                </td>
                <td data-title="Data Ativação">
                    <?= $object->getDataAtivacao('d/m/Y H:i:s') ?>
                </td>
                <!-- <td data-title="Cidade">
                    </?= !empty($cidades) ? implode($cidades, ', ') : '' ?>
                </td> -->
                <td data-title="Cidade">
                    <?= $object->getEnderecoPrincipal()->getCidade()->getNome() ?>
                </td>
                <td data-title="Estado">
                    <?= $object->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla()  ?>
                </td>
            </tr>
            <?php
        endforeach
        ?>

        <?php if ($pager->count() == 0) : ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>

</div>

<div class="col-xs-12">
    <?= $pager->showPaginacao(); ?>
</div>
