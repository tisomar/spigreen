<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QPressBase
 *
 * @author QPress
 */
class QPressBase extends BaseObject {

    public $strImageName = 'random';
    public $strImagemPadrao = 'default.png';
    public $randomName = 'random';

    public function setStrImagem($phpName) {
        $this->strPhpNameImagem = $phpName;
        return $this;
    }

    public function myValidate(&$erros, $columns = null) {
        
        if (!is_array($erros)) {
            $erros = array();
        }
        
        $classPeer = $this->getPeer();

        $validateErros = array();

        if (!$this->validate($columns)) {

            $rawNames = $classPeer::getFieldNames(BasePeer::TYPE_RAW_COLNAME);
            $colNames = $classPeer::getFieldNames(BasePeer::TYPE_COLNAME);

            foreach ($this->getValidationFailures() as $colname => $objValidationFailure) {
                $key = array_search($colname, $colNames);
                $rawname = isset($rawNames[$key]) ? $rawNames[$key] : null;

                if (!is_null($rawname)) {
                    $validateErros[$rawname] = $objValidationFailure->getMessage();
                } else {
                    $validateErros[] = $objValidationFailure->getMessage();
                }
            }
        }

        foreach ($validateErros as $index => $strErro) {
            $erros[$index] = $strErro;
        }

        return (count($erros) == 0);
    }

    public function validateOnDelete() {

        return array();
    }

    /**
     * Retorna descricao para constanstes de mostrar
     * @param $strStatus
     * @return string
     */
    public static function getDescConstStatus($strStatus) {
        switch ($strStatus) {
            case 'SIM' : $strRet = 'Sim';
                break;
            case 'NAO' : $strRet = 'Não';
                break;
            default : $strRet = '';
                break;
        }

        return $strRet;
    }

    public function move($file, $path = 'arquivos', $allowed_extentions = array()) {
        return $this->saveImagem($file, $path, $allowed_extentions);
    }

    /**
     *
     * Faz upload de uma imagem para o servidor e redimensiona
     * @author Jaison Vargas
     * @author Rodrigo Antunes <rodrigo@qualitypress.com.br>
     * @date 29/11/2012
     * @param array $file Um arquivo enviado via formulario de um input type="file"
     * @return boolean Indica se salvou com sucesso a imagem
     */
    public function saveImagem($file, $path = 'arquivos', $nodeThumb = null, $allowed_extentions = array()) {

        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
                ->setAllowedExtensions($allowed_extentions)
                ->setUploadDir($path)
                ->prepare($file)
        ;

        if (false !== $file = $fileUploader->move()) {
            $this->deleteImagem();
            $this->_setImagem($file->getFileName());
        }

        return $fileUploader;

    }

    public function saveArquivo($file) {
        // instancia para upload de arquivos
        $objFile = new UploadFile($file, array (
            'saveDir'           => $this->_getAbsolutePathImagem(),
            'prefixFileName'    => $this->strPrefixFileName,
            'generateFileName'  => true
        ));

        $strFileNameImg = $objFile->save(); // salva imagem no servidor e retorna nome do novo arquivo

        if ($strFileNameImg !== false) { // se salvou com sucesso

            // se salvou imagem com sucesso, deleta a imagem antiga do servidor se ela existir
            $this->deleteImagem();

            // seto a imagem com o nome do novo arquivo no servidor com o metodo interno para setar imagem
            $this->_setImagem($strFileNameImg);

            return true;
        }

        return false;
    }

    /**
     * Método interno para setar a proproiedade da imagem de acordo com o configurado na classe pai
     * @author Jaison Vargas
     * @date 11/02/2010
     * @param string $strFileNameImg
     * @return Destaque
     */
    public function _setImagem($strFileNameImg) {
        // nome da função para setar a imagem
        $strFunction = 'set' . $this->strPhpNameImagem;

        return $this->$strFunction($strFileNameImg);
    }

    /**
     * Método interno para pegar a proproiedade da imagem de acordo com o configurado na classe pai
     * @author Jaison Vargas
     * @date 11/02/2010
     * @param string $strFileNameImg
     * @return Destaque
     */
    public function _getImagem() {
        // nome da função para pegar a imagem
        $strFunction = 'get' . $this->strPhpNameImagem;

        return $this->$strFunction();
    }

    /**
     * Pega endereço absoluto da imagem no servidor usando a propriedade da classe pai $this->strPathImg e o BASE_PATH do projeto
     * @author Jaison Vargas
     * @date 11/02/2010
     * @return string
     */
    public function _getAbsolutePathImagem() {
        return $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . $this->strPathImg;
    }

    /**
     * Indica se a imagem existe no servidor
     * @author Jaison Vargas
     * @date 11/02/2010
     * @return boolean
     */
    public function isImagemExists() {
        return is_file($this->_getAbsolutePathImagem() . $this->_getImagem()) && $this->_getImagem() != '';
    }

    /**
     * Retorna o endereço SRC da imagem. Concatena BASE_URL com PATHIMG e o nome da propria imagem
     */
    public function getSrcImagem() {
        return BASE_PATH . $this->strPathImg . $this->_getImagemValida();
    }

    public function getPathAbsolute() {
        return $this->_getAbsolutePathImagem() . $this->_getImagem();
    }

