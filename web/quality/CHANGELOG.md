CHANGELOG FROM TAG v3.x.x
===================

### v3.10.0 (2016-02-05)

 * Adicionado recurso que permite duplicar um produto

### v3.9.0 (2016-02-04)

 * Adicionado recurso de múltiplas lojas para retirada

### v3.8.0 (2016-02-01)

 * Adicionado recurso de pre-seleção de variações ao entrar na página de detalhes do produto
 * validação de atributos excluídos na consulta do nome completo da variação
 * ajuste na galeria de fotos do produto na tela de detalhes
 * Adicionado campo para escolha da variação padrão que virá pre-selecionada ao entrar na tela de detalhes do produto

### v3.7.5 (2016-01-28)

 * Adicionado campo de tags no cadastro do produto para auxiliar no recurso de busca e busca sugestiva
 * Ajuste no sprite dos icones
 * correção de lógica para validação de pagamentos via pagseguro

### v3.7.4 (2016-01-18)

 * Atualização do pacote imagemin para 1.0.0
 
### v3.7.3 (2016-01-18)

 * adicionado controle de produtos e variações mais vendidos
 * correção nos parametros iniciais
 * Adicionadas imagens padrões para quando não houver imagem cadastrada
 
### v3.7.2 (2016-01-18)
 
 * retirada obrigatoriedade do campo de imagem da categoria
 * definido min-height para o carousel vertical
 
### v3.7.1 (2016-01-15)

 * Adicionadas imagens padrões para quando não houver imagem cadastrada
 * retirada tarja do topo para versões demo
 * ajuste na tela de login, recuperacao de senha e redefinicao de senha do admin
 
### v3.7.0 (2016-01-15)

 * Ajustes no zoom e na galeria da página de detalhes do produto
 * adicionada opção de cadastrar um banner na categoria que será disponibilizado na listagem de produtos
 * controle de redirecionamento aos passos do carrinho de compras depois da recuperação de senha caso o cliente possua itens em seu carrinho
 * adicionada instrução na lista de crons do cpanel
 * correção no envio de e-mail generico
 * adicionado recurso de ordenação por mais vendidos
 * Ajustes nas fixtures para criação do projeto.
 * Ajuste no relatório por formas de pagamento

### v3.6.2 (2015-12-29)
 
 * adicionada configuração para a versão padrão que se estiver definida, algumas mensagens e alertas aparecerão ao visitante informando que a versão demonstrativa é
                    um recurso para simulação do produto
 * correção na definição do número de colunas

### v3.6.1 (2015-12-23)

 * correção do problema de sobreposição entre o sprite e addthis
 * ajustado layout da listagem de produtos para quando houver menu lateral, o layout apresenta 3 colunas de produtos. Do contrário o layout apresenta 4 colunas de produtos
 * correção no script de busca

### v3.6.0 (2015-12-08)

 * Alteração da nomenclatura de configurações do sistema para configurações restritas
 * organização nas forma de pagamento que permite superpay + checkout transparente do pagseguro
 * ajustes de layout
 * buxfix   a09f3c0 Adicionada validação na exclusão de clientes com pedidos vinculados
 * buxfix   237fa79 correção no nome banco de dados

### v3.5.2 (2015-12-04)

 * buxfix   da33aeb Alteração na regra de transportadora
 * Adicionado filtro por marca na página de listagem dos produtos no admin

### v3.5.1 (2015-11-16)

 * Ajustados parametros no gateway PayPal
 * ajuste na biblioteca do pagseguro que não enviava o parametro senderCNPJ para cadastros PJ
