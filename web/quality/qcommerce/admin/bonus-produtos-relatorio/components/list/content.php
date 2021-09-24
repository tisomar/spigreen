<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Graduação</th>
            <th>Telefone</th>
            <th>Endereço</th>
            <th>Descrição</th>
            <th>Total bonificação</th>
            <th>Data envio</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) : /* @var $object DistribuicaoCliente */
            $cep = $object->getCliente()->getEnderecoPrincipal()->getCep();
            $logradouro = $object->getCliente()->getEnderecoPrincipal()->getLogradouro();
            $numero = $object->getCliente()->getEnderecoPrincipal()->getNumero();
            $bairro = $object->getCliente()->getEnderecoPrincipal()->getBairro();
            $cidade = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getNome();
            $estadoNome = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getNome();
            $estadoCigla = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla();
            $complemento = $object->getCliente()->getEnderecoPrincipal()->getComplemento() != null ? '<br>Complemento: ' . $object->getCliente()->getEnderecoPrincipal()->getComplemento() : '';

            ?>
            <tr>
                <td data-title="Nome"><?= $object->getCliente()->getNomeCompleto() ?></td>
                <td data-title="Graduacao"><?= $object->getGraduacao() ?></td>
                <td data-title="Telefone"><?= $object->getCliente()->getTelefone() ?></td>
                <td data-title="Endereço">
                    <?= 
                        'CEP: ' . $cep .
                        '<br>Estado: ' .  $estadoNome . '/' .  $estadoCigla .
                        '<br>Cidade: ' . $cidade . 
                        "<br>Logradouro: " . $logradouro .
                        '<br>Bairro: ' . $bairro .
                        '<br>Numero: ' .  $numero .
                        $complemento;
                    ?>
                </td>
                <td data-title="BonusAcumulados"><?= $object->getObservacao() ?></td>
                <td data-title="TotalBonificacao">R$ <?= number_format($object->getValorTotalBonificacao(), '2', ',', '.') ?></td>
                <td data-title="DataEnvio"><?=  $object->getDataRetirada() ? date('d/m/Y', strtotime($object->getDataRetirada())) : 'Entrega Pendente' ?></td>
            </tr>
        <?php
        endforeach;

        if (!$pager->count()) :
            ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>

    </table>
</div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