    /**
     * Apaga a imagem do servidor. Antes de apagar a imagem função verifica se ela existe.
     * @author Jaison Vargas
     * @date 11/02/2010
     * @return boolean
     */
    public function deleteImagem() {
        if ($this->isImagemExists()) {
            if (@unlink($this->_getAbsolutePathImagem() . $this->_getImagem())) {
                $this->_setImagem("");
                return true;
            }
        }
        return false;
    }

    /**
     * Retorna uma string que contem uma imagem valida. Eg: caso a imagem não existir no servidor, retorna a parametro definido na classe pai $strImagemPadrao
     * @author Jaison Vargas
     * @date 11/02/2010
     * @return string
     */
    public function _getImagemValida() {
        return $this->isImagemExists() ? $this->_getImagem() : $this->strImagemPadrao;
    }

    public function getUrlImage($fieldName = null) {

        $backup = $this->strPhpNameImagem;

        if (!is_null($fieldName)) {
            $this->strPhpNameImagem = $fieldName;
        }

        $response = BASE_PATH . $this->strPathImg . $this->_getImagem();

        $this->strPhpNameImagem = $backup;

        return $response;
    }

    /**
     * Retorna url apontando para o arquivo de resize de imagens juntamente com os parametros definidos por $strArgs e o nome do arquivo usado para mostrar a imagem
     * @author Rodrigo Antunes <rodrigo@qualitypress.com.br>
     * @date 11/10/2012
     * @param string $strArgs Uma strinf contendo os parametros necessarios para gerar uma thumb pelo arquivo resize. ex: width=100&height=150&cropratio=0.66:1
     * @return string
     */
    public function getUrlImageResize($strArgs) {
        extract($this->_getDimensions($strArgs));
        return resizeImage($this->_getAbsolutePathImagem(), $this->_getImagemValida(), $width, $height, $cropratio);
    }

    public function forceUrlImageResize($strArgs) {
        extract($this->_getDimensions($strArgs));
        return resizeImage($this->_getAbsolutePathImagem(), $this->_getImagemValida(), $width, $height, $cropratio, 90, true);
    }

    public function _getDimensions($strArgs) {
        if (($pos = strpos($strArgs, "&amp;")) !== false) {
            $infoDimensoes = explode("&amp;", $strArgs);
        } else {
            $infoDimensoes = explode("&", $strArgs);
        }

        $width = 0;
        $height = 0;
        $cropratio = 0;

        if (is_array($infoDimensoes)) {
            foreach ($infoDimensoes as $info) {
                $arrValores = explode("=", $info);
                if ($arrValores[0] == 'width') {
                    $width = $arrValores[1];
                } else if ($arrValores[0] == 'height') {
                    $height = $arrValores[1];
                } else if ($arrValores[0] == 'cropratio') {
                    $cropratio = $arrValores[1];
                }
            }
        }

        return array('width' => $width, 'height' => $height, 'cropratio' => $cropratio);
    }

    /**
     * Retorna url apontando para o arquivo de resize de imagens juntamente com os parametros definidos por $strArgs e o nome do arquivo usado para mostrar a imagem
     * @author Rodrigo Antunes <rodrigo@qualitypress.com.br>
     * @date 10/10/2012
     * @param string $strArgs Uma strinf contendo os parametros necessarios para gerar uma thumb pelo arquivo resize. ex: width=100&height=150&cropratio=0.66:1
     * @return string
     */
    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true) {

        if (!$this->isImagemExists() && !$boolUseImagemPadrao) {
            return '';
        }

        extract($this->_getDimensions($strArgs));
        $srcImagem = resizeImage($this->_getAbsolutePathImagem(), $this->_getImagemValida(), $width, $height, $cropratio);

        $arrAtributtes = array_merge(
                $arrAtributtes, array('src' => $srcImagem)
        );

        return "<img " . get_atributes_html($arrAtributtes) . " />";
    }

    // Fim métodos adicionados para fazer gerenciamento de imagens

    /**
     *
     * Seta as propriedades do objeto por um array
     *
     * @param <array> $array Um array para setar as propriedades do objeto
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     */
    public function setByArray($array, $type = BasePeer::TYPE_FIELDNAME) {
        if (!is_array($array)) {
            $array = array();
        }

        $array = array_change_key_case($array, CASE_UPPER);

        $fieldsFunc = $this->getPeer()->getFieldNames();
        $fieldName = $this->getPeer()->getFieldNames($type);

        foreach ($fieldsFunc as $key => $name) {
            $method = 'set' . $name;
            if (array_key_exists($fieldName[$key], $array)) {
                if ($array[$fieldName[$key]] != '') {
                    $this->$method($array[$fieldName[$key]]);
                } else {
                    $this->$method(null);
                }
            }
        }
    }

    public function _getAllowedExtentions($type = null) {
        $options = array(
            'image' => array('png', 'jpeg', 'jpg', 'gif',),
            'documents' => array('pdf', 'xls', 'xlsx', 'csv', 'xml', 'json', 'doc', 'docx',),
        );

        if (isset($options[$type])) {
            return $options[$type];
        }

        return array_merge(
                array_values($options['image']), array_values($options['documents'])
        );
    }

}
