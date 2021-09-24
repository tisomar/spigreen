<?php
/*
 *
 * FUNÇÕES UTILIZADAS NO CONTROLADOR
 *
 * @author Felipe Corrêa
 * @description A lógica utilizada na criação das funções do controlador
 * possui diversos autores e foi melhorada com o passar do tempo.
 */


/**
 * Divide a URL em partes com base no caractere '/', ignorando parâmetros passados por $_GET '?'.
 * Retorna o array com o resultado da URL divida
 *
 * @param string $url URL atual do arquivo
 * @return array Array com a URL dividida
 */
function dividirUrl($url)
{
    $_partes = array();
    
    foreach (explode('/', $url) as $parte) {
        if ($parte !== '') {
            $pos = strpos($parte, '?');
            if ($pos !== false) {
                $parte = substr($parte, 0, $pos);
                if ($parte === '') {
                    continue;
                }
            }
            $_partes[] = $parte;
        }
    }
    return $_partes;
}

/**
 * Definindo automaticamente (quando necessário) o primeiro parâmetro,
 * assim, torna-se ilimitado a quantidade de níveis de diretórios
 *
 * @author Felipe Corrêa
 * @since 14/02/2013
 * @param array $_partes URL dividida em partes
 * @param int $primeiroParam Número mínimo de parâmetros para começar a iniciar a identificação automática
 * @param string $controladorCaminhoBase Caminho base para fazer a busca pelos arquivos
 * @return int Retorna a posição na url onde iniciam-se os parâmetros
 */
function definirPrimeiroParametro($_partes, $primeiroParam, $controladorCaminhoBase)
{
    
    // ganhando em desempenho definindo o $primeiroParam
    // automaticamente apenas quando necessário
    if (count($_partes) >= $primeiroParam) {
        $urlVerificacao = '';

        // percorrendo as partes da URL até não encontrar mais arquivos ou diretórios válidos
        for ($i = 0; $i < count($_partes); $i++) {
            $isValid = false;
            $urlVerificacao .= DIRECTORY_SEPARATOR . $_partes[$i];

            $fileVerificar =  $controladorCaminhoBase . $urlVerificacao;
            
            // verifica se é um arquivo ou diretório
            if (is_file($fileVerificar) || is_dir($fileVerificar)) {
                $isValid = true;
            }
            // verifica se é um arquivo em que foi omitido o .php na URL
            elseif (strrpos($fileVerificar, '.php') === false && is_file($fileVerificar . '.php')) {
                $isValid = true;
            } else {
                // começaram os parâmetros, então para a execução
                break;
            }

            // se é um caminho válido e já chegou no mínimo do primeiro parâmetro
            // então altera o início da primeiro parâmetro
            if ($isValid && $i == $primeiroParam) {
                    $primeiroParam++;
            }
        }
    }
    
    return $primeiroParam;
}

/**
 * Define quais partes da URL serão consideradas na definição do caminho para o arquivo que será incluído
 *
 * @param array $_partes Array contendo a URL dividia em partes
 * @param int $primeiroParam Define onde iniciam os parâmetros na URL
 * @return array Array contendo as partes da URL (caminhos reais de diretórios e arquivos)
 */
function definirPartesUrl($_partes, $primeiroParam)
{
    $partes = array();
    
    for ($i = 0; ($i < $primeiroParam) && ($i < count($_partes)); $i++) {
        $partes[] = $_partes[$i];
    }
    
    return $partes;
}

/**
 * Define os argumentos passados pela URL (não leva em conta o que foi colocado
 * no GET utilizando o '?')
 *
 * @param array $_partes Array contendo a URL dividia em partes
 * @param int $primeiroParam Define onde iniciam os parâmetros na URL
 * @return array Array contendo os argumentos da URL
 */
function definirArgsUrl($_partes, $primeiroParam)
{
    $args = array();
    
    for ($i = $primeiroParam; $i < count($_partes); $i++) {
        $args[] = $_partes[$i];
    }
    
    return $args;
}

/**
 * Procura na última parte da url (antes de começar os argumentos) para ver se
 * a extensão do arquivo foi passada, caso não, define uma extensão padrão
 *
 * @param array $partes Array contendo as partes da URL (caminhos reais de diretórios e arquivos)
 * @return string Extensão que será utilizada na definição do caminho do arquivo
 */
function definirExtensaoPadrao($partes)
{
    
    $extensao = '';
    
    // procurando na última parte para ver se o arquivo já possui extensão definida
    if (strrpos(end($partes), '.') === false) {
        $extensao = '.php';
    }
    
    return $extensao;
}


/**
 * Identifica e retorna o arquivo que será incluso
 *
 * @param array $partes Array contendo as partes da URL (caminhos reais de diretórios e arquivos)
 * @param string $extensao Extensão padrão que será definida caso nenhuma seja encontrada no arquivo
 * @param string $arquivoIndexPadrao Nome do arquivo que será assumido caso nenhum arquivo tenha sido passado na URL
 * @param string $controladorCaminhoBase Caminho base para fazer a busca pelos arquivos
 * @return string Caminho para o arquivo que será incluso pelo controlador (ou parte dele caso não encontre)
 */
function definirCaminhoArquivo(&$partes, $extensao, $arquivoIndexPadrao, $controladorCaminhoBase)
{
    $file = $controladorCaminhoBase;

    // percorrendo partes da URL e gerando caminho físico real do arquivo
    foreach ($partes as $indice => $parte_url) {
        $file .= DIRECTORY_SEPARATOR . $parte_url;

        // se for a última parte
        if ($indice == (count($partes) - 1)) {
            // verificando se a última parte é um arquivo
            if (is_file($file . $extensao)) {
                // achou o arquivo então salva com a extensão
                $file .= $extensao;
            }
            // se não for um arquivo, verifica se é uma pasta
            elseif (is_dir($file)) {
                // achou a pasta e define um arquivo padrão "index.php"
                $file .= DIRECTORY_SEPARATOR . $arquivoIndexPadrao . $extensao;
                
                $partes[] = $arquivoIndexPadrao;
            }
        }
    }
    
    return $file;
}
