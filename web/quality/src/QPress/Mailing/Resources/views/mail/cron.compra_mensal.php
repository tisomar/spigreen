<?php $this->start(); ?>
<?php
/* @var $cliente Cliente */
?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    <?php echo $assunto; ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Olá <?php echo $cliente->getNomeCompleto(); ?>,
                    <br />
                    <?php echo $descricao; ?>
                </p>

            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
