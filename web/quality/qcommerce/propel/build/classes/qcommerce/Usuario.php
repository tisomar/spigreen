<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_USUARIO' table.
 *
 * Usuários do admin
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Usuario extends BaseUsuario
{

    /**
     * Salt para geração de senhas
     */
    const SENHA_SALT = 'm~72zbc-703$1^L#Z7';

    /**
     * Senha original sem a criptografia
     * @var String 
     */
    private $senhaOriginal = null;

    /**
     * Módulos que o cliente tem acesso liberado.
     * @var array
     */
    private $modulosLiberados = null;

    /**
     * Retorna true se o usuário for MASTER 
     * Obs: Usuários da QualityPress são usuários Master
     * 
     * @return boolean
     */
    public function isMaster()
    {
        return (bool) $this->getMaster();
    }

    /**
     * Validação na qual o propel não possui
     * 
     * @param array $erros
     * @param type $columns
     * @return boolean
     */
    public function myValidate(&$erros, $columns = null)
    {
        parent::myValidate($erros, $columns);

        if ($this->senhaOriginal !== null)
        {
            if (strlen($this->senhaOriginal) < 6)
            {
                $erros[] = 'A senha deve possuir pelo menos 6 caracteres';
            }
            else if (strlen($this->senhaOriginal) > 20)
            {
                $erros[] = 'A senha deve possuir ao máximo 20 caracteres';
            }
        }

        return (count($erros) == 0);
    }

    /**
     * Seta a senha do usuario com a criptografia sha1();
     *
     * @param string $senha  A senha a ser definida na instancia do usuario
     */
    public function setSenha($senha)
    {
        if ($senha != '')
        {
            $this->senhaOriginal = $senha;
            parent::setSenha(sha1($senha . self::SENHA_SALT));
        }
        return $this;
    }

    /**
     * Faz login do usuario na session
     */
    public function makeLogin()
    {
        UsuarioPeer::setUsuarioLogado($this);
    }

    public function getModulosLiberados()
    {
        $arrPermissaoGrupoModulo = PermissaoGrupoModuloQuery::create()
            ->usePermissaoGrupoQuery()
            ->usePermissaoGrupoUsuarioQuery()
            ->filterByUsuarioId($this->getId())
            ->endUse()
            ->endUse()
            ->groupByModuloId()
            ->find()
            ->toArray();



        if(count($arrPermissaoGrupoModulo) > 0 && $this->modulosLiberados == null){
            $arrModules = array();

            foreach ($arrPermissaoGrupoModulo as $objPermissaoGrupoModulo)
            { /** @var $objPermissaoGrupoModulo PermissaoGrupoModulo */
                $arrModules[] = $objPermissaoGrupoModulo['ModuloId'];
            }
            $this->modulosLiberados = $arrModules;
        }

        return !is_array($this->modulosLiberados) ? array() : $this->modulosLiberados;
    }
    
    public function setModulosLiberados($v) {
        $this->modulosLiberados = $v;
    }

    /**
     *
     * Envia email com link para usuario criar uma nova senha
     *
     * @return boolean True se enviou o email false senao
     */
    public function initProccessRecoveryPassword()
    {
        $token = bin2hex(mcrypt_create_iv(6, MCRYPT_DEV_RANDOM));
        $this->setToken($token)->save();

        \QPress\Mailing\Mailing::enviarLinkRenovacaoSenhaAdmin($this);
    }

}
