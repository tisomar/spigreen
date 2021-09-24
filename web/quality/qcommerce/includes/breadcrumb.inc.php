<?php

if (true) {
    echo '<pre><br><br><br><br><br>';
    print_r($links);

    die(
        "<br>Alterar para utilizar o template:<br>Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Identificação' => '')));"
    );
}

$tamanho = count($links);
$contador = 0;
$contCaracteresBreadcrumb = 0;

?>
<?php if (count($links)) : ?>
<nav class="breadcrumb">

    <div class="container">
        <ol itemprop="breadcrumb">
            <?php foreach ($links as $nome => $url) : ?>
                <?php
                // Definindo url de um link
                $urlBreadcrumb = ROOT_PATH . $url;
                // Definindo nome do breadcrumb
                $nomeBreadcrumb = escape($nome);

                // Contador utilizado para saber se deve-se utilizar um separador ou n?o
                $contador++;
                // Contagem de caracteres totais dos nomes dos breadcrumbs
                $contCaracteresBreadcrumb += strlen($nomeBreadcrumb);

                // Parando a cria??o do breadcrumb caso tenha chego ao m?ximo de caracteres permitidos
                if ($contCaracteresBreadcrumb >= BREADCRUMB_LIMITE_CARACTERES) :
                    break;
                endif;
                ?>


                <?php if (!empty($url)) : ?>
                    <li>
                        <a href="<?php echo $urlBreadcrumb ?>" title="<?php echo $nomeBreadcrumb ?>">
                            <?php echo $nomeBreadcrumb ?>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="active"><?php echo $nomeBreadcrumb ?></li>
                <?php endif ?>

            <?php endforeach ?>

        </ol>
    </div>
</nav>
<?php endif ?>
