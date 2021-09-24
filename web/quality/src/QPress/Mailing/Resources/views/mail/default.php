<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>
                <p style="<?php echo $this->style('p') ?>">
                    <?php echo $conteudo; ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
