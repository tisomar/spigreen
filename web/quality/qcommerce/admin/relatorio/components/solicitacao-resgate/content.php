<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Nome Solicitante</th>
            <th>Cpf Solicitante</th>
            <th>E-mail</th>
            <th>Informações Bancarias</th>
            <th>Valor</th>
            <th>Data da Solicitação</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $arrTotalPontos = array();
   
        foreach ($pager as $object) : /* @var $object Resgate */
           $clienteNome = $object->getCliente()->getNome();  
           $clienteCpf = $object->getCliente()->getCpf();  
           $email = $object->getCliente()->getEmail(); 
           ?>
            <tr>
                <td data-title="Nome">
                    <?php echo $clienteNome ?>
                </td>
                <td data-title="E-mail">
                    <?php echo $clienteCpf ?>
                </td>
                <td data-title="E-mail">
                    <?php echo $email ?>
                </td>
                <td data-title="Telefone">
                    <?php echo  '<strong>Banco: </strong> ' . $object->getBanco() . '
                    <br><strong>Agencia: </strong> ' . $object->getAgencia() . '
                    <br><strong>Conta: </strong>' . $object->getConta() . '
                    <br><strong>Cpf Correntista: </strong>' . $object->getCpfCorrentista()  
                    ?>
                </td>
                <td data-title="Inativacao">
                    <?php echo 'R$ ' . number_format($object->getValor(), '2', ',', '') ?>
                </td>
                <td data-title="Inativacao">
                    <?php echo $object->getData('d/m/Y H:i:s') ?>
                </td>
                <td data-title="Inativacao">
                    <?php echo str_replace('NAOEFETUADO', 'NÃO EFETUADO', $object->getSituacao()) ?>
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