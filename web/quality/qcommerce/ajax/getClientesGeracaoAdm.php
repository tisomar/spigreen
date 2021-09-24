<?php

$objClienteLogado = ClientePeer::retrieveByPK($_POST["idClienteLogado"]);
$clienteLogado = $objClienteLogado;

$mes = date('m');
$ano = date('Y');

if(!empty($_POST['searchRede'])) {
    $clientePesquisado = ClientePeer::retrieveByPK($_POST['searchRede']);
    $objClienteRede = $clientePesquisado->getClienteRelatedByClienteIndicadorId();
    $arrClienteGeracao2 = ClientePeer::getIndicadosDiretosByClienteId($objClienteRede->getId());
} else{
    $objClienteRede = ClientePeer::retrieveByPK($_POST["idClienteRede"]);
    $arrClienteGeracao2 = ClientePeer::getIndicadosDiretosByClienteId($objClienteRede->getId());
}

$geracaoAnterior = $_POST["geracao"];
$geracaoNova = $geracaoAnterior + 1;

$arrNiveis = array();
$sair = false;

$objClienteAnterior = $objClienteRede->getPatrocinadorDireto();

while ($sair == false) :
    if ($objClienteAnterior != null) :
        $arrNiveis[] = $objClienteAnterior;
        if ($objClienteAnterior->getId() == $objClienteLogado->getId()) :
            $sair = true;
        else:
            $objClienteAnterior = $objClienteAnterior->getPatrocinadorDireto();
        endif;
    else: 
        $sair = true;
    endif;
endwhile;

$arrNiveis = array_reverse($arrNiveis);
?>


<?php if ($objClienteLogado->getId() != $objClienteRede->getId()) : ?>
    <div class="container">
        <ol itemprop="breadcrumb" class="breadcrumb clearfix">
            <?php
            $count = 1;
            foreach ($arrNiveis as $objClienteBreadcrumb) :
                if ($count === 1) :
                    $str = 'Sua Rede';
                else:
                    $str = 'Geração ' . $count;
                endif;
                $gen = $count - 1

                ?>
                <li>
                    <a href='#' class='linkRede' data-id='<?php echo $objClienteBreadcrumb->getId();?>'
                       data-gen="<?php echo $gen ?>" data-idlogado='<?php echo $objClienteLogado->getId();?>'>
                        <?php echo $str; ?>
                    </a>
                </li>
                <?php
                $count++;
            endforeach;
            ?>
        </ol>
    </div><!-- /breadcrumb -->
<?php endif;?>

<div class="topo text-center hidden-xs">
    <div class="row">
        <div class="col-sm-2">
      <span class="btn-back">
            <?php if ($objClienteLogado->getId() != $objClienteRede->getId()) : ?>
              <a href="#" class="linkRede" data-gen="<?php echo $geracaoAnterior - 1 ?>" data-id="<?php echo $objClienteRede->getClienteIndicadorDiretoId();?>" data-idlogado="<?php echo $objClienteLogado->getId();?>">
                <i class="fa fa-arrow-left" style="color:#fff"></i>
              </a>
            <?php endif; ?>
      </span>
        </div>
        <div class="col-sm-8">
            <p class="titulo" style="margin: 0 0;"><h4>Indicador: <?php echo $objClienteRede->getNomeCompleto(); ?></h4> </p>
        </div>
        <div class="col-sm-2">
            <div class="btn-load">&nbsp;</div>
        </div>
    </div>
</div>
<div class="content hidden-xs">
    <table class="table table-striped table-rede-sized">
        <tbody><tr class="header">
            <td class="center">Nro. Patrocinador</td>
            <td>Nome</td>
            <td>E-mail</td>
            <td class="center">Plano</td>
            <td class="center">Geração</td>
            <td class="center">Pedidos</td>
            <td class="center">Cadastro</td>
            <td class="center">Total de Pontos</td>
        </tr>
        <?php foreach ($arrClienteGeracao2 as $cliente) :

         ?>
            <?php if (ClientePeer::getCountIndicadorCliente($cliente['id']) > 0) : ?>
                <tr>
                    <td class="center">
                        <input type="hidden" class="cliente-rede-id" value="<?php echo $cliente['id'] ?>">
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b><?php echo $cliente['nr_patrocinador'] ?></b>
                        </a>
                    </td>
                    <td class="nome_cliente_filter">
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b><?php echo $cliente['nome'] ?></b>
                        </a>
                    </td>
                    <td>
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b><?php echo $cliente['email'] ?></b>
                        </a>
                    </td>
                    <td>
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b><?php echo $cliente['plano'] > 0 ? PlanoPeer::retrieveByPK($cliente['plano'])->getNome() : 'N/I' ?></b>
                        </a>
                    </td>
                    <td class="center">
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b>Geração <?php echo $geracaoNova ?></b>
                        </a>
                    </td>
                    <td>
                        <a class="open-modal2" href="<?php echo get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $cliente['id']  ?>&amp;isLightbox=true">
                            Pedidos mês
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo get_url_admin() . '/clientes/registration?id=' . $cliente['id']  ?>" target="_blank">Cadastro</a>
                    </td>
                    <td class="center">
                        <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                            <b><?= number_format($totalPontos, 0, ',', '.'); ?></b>
                        </a>
                    </td>
                </tr>
            <?php else : ?>
                <tr>
                    <td class="center">
                        <input type="hidden" class="cliente-rede-id" value="<?php echo $cliente['id'] ?>">
                        <?php echo $cliente['nr_patrocinador'] ?>
                    </td>
                    <td class="nome_cliente_filter"><?php echo $cliente['nome'] ?></td>
                    <td><?php echo $cliente['email'] ?></td>
                    <td class="center"><?php echo $cliente['plano'] > 0 ? PlanoPeer::retrieveByPK($cliente['plano'])->getNome() : 'N/I' ?></td>
                    <td class="center">Geração <?php echo $geracaoNova ?></td>
                    <td>
                        <a class="open-modal2" href="<?php echo get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $cliente['id']  ?>&amp;isLightbox=true">
                            Pedidos mês
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo get_url_admin() . '/clientes/registration?id=' . $cliente['id']  ?>" target="_blank">Cadastro</a>
                    </td>
                    <td class="center">
                        <b><?= number_format($totalPontos, 0, ',', '.'); ?></b>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>


