<?php /* @var $usuario Usuario */ ?>
<?php $this->start(); ?>

    <table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
        <tbody>
        <tr>
            <td style="max-width: 528px">

                <h2 style="<?php echo $this->style('h2') ?>">
                    Solicitação de renovação de senha
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    <b>Olá <?php echo $usuario->getNome() ?></span>!</b>
                </p>

                <p style="<?php echo $this->style('p') ?>; word-wrap: break-word;max-width: 528px;">
                    Recebemos uma solicitação de recuperação de senha para o e-mail: <b><?php echo $usuario->getEmail() ?></b>.
                    <br>
                    <br>
                    Para gerar uma nova senha <a href='<?php echo get_url_admin() ?>/secure/confirmation/<?php echo $usuario->getToken() ?>' >clique aqui</a>
                    ou copie e cole a URL abaixo em seu navegador <code><?php echo get_url_admin() ?>/secure/confirmation/<?php echo $usuario->getToken() ?></code>
                    <br>
                    <br>
                    <b>Seu login de aceso é:</b> <?php echo $usuario->getLogin() ?>
                </p>

                <hr style="<?php echo $this->style('hr') ?>">

                <p style="<?php echo $this->style('p') ?>; text-align: center;">
                    Caso não tenha feito esta solicitação, apenas desconsidere esta mensagem.
                </p>

            </td>
        </tr>
        </tbody>
    </table>

<?php
$this->end('content');
$this->extend('mail/_layout');
