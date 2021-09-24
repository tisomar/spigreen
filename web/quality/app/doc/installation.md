Installation
============

Leia a documentação abaixo para conseguir instalar e rodar a solução para lojas virtuais Q.Commerce:

[Premissas](#premissas)

1. [Instalação das dependências através do composer](#enable-in-composer)

2. [Criação e configuração do banco de dados](#database-config)

### <a id="premissas" name="premissas"></a>
### Premissas

Antes de iniciar o processo da configuração e instalação do Q.Commerce, você deverá ter pré configurado alguns itens em seu computador, sendo:

- Instalação do PHP e servidor HTTP, incluindo o PHP no Path do sistema operacional
- Instalaçao do composer e criaçao de atalho BAT
- Banco de dados criado no servidor interno para conexão


### <a id="enable-in-composer" name="enable-in-composer"></a>
### Passo 1: Instalação via composer

Assumindo que você possua o composer global:
Abrir o Prompt Command/Windows ou Terminal/Linux como administrador.
Em seguida você deverá seguir até o caminho de onde encontra-se a pasta /quality de seu e-commerce, digitando sequência:

```
$ composer install
```

Não existindo o composer, você poderá baixá-lo em [getcomposer.org/download](https://getcomposer.org/download/)

```
$ php composer.phar install
```

### <a id="database-config" name="database-config"></a>
### Passo 2: Configurar o banco de dados

Após ter instalado as dependências do composer, as seguintes configurações devem ser executadas:

#### Passo 1. Criação do banco de dados:

- O banco de dados já está criado no servidor interno? Se sim, pode pular ao passo 02.

Se não estiver, o comando abaixo permite você criar o banco de dados com base no nome da pasta do projeto. Ex.: catarinasemijoias-qcommerce.

Acessando a pasta quality/ do projeto através do CMD, executar o comando abaixo:

```
$ php bin/console quality:database:create
```

O próprio CMD lhe fará umas perguntas que ajudarão na configuração deste banco de dados.

#### Passo 2. Configuração do banco de dados criado no Propel:

Este comando irá gerar os arquivos de configuração do ORM Propel.

Acessando a pasta quality/ do projeto através do CMD, executar o comando abaixo:

```
$ php bin/console quality:database:config
```

#### Passo 3. Executando o propel-gen para aplicar as configurações do ORM Propel:

Acessando a pasta quality/ do projeto através do CMD, executar o comando abaixo:

```
$ php bin/console quality:propel:generator
```

#### Passo 4. Criando as tabelas e as fixures iniciais do projeto:

Alguns comandos foram criados para auxiliar na criação de tabelas e dados iniciais.

Tendo a certeza de que o arquivo quality/qcommerce/propel/build/conf/qcommerce-conf.php está devidamente configurado
com a base de dados criada, podemos executar os seguintes comandos:

1. Criar as tabelas no banco de dados com base no arquivo schema.sql:

```
$ php bin/console quality:database:create-tables
```

2. Inserir os registros basilares para o funcionamento inicial do projeto com base nos arquivos contidos na pasta 
quality/app/database/fixtures:

``` bash
$ php bin/console quality:database:fixtures:load
```

Por final, acessar a url do projeto para certificar-se de que os passos foram executados com sucesso.