<div class="hidden-sm hidden-md hidden-lg">

    <?php foreach ($arrClienteGeracao2 as $cliente) :

    ?>
        <?php if (ClientePeer::getCountIndicadorCliente($cliente['id']) > 0) :?>
            <ul class="list-group">
                <li class="list-group-item">
                    <input type="hidden" class="cliente-rede-id" value="<?php echo $cliente['id'] ?>">
                    <a href="#" class="linkRede" data-gen="<?php echo $geracaoNova ?>" data-id="<?php echo $cliente['id'] ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">Nro. Patrocinador:
                        <?php echo $cliente['nr_patrocinador'] ?></a>
                </li>
                <li class="nome_cliente_filter list-group-item">
                    <a href="#" class="linkRede" data-gen="<?php echo $geracaoNova ?>" data-id="<?php echo $cliente['id'] ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">Nome:
                        <?php echo $cliente['nome'] ?></a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="linkRede" data-gen="<?php echo $geracaoNova ?>" data-id="<?php echo $cliente['id'] ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">Email:
                        <?php echo $cliente['email'] ?></a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="linkRede" data-gen="<?php echo $geracaoNova ?>" data-id="<?php echo $cliente['id'] ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">Plano:
                        <?php echo $cliente['plano'] > 0 ? PlanoPeer::retrieveByPK($cliente['plano'])->getNome() : 'N/I'; ?></a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="linkRede" data-gen="<?php echo $geracaoNova ?>" data-id="<?php echo $cliente['id'] ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                        Geração <?php echo $geracaoNova ?></a>
                </li>

                <li class="list-group-item">
                    <a class="open-modal2" href="<?php echo get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $cliente['id']  ?>&amp;isLightbox=true">
                        Pedidos mês
                    </a>
<!--                    <a href="javascript:void(0)" id="modal-pedidos"-->
<!--                            data-id="--><?php //echo $cliente['id'] ?><!--" >-->
<!--                       -->
<!--                    </a>-->
                </li>
                <li class="list-group-item">
                    <a href="" target="_blank">Cadastro</a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="linkRede" data-id="<?php echo $cliente['id'] ?>" data-gen="<?php echo $geracaoNova ?>" data-idlogado="<?php echo $clienteLogado->getId() ?>">
                        <b><?= number_format($totalPontos, 0, ',', '.'); ?></b>
                    </a>
                </li>
            </ul>
        <?php else : ?>
            <ul class="list-group">
                <li class="list-group-item">
                    <input type="hidden" class="cliente-rede-id" value="<?php echo $cliente['id'] ?>">
                    Nro. Patrocinador: <?php echo $cliente['nr_patrocinador'] ?>
                </li>
                <li class="nome_cliente_filter list-group-item">
                    Nome: <?php echo $cliente['nome'] ?>
                </li>
                <li class="list-group-item">
                    Email: <?php echo $cliente['email'] ?>
                </li>
                <li class="list-group-item">
                    Plano: <?php echo $cliente['plano'] > 0 ? PlanoPeer::retrieveByPK($cliente['plano'])->getNome() : 'N/I'; ?>
                </li>
                <li class="list-group-item">
                    Geração <?php echo $geracaoNova ?>
                </li>

                <li class="list-group-item">
                    <a class="open-modal2" href="<?php echo get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $cliente['id']  ?>&amp;isLightbox=true">
                        Pedidos mês
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="<?php echo get_url_admin() . '/clientes/registration?id=' . $cliente['id']  ?>" target="_blank">Cadastro</a>
                </li>
                <li class="list-group-item">
                    <b><?= number_format($totalPontos, 0, ',', '.'); ?></b>
                </li>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
