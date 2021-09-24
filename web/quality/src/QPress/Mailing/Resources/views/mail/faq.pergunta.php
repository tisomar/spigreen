<?php $this->start(); ?>
<?php /* @var $faq Faq */ ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Nova pergunta recebida
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    <b>Nome:</b> <?php echo $faq->getNome() ?>
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <b>E-mail:</b> <?php echo $faq->getEmail() ?>
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <strong>Pergunta:</strong> <i>"<?php echo $faq->getPergunta() ?>"</i>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
