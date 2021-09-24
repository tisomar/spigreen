<?php /* @var $cliente Cliente */ ?>

<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>
                <h2 style="<?php echo $this->style('h2') ?>">
                    Olá <?php echo $cliente->getNome() ?>!
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    <b>Seu cadastro foi efetuado com sucesso!</b>
                </p>

                <p style="<?php echo $this->style('p') ?>">
                    Para iniciar o processo de redefinição de senha, clique no link abaixo: 
                    <br />

                    <a href='<?php echo get_url_site() . '/login/nova-senha/' . $cliente->getRecuperacaoSenhaToken() ?>'>
                        <?php echo get_url_site() . '/login/nova-senha/' . $cliente->getRecuperacaoSenhaToken() ?>
                    </a> 
                    <br /><br />

                    Se ao clicar no link acima não funcionar, copie e cole o URL em uma nova janela do navegador. <br /><br />

                    Se você recebeu esta mensagem por engano, é provável que outro usuário tenha inserido <br />
                    seu endereço de e-mail por engano ao tentar redefinir uma senha. Se você não iniciar a <br />
                    solicitação, você não precisa tomar nenhuma providência e pode seguramente desconsiderar <br />
                    este e-mail.
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
