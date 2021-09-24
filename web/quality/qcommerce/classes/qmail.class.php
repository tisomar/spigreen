<?php

/**
 * Classe para padronização de envio de emails via SMTP, utilizando biblioteca SWIFT
 *
 * @author Jaison Vargas Veneri
 * @see PHPMailer
 * @since 07/10/2009
 *
 * USO:
 *
 *
 * Qmail::enviaMensagem($to, $subject, $mensagem);
 *
 * $to        = para quem enviar a mensagem (pode ser um array de emails)
 * $subject   = titulo da mensagem
 * $mensagem  = corpo da mensagem (pode ser HMTL)
 *
 *
 */
class Qmail
{
    // Dados padrões para envio de email. Configure em cada novo projeto para envio padrao
    // Ao início de cada projeto colocar os dados do cliente

    /**
     * Obtido através do campo 'smtp_host' da tabela de parametros
     */
    private $host = '';
    private $userName = '';
    private $senha = '';
    private $from = '';
    private $fromName = '';
    private $reply_to = '';
    private $use_smtp = true;
    private $timeout = 30;
    private $assunto;
    private $body;
    private $para;
    private $port;
    private $security;

    /**
     * Cria instancia do Qmail com valores de configuração padrão
     *
     * @param string $host host para se conectar com servidor SMTP
     * @param boolean $smtp se é uma conexão smtp
     * @param string $userName nome do usuario para se conectar no servidor
     * @param string $senha senha do usuario para se conectar no servidor
     * @param string $from
     * @param string $fromName Nome de quem esta enviando a mensagem
     */
    public function __construct($host = '', $smtp = '', $userName = '', $senha = '', $from = '', $fromName = '', $port = '', $security = '')
    {
        // Host
        if ($host == '') {
            $host = Config::get('smtp_host');
        }
        $this->host = $host;

        // User
        if ($userName == '') {
            $userName = Config::get('smtp_usuario');
        }
        $this->userName = $userName;

        // Pass
        if ($senha == '') {
            $senha = Config::get('smtp_senha');
        }
        $this->senha = $senha;

        // From - E-mail
        if ($from == '') {
            $from = Config::get('mail_from');
        }
        $this->from = $from;

        // From - Name
        if ($fromName == '') {
            $fromName = Config::get('mail_name');
        }
        $this->fromName = $fromName;

        // Use SMTP
        if ($smtp != '') {
            $this->use_smtp = $smtp;
        }

        if ($port == '') {
            $port = Config::get('smtp_port');
        }
        $this->port = $port;

        if ($security == '') {
            $security = Config::get('smtp_security');
        }
        $this->security = $security;

        // ReplyTo
        $replyTo = array();

        $email = Config::get('email_administrador');
        $name = Config::get('empresa_nome_fantasia');

        if ($email != '' && $name != '') {
            $replyTo[0] = array($name, $email);
        }

        $this->reply_to = $replyTo;
    }

    /**
     *
     * Envia um email
     *
     * @param string $assunto Assunto do email
     * @param \QPress\Template\Template|string $mensagem A mensagem em si
     * @param string $replyTo Um array (indice 0 significa o nome para resposta e indice 1 significa o email para resposta) com emails para serem adicionados como emails para resposta.
     * @param string $fromName Nome de quem esta enviando a mensagem
     * @param string $from Email de quem esta enviando a mensagem
     * @param array $cco array para copia ocultas
     * @param array $arrAnexos array com path para arquivos em anexo
     * @return boolean true se enviou false senao
     */
    
    public function envia($assunto, $mensagem, $replyTo = array(), $fromName = '', $from = '', $cco = array(), $arrAnexos = array())
    {
        $from = $from != '' ? $from : $this->from;
        $fromName = $fromName != '' ? $fromName : $this->fromName;
        
        // Criar "meio de trasporte" para envio
        if (true == (bool) \Config::get('email.tempo_real')) {
            if ($this->userName == '' && $this->senha == '') {
                $transport = Swift_SmtpTransport::newInstance($this->host, 25);
            } else {
                $transport = Swift_SmtpTransport::newInstance($this->host, $this->port, $this->security)->setUsername($this->userName)->setPassword($this->senha);
            }
        } else {
            ### Caso contrário, utilizar-se de SPOOL
            $transport = Swift_SpoolTransport::newInstance(new Swift_FileSpool(SPOOL_DIR));
        }
        
        //Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);
        
        //Create a message
        $env    = (false === strpos(getenv('APPLICATION_ENV'), 'prod')) ? 'dev' : 'prod';
        $env = 'prod';
        $to     = ('dev' == $env) ? \Config::get('email.desenvolvimento') : $this->getPara();
        
        $message = Swift_Message::newInstance($assunto, null, null, 'utf-8')
            ->setFrom(array($from => $fromName))
            ->setTo($to)
            ->setBody(
                ($mensagem instanceof \QPress\Mailing\TemplateMailing)
                    ? $mensagem->__toString()
                    : $mensagem,
                'text/html',
                'utf-8'
            )
            ->setBcc($cco);
        
        foreach ($replyTo as $reply) {
            $message->addReplyTo($reply[1], $reply[0]);
        }
        
        foreach ($arrAnexos as $strPath) {
            $message->attach(Swift_Attachment::fromPath($strPath));
        }

        $host = $_SERVER['HTTP_HOST'];

        $isLocal = stripos($host, '127.0.0.1') !== false
            || stripos($host, 'localhost') !== false
            || stripos($host, 'dev.spigreen') !== false
            || stripos($host, 'staging.spigreen.com.br') !== false;

        $isSpigreenEmail = is_array($to)
            && count($to) == 1
            && stripos($to[0], '@spigreen.com.br') !== false;
        
        // if ($isLocal && !$isSpigreenEmail):
        //     return true;
        // endif;

        if ($isLocal):
            return true;
        endif;

        //Send the message
        return $mailer->send($message);
    }


