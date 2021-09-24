<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Nome Cliente</th>
            <th>Graduação</th>
            <th>Pontos</th>
            <th>Mês</th>
            <th>Ano</th>
            <th>Geração</th>
            <th>Código do Patrocinador</th>
            <th>Próxima Graduação</th>
<!--            <th>Cidade</th>-->
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($pager as $object) : /* @var $object PlanoCarreiraHistorico */
            $cliente = $object->getCliente();
            //var_dump($cliente);exit();
            $planoCarreira = $object->getPlanoCarreira();

            $proximaGraduacao = PlanoCarreiraQuery::create()
                ->filterByNivel($planoCarreira->getNivel() + 1)
                ->findOne();

            if (empty($proximaGraduacao)) :
                $strProximaGraduacao = 'Graduação Máxima';
            else :
                $strProximaGraduacao = $proximaGraduacao->getGraduacao();
            endif;
            ?>
            <tr>
                <td data-title="Nome Cliente">
                    <?= $cliente->getNomeCompleto() ?>
                </td>
                <td data-title="Graduação">
                    <?= $planoCarreira->getGraduacao() ?>
                </td>
                <td data-title="Pontos">
                    <?= $object->getVolumeTotalGrupo() ?>
                </td>
                <td data-title="Mês">
                    <?= $meses[$object->getMes()] ?>
                </td>
                <td data-title="Ano">
                    <?= $object->getAno() ?>
                </td>
                <td data-title="Geração">
                    <?= $cliente->getTreeLevel() . 'º geração' ?>
                </td>
                <td data-title="Código do Patrocinador">
                    <?= $cliente->getChaveIndicacao() ?>
                </td>
                <td data-title="Próxima Graduação">
                    <?= $strProximaGraduacao ?>
                </td>
            </tr>
            <?php
        endforeach;
        if ($pager->count() == 0) :
            ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>
    </table>
</div>

<div class="col-xs-12 pull-right">
    <?php echo $pager->showPaginacao(); ?>
</div>