<?php
// Verifica se o cliente optou por alguma ordenação
if ($request->query->get('ordenar-por') != '') {
    $container->getSession()->set('ordenar-por', $request->query->get('ordenar-por'));
}

// Verifica a quantidade de itens por página que devem ser exibidos
if ($request->query->get('produtos-por-pagina') != '') {
    $container->getSession()->set('produtos-por-pagina', $request->query->get('produtos-por-pagina'));
} else {
    $keys = array_keys(get_exibicao_produto_options());
    if (!in_array($container->getSession()->get('produtos-por-pagina'), $keys)) {
        $container->getSession()->set('produtos-por-pagina', array_pop($keys));
    }
}
