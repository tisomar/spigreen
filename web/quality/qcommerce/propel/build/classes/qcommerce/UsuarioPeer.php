<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_USUARIO' table.
 *
 * Usuários do admin
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class UsuarioPeer extends BaseUsuarioPeer
{

    const LINGUAGEM_INGLES = 'ING';
    const LINGUAGEM_PORTUGUES = 'POR';
    const PERMISSOES = 'PERMISSOES';
    const USUARIO_LOGADO = 'USUARIO_LOGADO';

    public static function validateNewPassword($password, $passwordConfirmation = null) {

        $response = array();
        $hasError = false;

        if ($password == '') {
            $response['error'] = 'Por favor, informe sua senha.';
            $hasError = true;
        }

        if (!is_null($passwordConfirmation) && $passwordConfirmation == '') {
            $response['error'] = 'Por favor, informe a confirmação de senha.';
            $hasError = true;
        }

        if (!$hasError && $password != $passwordConfirmation) {
            $response['error'] = 'Por favor, a senha informada não é igual à confirmação de senha.';
            $hasError = true;
        }

        if (!$hasError && !isset($password[5])) {
            $response['error'] = 'Por favor, a senha deve conter pelo menos 6 caracteres.';
            $hasError = true;
        }

        return $response;

    }

    /**
     *
     * Retorna usuario do banco de dados de acordo com login e senha de parametros
     *
     * @param string $login O login do usuario
     * @param string $senha a senha do usuario, sera usada função md5() nesse parametro
     * @return Usuario instancia de usuario se existir, null senao
     */
    public static function retrieveByLoginSenha($login, $senha)
    {
        $c = new Criteria();
        $c->add(self::LOGIN, $login);
        $c->add(self::SENHA, sha1($senha . Usuario::SENHA_SALT));

        return self::doSelectOne($c);
    }

    /**
     *
     * Indica se tem algum usuario logado no sistema
     *
     * @return boolean
     */
    public static function isAuthenticad()
    {
        return self::getUsuarioLogado() instanceof Usuario;
    }

    /**
     *
     * Coloca usuario de parametro como usuario logado no sistema
     *
     * @param Usuario $usuario
     */
    public static function setUsuarioLogado(Usuario $usuario)
    {
        $modulosLiberados = $usuario->getModulosLiberados();
        
        $_SESSION[self::PERMISSOES] = serialize($modulosLiberados);
        $_SESSION[self::USUARIO_LOGADO] = serialize($usuario);
    }

    /**
     *
     * Retorna usuario logado no sistema
     *
     * @return Usuario Usuario logado se tiver, null senao
     */
    public static function getUsuarioLogado()
    {
        return (isset($_SESSION[self::USUARIO_LOGADO])) ? unserialize($_SESSION[self::USUARIO_LOGADO]) : null;
    }

    /**
     *
     * Faz logout do usuario na sessão
     *
     */
    public static function logout()
    {
        unset($_SESSION[self::USUARIO_LOGADO]);
    }

    /**
     *
     * Retorna instancia de usuario buscando pelo email passado ou null senao
     *
     * @param string $email
     * @return Usuario
     */
    public static function getUsuarioByEmail($email)
    {
        $c = new Criteria();
        $c->add(UsuarioPeer::EMAIL, $email);

        return self::doSelectOne($c);
    }

    /**
     *
     * Gera uma nova senha com quantidade de caracteres definidos por $qtdCaracteres
     *
     * @param integer $qtdCaracteres
     * @return string
     */
    public static function geraSenha($qtdCaracteres = 6)
    {
        return substr(md5(time()), 0, $qtdCaracteres);
    }

    /**
     *
     * Codifica uma string (id)
     *
     * @param string $id
     * @return string codificada
     */
    public static function codificaId($id)
    {
        return base64_encode($id);
    }

    /**
     *
     * Decodifica uma string (id)
     *
     * @param string $id
     * @return string decodificada
     */
    public static function decodificaId($id)
    {
        return base64_decode($id);
    }

    /**
     * Retorna os usuários da plataforma em array list
     *
     * @return array
     */
    public static function getUsuarioNomeList()
    {

        $arrUsuarios = array();

        $arrObjUsuarios = UsuarioQuery::create()->filterById(1, Criteria::NOT_EQUAL)->find();

        if(count($arrObjUsuarios)){
            foreach ($arrObjUsuarios as $objUsuario){ /** @var $objUsuario Usuario */
                $arrUsuarios[$objUsuario->getId()] = $objUsuario->getNome();
            }
        }

        return $arrUsuarios;
    }

}
