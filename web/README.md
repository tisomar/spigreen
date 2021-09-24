# como usar
installe composer:
`find . -type d \( -name "vendor" -o -name "vendors" -o -name "backend" \) -prune -o -name composer.json -print -execdir composer install ';'`

** NAO PODE INCLUIR ** `composer.lock` quando quer submeter um `git push`.

### Gerar Documentação
```bash
    $ docker pull phpdoc/phpdoc
    $ docker run --rm -v $(pwd):/data phpdoc/phpdoc -d ./quality/qcommerce/propel/build/classes/qcommerce/ -t ./docs/propel 
```

### PSR-2 Validação
```bash
    $ docker-compose exec -T php-fpm ./quality/vendor/bin/phpcs -v --standard=PSR2 ./quality # para ver
    $ docker-compose exec -T php-fpm ./quality/vendor/bin/phpcbf --ignore=*/map/*,*/om/* -v --standard=PSR2 ./quality # para fazer mudanças
```

#### Dependências
#### Clear Sale
[Repo dependency](https://github.com/isaquesb/clear-sale-php-sdk)
