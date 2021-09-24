Na pasta `quality/qcommerce/propel` para produzir classes, sql e conf:
```
    $ ../../vendor/propel/propel1/generator/bin/propel-gen
```

ou para gerar so os classes:

```
    $ ../../vendor/propel/propel1/generator/bin/propel-gen om
```

Na pasta `quality/qcommerce/propel` para engenharia reversa:
```
    $ ../../vendor/propel/propel1/generator/bin/propel-gen . reverse
```

# Atualizações
O arquivo `schema_org.xml` contém uma cópia do código original.

Atualize `schema.xml` quando quer atualizar o banco de dados e use os comandos acima.