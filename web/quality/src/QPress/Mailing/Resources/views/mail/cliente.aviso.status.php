<?php /* @var $cliente Cliente */ ?>

<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>
                <h2 style="<?php echo $this->style('h2') ?>">
                    Olá <?php echo $cliente->getNome() ?>
                </h2>
                
                <p style="<?php echo $this->style('p') ?>">
                    <?php
                    if ($cliente->getStatus() == ClientePeer::STATUS_APROVADO) {
                        echo nl2br(Config::get('cliente.email_aprovado'));
                    } elseif ($cliente->getStatus() == ClientePeer::STATUS_PENDENTE) {
                        echo nl2br(Config::get('cliente.email_pendente'));
                    } elseif ($cliente->getStatus() == ClientePeer::STATUS_REPROVADO) {
                        echo nl2br(Config::get('cliente.email_reprovado'));

                        if (Config::get('cliente.enviar_motivo')) {
                            echo '<br>';
                            echo '<b>Motivo:</b> ' . $cliente->getMotivoReprovacao();
                        }
                    }
                    ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