    /**
     * Efetua a criação do "transporte" de e-mail (sender)
     *
     * @return Swift_SmtpTransport
     */
    public static function createMailingTransport()
    {
        $mailer = new Qmail();

        if ($mailer->getUserName() == '' && $mailer->getSenha() == '') {
            $transport = Swift_SmtpTransport::newInstance($mailer->getHost(), 25);
        } else {
            $transport = Swift_SmtpTransport::newInstance($mailer->getHost(), $mailer->getPort(), $mailer->getSecurity())->setUsername($mailer->getUserName())->setPassword($mailer->getSenha());
        }

        return $transport;
    }

    /**
     *
     * @param mixed $para Pode ser um array com email ou uma string contendo o email para quem sera enviada a mensagem
     * @param string $assunto O assunto da mensagem
     * @param string $mensagem Corpo da mensagem, padrao HTML
     * @param array $replyTo Um array (indice 0 significa o nome para resposta e indice 1 significa o email para resposta) com emails para serem adicionados como emails para resposta.
     * @param string $fromName Nome de quem esta enviando a mensagem
     * @param string $from Email de quem esta enviando a mensagem
     * @param array $cco CCO email enviado como copia oculta para essas pessoas
     * @param array $arrAnexos Um array com endereco de um arquivo no servidor que sera enviado no email como um anexo
     * @return boolean Se mensagem foi enviada ou não
     */
    public static function enviaMensagem($para, $assunto, $mensagem, $replyTo = array(), $fromName = '', $from = '', $cco = array(), $arrAnexos = array())
    {

        if (!is_array($para)) {
            $para = explode(';', $para);
        }

        /* @var $mensagem \QPress\Mailing\TemplateMailing */
        $sender = new Qmail();

        $log = new EmailLog();
        $log->setRemetente($sender->getFrom());
        $log->setDestinatario(implode(';', $para));
        $log->setAssunto($assunto);
        $log->setConteudo($mensagem);
        if ($mensagem instanceof \QPress\Mailing\TemplateMailing) {
            $log->setTipo(str_replace('/', '_', $mensagem->__file));
        } else {
            $log->setTipo('no-template');
        }
        $log->setDataEnvio(date('Y-m-d H:i:s'));
        $log->save();

        $sender->setPara($para);
        $status = $sender->envia($assunto, $mensagem, $replyTo, $fromName, $from, $cco, $arrAnexos);

        $log->setStatus(($status == true ? EmailLogPeer::STATUS_ENVIADO : EmailLogPeer::STATUS_PENDENTE));
        $log->save();

        return $status;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getAssunto()
    {
        return $this->assunto;
    }

    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    public function getPara()
    {
        return $this->para;
    }

    public function setPara($para)
    {
        $this->para = $para;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getUse_smtp()
    {
        return $this->use_smtp;
    }

    public function setUse_smtp($use_smtp)
    {
        $this->use_smtp = $use_smtp;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     *
     * Passa os valores padroes da classe Qmail para a PhpMailer
     *
     */
    public function init()
    {
        if ($this->use_smtp) {
            $this->IsSMTP();
        }
        $this->IsHTML(true);
        $this->Host = $this->host;
        $this->SMTPAuth = $this->use_smtp;
        $this->Username = $this->userName;  // Change this to your gmail adress
        $this->Password = $this->senha;      // Change this to your gmail password
        $this->From = $this->from;       // This HAVE TO be your gmail adress
        $this->FromName = $this->fromName;   // This is the from name in the email, you can put anything you like here
        $this->Body = $this->body;
        $this->Subject = $this->assunto;
        $this->Timeout = $this->timeout;
    }

    /**
     * @return bool|string
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @param bool|string $security
     */
    public function setSecurity($security)
    {
        $this->security = $security;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param bool|string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }
}
