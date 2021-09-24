<?php if (!empty($erros)) : ?>
    <div id="caixa_alerta_login">
            <div id="caixa_alerta_titulo_login"><strong>Atenção!</strong> Os seguintes erros foram encontrados:</div>
            <div id="caixa_alerta_descr_login">
                    <ul>
    <?php foreach ($erros as $erro) : ?>                    
                        <li> - <?php echo $erro ?></li>
    <?php endforeach; ?>               
                    </ul>
            </div>
    </div>
<?php endif ?>    
