<?php /* @var $patrocinador Cliente */ ?>

<?php $this->start(); ?>

    <table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
        <tbody>
        <tr>
            <td>
                <h2 style="<?php echo $this->style('h2') ?>">
                    Dados de seu patrocinador.
                </h2>
                <p style="<?php echo $this->style('p') ?>">
                    <b>Nome:</b> <?php echo escape($patrocinador->getNomeCompleto()) ?><br>
                    <b>E-mail:</b> <?php echo escape($patrocinador->getEmail()) ?><br>
                <?php if ($telefone = $patrocinador->getTelefone()):  ?>
                    <b>Telefone:</b> <?php echo escape($telefone) ?><br>
                <?php endif ?>
                <?php if (($endereco = $patrocinador->getEnderecos()->getFirst()) && ($cidade = $endereco->getCidade()) && ($estado = $cidade->getEstado())):  ?>
                    <b>Cidade:</b> <?php echo sprintf('%s (%s)', escape($cidade->getNome()), escape($estado->getSigla())) ?><br>
                <?php endif ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>

<?php
$this->end('content');
$this->extend('mail/_layout');
