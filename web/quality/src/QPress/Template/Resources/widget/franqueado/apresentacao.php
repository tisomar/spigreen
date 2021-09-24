<?php
/* @var $franqueado Hotsite */

$linkDistribuidor = '';

try {
    $linkDistribuidor = get_url_site()
        . '/home/validPatrocinador?codigo_patrocinador='
        . $franqueado->getCliente()->getChaveIndicacao();
} catch (PropelException $e) {
}
?>
<section>
    <div class="bg-banner-franqueado banner-full" style="background: #F0F0F0 50% 50%; background-size: cover;">
        <div class="container">
            <div class="row franqueado">
                <div class="visible-xs">
                    <div class="col-xs-12">
                        <?php
                        echo $franqueado->getThumb(
                            'width=155&height=124&cropratio=1.25:1',
                            ['class' => 'center-block img-responsive']
                        )
                        ?>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-7 descricao">
                    <?= $franqueado->getDescricao() ?>

                    <div class="tit">
                        <?= $franqueado->getNome() ?>
                        <br>
                        <small class="muted"><?= $franqueado->getEmail() ?></small>
                    </div>
                </div>
                <div class="hidden-xs col-sm-5">
                    <?php $foto = asset('/arquivos/hotsite/') . $franqueado->getFoto() ?>
                    <img class="center-block img-responsive" width="300" height="300" src="<?= $foto ?>" alt="">
                    <div class="center-block text-center">
                        <a class="btn btn-theme" href="<?= $linkDistribuidor ?>">
                            Torne-se um distribuidor
                        </a>
                    </div>
                </div>

                <div class="visible-xs center-block text-center">
                    <a class="btn btn-theme" href="<?= $linkDistribuidor ?>">
                        Torne-se um distribuidor
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>