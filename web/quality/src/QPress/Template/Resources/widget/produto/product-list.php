<?php

use QPress\Template\Widget;

if ($collProdutos->count() > 0) :

    $numeroColunas = isset($numeroColunas) ? $numeroColunas : 4;
    $carousel = isset($carousel) && $carousel;
    $headingContainer = isset($headingContainer) && is_array($headingContainer) ? $headingContainer : false;

    ?>
    <section>
        <?php
        if ($headingContainer) :

            if ($carousel) :
                Widget::render('general/heading-container', $headingContainer);
            else :
                ?>
                <div class="container">
                    <?php
                    Widget::render('general/box-title', $headingContainer);
                    ?>
                </div>
                <?php
            endif;
        endif;

        if ($carousel) :
            ?>
            <div class="product-list product-list-carousel">
                <div class="container">
                    <div class="carousel-products owl-carousel owl-theme">
                        <?php foreach ($collProdutos as $objProduto) : /* @var $objProduto Produto */ ?>
                            <div class="item">
                            <?php Widget::render('produto/product', array('objProduto' => $objProduto, 'carousel' => $carousel)); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        else :
            if ($headingContainer) :
                ?>
                <div class="container">
                <?php
            endif;
            ?>

            <div class="container-product-list">
                <div class="product-list clearfix">
                    <?php
                    foreach ($collProdutos as $objProduto) :
                        Widget::render('produto/product', array('objProduto' => $objProduto, 'numeroColunas' => $numeroColunas));
                    endforeach;
                    ?>
                    <div class="clearfix"></div>

                    <?php
                    if (!$collProdutos instanceof ArrayObject && !$collProdutos instanceof PropelCollection) :
                        echo '<br>';
                        Widget::render('components/pagination', array(
                            'pager' => $collProdutos,
                            'href' => isset($url) ? $url : '',
                            'queryString' => isset($queryString) ? $queryString : '',
                            'align' => 'center'
                        ));
                    endif;
                    ?>
                </div>
            </div>

            <?php

            if ($headingContainer) :
                ?>
                </div>
                <?php
            endif;
        endif;
        ?>
    </section>
    <?php
endif;
