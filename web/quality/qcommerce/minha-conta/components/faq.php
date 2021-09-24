
<?php if (isset($busca)) : ?>
    <div id="procuramos">
        Sua busca por "<span><?php echo escape($busca) ?></span>" retornou <?php echo count($objFaqs) ?> resultado(s).
    </div> <!-- /procuramos -->
<?php endif; ?>  


<form class="form-busca search-bar" name="form-busca" action="<?php echo $root_path; ?>/minha-conta/faq" method="get">
    <input autocomplete="off" name="f" id="busca" title="Busque entre as dúvidas abaixo" class="busca-faq ipt ipt-full ipt-small" required type="text" placeholder="Faça uma busca" />
    <button type="submit" class="btn btn-small"><span class="icon-search"></span></button>
</form>
<div>



    <?php if ($objFaqs->count()) : ?>
        <div id="faq_box_perguntas">

            <?php
            $contFaq = 1;
            foreach ($objFaqs as $key => $objFaq) :
                // Formatando numeração do FAQ - mantém com zeros à esquerda caso tenha  um só digito
                $contFaqFormatado = str_pad($contFaq, 2, 0, STR_PAD_LEFT);
                ?>

                <div class="box_pergunta">
                    <div class="pergunta box-primary">
                        <span class="icon-plus-mini"></span>
                        <span class="num"><?php echo $contFaqFormatado; ?></span> - <?php echo escape($objFaq->getPergunta()); ?>
                    </div><!-- pergunta -->
                    <div class="resposta">
                        <?php echo $objFaq->getResposta(); ?>  
                    </div><!-- resposta -->
                </div><!-- box_pergunta -->

                <?php
                $contFaq++;
            endforeach;
            ?>

        </div>

    <?php else : ?>
        <div id="info"> Não existem dúvidas frequentes cadastradas no momento. </div>


    <?php endif; ?>

    <?php include __DIR__ . '/faq_duvida.form.php'; ?>

</div>   
