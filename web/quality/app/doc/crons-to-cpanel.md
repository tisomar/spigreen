# Adicionando Crons ao CPanel através do Command Line

---

Para inserir as crons no CPanel através do Command Line, você deve abrir o arquivo 'quality/app/config/cpanel.yml'

Nele você deverá informar os dados de conexão e os comandos das crons.

Após ter estas informações configuradas, você deve acessar a pasta '/qualty' do projeto através do CMD/Terminal.

Executar o seguinte comando:

``` bash
$ php bin/console cpanel:cron:create-from-yaml ./app/config/cpanel.yml
```

Por final, acessar o CPanel do projeto para garantir que as crons foram inseridas corretamente.
