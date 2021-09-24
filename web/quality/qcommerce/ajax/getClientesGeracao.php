<?php

$clienteLogado = ClientePeer::retrieveByPK($_POST["idClienteLogado"]);
$objClienteRede = ClientePeer::retrieveByPK($_POST["idClienteRede"]);

$data = new Datetime();
$mes = !empty($_POST['mes']) ? $_POST['mes'] : $data->format('n');
$ano = !empty($_POST['ano']) ? $_POST['ano'] : $data->format('Y');

//$data = new Datetime();
//$mes = $data->format('n');
//$ano = $data->format('Y');

$geracaoAnterior = $_POST["geracao"];
$geracaoNova = $geracaoAnterior + 1;

$arrClienteGeracao2 = ClientePeer::getIndicadosDiretosByClienteId($objClienteRede->getId());
//var_dump($arrClienteGeracao2);

$arrNiveis = array();
$sair = false;

$objClienteAnterior = $objClienteRede->getPatrocinadorDireto();
while ($sair == false) {
    if ($objClienteAnterior != null) {
        $arrNiveis[] = $objClienteAnterior;
        if ($objClienteAnterior->getId() == $clienteLogado->getId()) {
            $sair = true;
        } else {
            $objClienteAnterior = $objClienteAnterior->getPatrocinadorDireto();
        }
    } else {
        $sair = true;
    }
}

$arrNiveis = array_reverse($arrNiveis);

$mesAtual = date('m');
$anoAtual = date('Y');
?>


<?php if ($clienteLogado->getId() != $objClienteRede->getId()) : ?>
    <div class="container">
        <ol itemprop="breadcrumb" class="breadcrumb clearfix">
            <?php
            $count = 1;
            foreach ($arrNiveis as $objClienteBreadcrumb) {
                if ($count === 1) {
                    $str = 'Sua Rede';
                } else {
                    $str = 'Geração ' . $count;
                }
                $gen = $count - 1

                ?>
                <li>
                    <a href='#' class='linkRede' data-id='<?= $objClienteBreadcrumb->getId();?>'
                       data-gen="<?= $gen ?>" data-idlogado='<?= $clienteLogado->getId();?>'>
                        <?= $str; ?>
                    </a>
                </li>
                <?php
                $count++;
            }
            ?>
        </ol>
    </div>
<?php endif;?>

<div class="topo text-center hidden-xs">
    <div class="row">
        <div class="col-sm-2">
        <span class="btn-back">
            <?php if ($clienteLogado->getId() != $objClienteRede->getId()) : ?>
              <a href="#" class="linkRede" data-gen="<?= $geracaoAnterior - 1 ?>" data-id="<?= $objClienteRede->getClienteIndicadorDiretoId();?>" data-idlogado="<?= $clienteLogado->getId();?>">
                <i class="fa fa-arrow-left" style="color:#fff"></i>
              </a>
            <?php endif; ?>
        </span>
        </div>
        <div class="col-sm-8">
            <p class="titulo" style="margin: 0 0;"><?= $objClienteRede->getNomeCompleto(); ?></p>
        </div>
        <div class="col-sm-2">
            <div class="btn-load">&nbsp;</div>
        </div>
    </div>
