<?php

$disabled = is_null($reference) ? 'disabled' : '';
$msgAviso = $disabled ? 'Você precisa cadastrar os dados iniciais do produto antes de inserir as demais informações como fotos, variações, etc...' : '';

$getTaxa = isset($getProdutoTaxa) && $getProdutoTaxa ? true : false;

if ($getTaxa) {
    $links = array(
        'produtos' => array(
            'name'      => 'Produto',
            'href'      => sprintf('/produtos/registration/?id=%s', $reference),
            'disabled'  => '',
        ),
        'pmidia' => array(
            'name'      => 'Fotos',
            'href'      => sprintf('/pmidia/list/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
    );
} else {
    $links = array(
        'produtos' => array(
            'name'      => 'Produto',
            'href'      => sprintf('/produtos/registration/?id=%s', $reference),
            'disabled'  => '',
        ),
        'pmidia' => array(
            'name'      => 'Fotos',
            'href'      => sprintf('/pmidia/list/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'produto-composto' => array(
            'name'      => 'Produto Composto',
            'href'      => sprintf('/produto-composto/registration/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'produto-atributos' => array(
            'name'      => 'Atributos',
            'href'      => sprintf('/produto-atributos/list/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'produto-variacoes' => array(
            'name'      => 'Variações',
            'href'      => sprintf('/produto-variacoes/list/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'produto-associacao' => array(
            'name'      => 'Associações',
            'href'      => sprintf('/produto-associacao/list/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'google-shopping' => array(
            'name'      => 'Google Shopping',
            'href'      => sprintf('/google-shopping/registration/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'buscape-company' => array(
            'name'      => 'Buscapé Company',
            'href'      => sprintf('/buscape-company/registration/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
        'uol-shopping' => array(
            'name'      => 'UOL Shopping',
            'href'      => sprintf('/uol-shopping/registration/?context=%s&reference=%s', $context, $reference),
            'disabled'  => $disabled,
        ),
    );
}

?>

<ul class="nav nav-tabs">
    <li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
    <?php
    foreach ($links as $key => $link) {
        ?>
        <li class="<?php echo $key != $module ? '' : 'active' ?> <?php echo $link['disabled'] ?>" data-toggle="tooltip" title="<?php echo $link['disabled'] ? $msgAviso : '' ?>">
            <a href="<?php echo (!$link['disabled'] && $key != $module ? get_url_admin() . $link['href'] : 'javascript:void(0)') ?>">
                <?php echo $link['name'] ?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
<br>
