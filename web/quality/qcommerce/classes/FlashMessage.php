<?php

/**
 *
 * Session-Based Flash Messages v1.0
 *
 * @description Guarda mensagens na sessão para serem facilmente exibidas depois.
 *
 *              As seguintes mensagens são suportadas: sucesso, erro, aviso e informação
 *
 *              OBS.: Esta classe foi totalmente atualizada para funcionar corretamente no e-commerce,
 *              não precisar ser instanciada (estática) e suportar um atalho genérico de
 *              callback: FlashMsg::danger('sua mensagem')
 *
 * @use Existem várias formas de adicionar uma mesma mensagem:<br><br>
 *
 * Exemplo para adicionar uma mensagem de erro:<br><br>
 *
 * FlashMsg::danger('mensagem de erro');<br>
 * FlashMsg::add('erro', 'mensagem de erro');<br>
 *
 * Adicionando outras mensagens: <br><br>
 *
 * FlashMsg::sucesso('mensagem de sucesso');<br>
 * FlashMsg::aviso('mensagem de aviso');<br>
 * FlashMsg::info('teste de informacao');<br>
 *
 * @method static FlashMsg sucesso(string $mensagem) Seta mensagem de sucesso
 * @method static FlashMsg aviso(string $mensagem) Seta mensagem de aviso
 * @method static FlashMsg info(string $mensagem) Seta mensagem de informação
 * @method static FlashMsg erro(string $mensagem) Seta mensagem de erro
 *
 * @author Mike Everhart - MikeEverhart.net (2011-05-15)
 * @author Felipe Corrêa (2013-01-30)
 *
 */
class FlashMsg
{
    //-----------------------------------------------------------------------------------------------
    // Class Variables
    //-----------------------------------------------------------------------------------------------
    public static $msgTypes = array('info', 'warning', 'success', 'danger');
    public static $msgClass = 'alert alert-dismissible fade in';
    public static $msgWrapper = "<div class='%s %s'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\n%s</div>\n";
    public static $msgBefore = '<p>';
    public static $msgAfter = "</p>\n";
    
    // Tratamento específico para mensagens de erro
    public static $msgWrapperError = "<div class='%s %s'><ul>\n%s</ul></div>\n";
    public static $msgBeforeError = '<li> - ';
    public static $msgAfterError = "</li>\n";

