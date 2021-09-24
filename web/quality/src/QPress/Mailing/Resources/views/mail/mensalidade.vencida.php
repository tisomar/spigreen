<?php /* @var $cliente Cliente */ ?>

<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Aviso de mensalidade vencida
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Informamos que sua mensalidade venceu no dia <strong><?php echo escape($cliente->getVencimentoMensalidade('d/m/Y')) ?></strong>.<br> 
                    Para voltar a ter acesso a todos os recursos do site, por favor, renove sua mensalidade <a href="<?php echo escape($link) ?>" target="_blank">clicando aqui</a> ou acessando:
                    <br><br><?php echo $link ?>
                </p>
                
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