</div>
<div class="content hidden-xs table-responsive">
    <table class="table table-striped table-rede-sized table-geracoes table-condensed">
        <thead>
        <tr>
            <td>Nro. Patrocinador</td>
            <td>Patrocinador</td>
            <td>Plano</td>
            <td class="center">Geração</td>
            <td class="center">Pedidos</td>
            <td>Status</td>
            <td>Graduação</td>
            <td>Pontuação<br>Pessoal</td>
            <td>Total<br>Pontos</td>
        </tr>
        </thead>
        <tbody>
        <?php
        
        foreach ($arrClienteGeracao2 as $cliente) :
            if (strpos($cliente['nome'], 'Cadastro Vago') === false) :
                $clienteObj = ClientePeer::retrieveByPK($cliente['id']);

                $controlePontuacao = $clienteObj->getControlePontuacaoMes($mes, $ano);
                $PP = $controlePontuacao->getPontosPessoais() ?? 0;
                $vml = $controlePontuacao->getPontosTotais() ?? 0;

                $graduacaoAtual = $clienteObj->getPlanoCarreira($mes, $ano);
                $graduacaoAtualDesc = !empty($graduacaoAtual) ? $graduacaoAtual->getPlanoCarreira()->getGraduacao() : 'Sem graduação';

                $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteObj);
                $status = $gerenciador->getStatusAtivacao($mes, $ano) ? 'Ativo' : 'Inativo';

                $plano = $clienteObj->getPlano();
                $planoDesc = !empty($plano) ? $plano->getNome() : 'N/I';

                $linkPedidos = get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $clienteObj->getId();

                if (ClientePeer::getCountIndicadorCliente($clienteObj->getId()) > 0) :
                    $propsFilhos = sprintf(
                        'href="#" class="linkRede" data-gen="%d" data-id="%s" data-idlogado="%s"',
                        $geracaoNova,
                        $cliente['id'],
                        $clienteLogado->getId()
                    );
                    ?>
                    <tr>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= $cliente['nr_patrocinador'] ?>
                            </a>
                        </td>
                        <td class="nome_cliente_filter">
                            <a <?= $propsFilhos ?>>
                                <?= $cliente['nome'] ?>
                                <br>
                                <?= $cliente['email'] ?>
                                <br>
                            </a>
                        </td>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= $planoDesc ?>
                            </a>
                        </td>
                        <td class="center">
                            <a <?= $propsFilhos ?>>
                                G. <?= $geracaoNova ?>
                            </a>
                        </td>
                        <td class="center">
                            <a data-lightbox="iframe" href="<?= $linkPedidos ?>">Pedidos do mês</a>
                        </td>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= $status ?>
                            </a>
                        </td>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= $graduacaoAtualDesc ?>
                            </a>
                        </td>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= $PP ?>
                            </a>
                        </td>
                        <td>
                            <a <?= $propsFilhos ?>>
                                <?= number_format($vml, 0, ',', '.') ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                else :
                    ?>
                    <tr>
                        <td>
                            <?= $cliente['nr_patrocinador'] ?>
                        </td>
                        <td class="nome_cliente_filter">
                            <?= $cliente['nome'] ?>
                            <br>
                            <?= $cliente['email'] ?>
                            <br>
                        </td>
                        <td>
                            <?= $planoDesc ?>
                        </td>
                        <td class="center">
                            G. <?= $geracaoNova ?>
                        </td>
                        <td class="center">
                            <a data-lightbox="iframe" href="<?= $linkPedidos ?>">Pedidos do mês</a>
                        </td>
                        <td>
                            <?= $status ?>
                        </td>
                        <td>
                            <?= $graduacaoAtualDesc ?>
                        </td>
                        <td>
                            <?= $PP ?>
                        </td>
                        <td>
                            <?= number_format($vml, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php
                endif;
            endif;
        endforeach;
        ?>
        </tbody>
    </table>
</div>

<div class="hidden-sm hidden-md hidden-lg">
    <?php
    foreach ($arrClienteGeracao2 as $cliente) :
        if (strpos($cliente['nome'], 'Cadastro Vago') === false) :
            $clienteObj = ClientePeer::retrieveByPK($cliente['id']);

            $controlePontuacao = $clienteObj->getControlePontuacaoMes($mes, $ano);
            $PP = $controlePontuacao->getPontosPessoais() ?? 0;
            $vml = $controlePontuacao->getPontosTotais() ?? 0;

            $graduacaoAtual = $clienteObj->getPlanoCarreira($mes, $ano);
            $graduacaoAtualDesc = !empty($graduacaoAtual) ? $graduacaoAtual->getPlanoCarreira()->getGraduacao() : 'Sem graduação';

            $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $clienteObj);
            $status = $gerenciador->getStatusAtivacao($mes, $ano) ? 'Ativo' : 'Inativo';

            $plano = $clienteObj->getPlano();
            $planoDesc = !empty($plano) ? $plano->getNome() : 'N/I';

            $linkPedidos = get_url_site() . '/minha-conta/visualizar-rede/pedidos?cliente_id=' . $clienteObj->getId();

            if (ClientePeer::getCountIndicadorCliente($clienteObj->getId()) > 0) :
                $propsFilhos = sprintf(
                    'href="#" class="linkRede" data-gen="%d" data-id="%s" data-idlogado="%s"',
                    $geracaoNova,
                    $cliente['id'],
                    $clienteLogado->getId()
                );
                ?>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Nro. Patrocinador: <?= $cliente['nr_patrocinador'] ?></a>
                    </li>
                    <li class="nome_cliente_filter list-group-item">
                        <a <?= $propsFilhos ?>>Nome: <?= $cliente['nome'] ?></a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Email: <?= $cliente['email'] ?></a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Telefone: <?= $cliente['telefone'] ?></a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Plano: <?= $planoDesc  ?></a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Geração <?= $geracaoNova ?></a>
                    </li>
                    <li class="list-group-item">
                        <a data-lightbox="iframe" href="<?= $linkPedidos ?>">Pedidos do mês</a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Status: <?= $status ?></a>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Graduação: <?= $graduacaoAtualDesc ?></a>
                    </li>
                    
                    <li class="list-group-item">
                        Pontuação Pessoal: <?= $PP ?>
                    </li>
                    <li class="list-group-item">
                        <a <?= $propsFilhos ?>>Total de Pontos: <?= number_format($vml, 0, ',', '.') ?></a>
                    </li>
                </ul>
                <?php
            else :
                ?>
                <ul class="list-group">
                    <li class="list-group-item">
                        Nro. Patrocinador: <?= $cliente['nr_patrocinador'] ?>
                    </li>
                    <li class="nome_cliente_filter list-group-item">
                        Nome: <?= $cliente['nome'] ?>
                    </li>
                    <li class="list-group-item">
                        Email: <?= $cliente['email'] ?>
                    </li>
                    <li class="list-group-item">
                        Telefone: <?= $cliente['telefone'] ?>
                    </li>
                    <li class="list-group-item">
                        Plano: <?= $planoDesc ?>
                    </li>
                    <li class="list-group-item">
                        Geração <?= $geracaoNova ?>
                    </li>
                    <li class="list-group-item">
                        <a data-lightbox="iframe" href="<?= $linkPedidos ?>">Pedidos do mês</a>
                    </li>
                    <li class="list-group-item">
                        Status: <?= $status ?>
                    </li>
                    <li class="list-group-item">
                        Graduação: <?= $graduacaoAtualDesc ?>
                    </li>
                    <li class="list-group-item">
                        Pontuação Pessoal: <?= $PP ?>
                    </li>
                    <li class="list-group-item">
                        Total de Pontos: <?= number_format($vml, 0, ',', '.') ?>
                    </li>
                </ul>
                <?php
            endif;
        endif;
    endforeach;
    ?>
</div>

<style>
    .table-geracoes td.center {
        text-align: center;
    }
    .table-geracoes td a {
        font-weight: bold;
    }
</style>
