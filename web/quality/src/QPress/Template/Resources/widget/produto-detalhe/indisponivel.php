<?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>
<strong>Produto indisponível</strong>
<p>Este produto encontra-se indisponível no momento!</p>
<?php endif; ?>
