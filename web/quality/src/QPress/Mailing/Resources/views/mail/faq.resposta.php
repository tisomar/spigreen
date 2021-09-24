<?php $this->start(); ?>
<?php /* @var $faq Faq */ ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Respondemos a sua pergunta
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Segue a resposta para a sua pergunta enviada em <?php echo $faq->getDataPergunta('d/m/Y') ?>:
                </p>

                <p style="<?php echo $this->style('p') ?>">
                    <strong><?php echo $faq->getPergunta() ?></strong>
                    <br>
                    <?php echo nl2br($faq->getResposta()) ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