    /**
     * Adiciona uma mensagem para posterior exibição
     * OBS.: Modificada para possibilitar adicionar os tipos de mensagem em português (Felipe Corrêa)
     *
     * Exemplo de uso normal: <br>
     *   FlashMsg::add('erro', 'mensagem de erro');<br>
     * Pode-se utilizar o atalho:<br>
     *   FlashMsg::danger('mensagem de erro');<br>
     *   FlashMsg::sucesso('mensagem de sucesso');<br>
     *
     * @author Mike Everhart
     *
     * @param  string   $type           O tipo da mensagem que será utilizado (erro, sucesso, aviso, info)
     * @param  string   $message        A mensagem que será utilizada
     * @return  bool
     *
     */
    public static function add($type, $message)
    {
        switch ($type) {
            case 'erro':
                $type = 'danger';
                break;
            case 'sucesso':
                $type = 'success';
                break;
            case 'aviso':
                $type = 'warning';
                break;
            case 'info':
                $type = 'info';
                break;
        }
        if (!array_key_exists('flash_messages', $_SESSION)) {
            $_SESSION['flash_messages'] = array();
        }

        if (!isset($type) || !isset($message[0])) {
            return false;
        }

        // Make sure it's a valid message type
        if (!in_array($type, self::$msgTypes)) {
            die('FlashMessage: "' . strip_tags($type) . '" não é um tipo válido!');
        }

        // If the session array doesn't exist, create it
        if (!array_key_exists($type, $_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'][$type] = array();
        }

        $_SESSION['flash_messages'][$type][] = $message;

        return true;
    }

    /**
     * Mostra as mensagens que foram adicionadas à sessão <br>
     * OBS.: Função modificada para remover os códigos duplicados (Felipe Corrêa)
     *
     * @author Mike Everhart
     *
     * @param  string   $type     Quais mensagens mostrar <br>
     *                            Padrão: 'all'<br>
     *                            Outras opções disponíveis: erro, aviso, sucesso, info
     *
     * @param  bool     $print    true  = imprime na tela (padrão) <br>
     *                            false = retorna o valor na função
     * @return mixed
     *
     */
    public static function display($type = 'all', $print = true)
    {
        $data = '';

        if (!isset($_SESSION['flash_messages'])) {
            return false;
        }

        // Print a certain type of message?
        if (in_array($type, self::$msgTypes)) {
            $data = self::listMessages($type);
        }
        // Print ALL queued messages
        elseif ($type == 'all') {
            foreach ($_SESSION['flash_messages'] as $type => $msgArray) {
                $data .= self::listMessages($type);
            }
        } else {
            return false;
        }

        // Print everything to the screen or return the data
        if ($print) {
            echo $data;
        } else {
            return $data;
        }
    }

    /**
     * Percorre cada mensagem de um tipo informando e retorna em um array
     * tratamento especial para os erros
     *
     * @author Felipe Corrêa
     * @date 2013-01-30
     *
     * @return string Retorna as mensagens de um tipo já corretamente formatadas
     *                em HTML
     *
     */
    private static function listMessages($type)
    {
        
        $data = '';
        $messages = '';

        // Tratamento especial para mensagens de erro
        $before = ($type == 'erro') ? self::$msgBeforeError : self::$msgBefore;
        $after = ($type == 'erro') ? self::$msgAfterError : self::$msgAfter;
        $wrapper = ($type == 'erro') ? self::$msgWrapperError : self::$msgWrapper;

        foreach ($_SESSION['flash_messages'][$type] as $msg) {
            switch ($type) {
                case 'erro':
                    $type = 'danger';
                    break;
                case 'sucesso':
                    $type = 'success';
                    break;
                case 'aviso':
                    $type = 'warning';
                    break;
                case 'info':
                    $type = 'info';
                    break;
            }
            $messages .= $before . $msg . $after;
        }

        // Tratando nome da classe para pegar estilo correto no css
        $classType = 'alert-' . $type;
        $data .= sprintf($wrapper, self::$msgClass, $classType, $messages);

        // Limpando as mensagens já utilizadas
        self::clear($type);

        return $data;
    }

    /**
     * Check to  see if there are any queued error messages
     *
     * @author Mike Everhart
     *
     * @return bool  true  = There ARE error messages<br>
     *               false = There are NOT any error messages
     *
     */
    public static function hasErros()
    {
        return empty($_SESSION['flash_messages']['erro']) ? false : true;
    }
    
    /**
     * Check to  see if there are any queued sucess messages
     *
     * @author Mike Everhart
     *
     * @return bool  true  = There ARE success messages<br>
     *               false = There are NOT any success messages
     *
     */
    public static function hasSucessos()
    {
        return empty($_SESSION['flash_messages']['sucesso']) ? false : true;
    }

    /**
     * Check to see if there are any ($type) messages queued
     *
     * @author Mike Everhart
     *
     * @param  string   $type     The type of messages to check for
     * @return bool
     *
     */
    public static function hasMessages($type = null)
    {
        if (!is_null($type)) {
            if (!empty($_SESSION['flash_messages'][$type])) {
                return $_SESSION['flash_messages'][$type];
            }
        } else {
            foreach (self::$msgTypes as $type) {
                if (!empty($_SESSION['flash_messages'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Clear messages from the session data
     *
     * @author Mike Everhart
     *
     * @param  string   $type     The type of messages to clear
     * @return bool
     *
     */
    public static function clear($type = 'all')
    {
//        var_dump($type);
//        var_dump($_SESSION);die;
        if ($type == 'all') {
            unset($_SESSION['flash_messages']);
        } else {
            unset($_SESSION['flash_messages'][$type]);
        }
        return true;
    }
    
    /**
     * @author Felipe Corrêa
     * @date 2013-01-30
     * @description Um atalho para FlashMsg::add()
     *
     * FlashMsg::danger('mensagem de erro');
     */
    public static function __callStatic($fn, $args)
    {
        call_user_func_array(array('FlashMsg', 'add'), array($fn, $args[0]));
    }
}


// end class
