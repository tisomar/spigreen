<?php
$tamanho = count($links);
$contador = 0;
$contCaracteresBreadcrumb = 0;

?>
<?php if (count($links)): ?>
    <div class="container">
        <ol itemprop="breadcrumb" class="breadcrumb clearfix">
            <?php foreach ($links as $nome => $url): ?>

                <?php
                // Definindo url de um link
                $urlBreadcrumb = ROOT_PATH . $url;
                // Definindo nome do breadcrumb
                $nomeBreadcrumb = htmlspecialchars($nome);

                // Contador utilizado para saber se deve-se utilizar um separador ou n?o
                $contador++;
                // Contagem de caracteres totais dos nomes dos breadcrumbs
                $contCaracteresBreadcrumb += strlen($nomeBreadcrumb);

                // Parando a cria??o do breadcrumb caso tenha chego ao m?ximo de caracteres permitidos
                if ($contCaracteresBreadcrumb >= BREADCRUMB_LIMITE_CARACTERES):
                    break;
                endif;
                ?>


                <?php if (!empty($url)): ?>
                    <li>
                        <a href="<?php echo $urlBreadcrumb ?>">
                            <?php echo $nomeBreadcrumb ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="active"><?php echo $nomeBreadcrumb ?></li>
                <?php endif ?>

            <?php endforeach ?>

        </ol>
    </div>
<?php endif ?>
