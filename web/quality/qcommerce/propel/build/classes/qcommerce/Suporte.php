<?php

use classes\S3Uploader;


/**
 * Skeleton subclass for representing a row from the 'qp1_suporte' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Suporte extends BaseSuporte
{
    const TIPO_TEXTO    = 'TEXTO';
    const TIPO_VIDEO    = 'VIDEO';
    const TIPO_ARQUIVO  = 'ARQUIVO';
    const TIPO_VIDEO_AULA  = 'VIDEO_AULA';
        
    protected static $tipos = array(
            self::TIPO_TEXTO, 
            self::TIPO_VIDEO, 
            self::TIPO_ARQUIVO,
            self::TIPO_VIDEO_AULA
    );
    
    // Controle de upload de imagem
    public $strPrefixFileName = ''; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/suporte/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Arquivo'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $strNodeName = 'suportes';
    public $allowedExtentions = array('pdf', 'jpg', 'png', 'jpeg');

    public function postDelete(\PropelPDO $con = null) 
    {
        $this->deleteImagem();
        
        return parent::postDelete($con);
    }
    
    public function setTipo($v) 
    {
        if (!in_array($v, self::$tipos)) {
            throw new InvalidArgumentException('Tipo inválido.');
        }
        
        return parent::setTipo($v);
    }
    
    /**
     * 
     * @return string
     */
    public function getTipoDesc()
    {
        $list = SuportePeer::getTipoList();
        $tipo = $this->getTipo();
        
        if (isset($list[$tipo])) {
            return $list[$tipo];
        }
        
        return '';
    }
    
    public function myValidate(&$erros, $columns = null) {
        
        if ($video = $this->getVideo()) {
            $host = '';
            $arr = parse_url($video);
            if (isset($arr['host'])) {
                $host = strtolower($arr['host']);
            }
            $permitidos = array('youtube', 'vimeo');
            $valido = false;
            foreach ($permitidos as $hostPermitido) {
                if (strpos($host, $hostPermitido) !== false) {
                    $valido = true;
                    break;
                }
            }
            if (!$valido) {
                $erros[] = sprintf('Site do vídeo não é permitido. Os serviços permitidos são: %s.', implode(', ', $permitidos));
            }
        }
        
        return parent::myValidate($erros, $columns);
    }

    /**
     * @param null $fieldName
     * @return string|null
     * @throws Exception
     */

    public function getUrlImageS3($fieldName = null) {

        $backup = $this->strPhpNameImagem;

        if (!is_null($fieldName)) {
            $this->strPhpNameImagem = $fieldName;
        }

        $s3Uploader = new S3Uploader('suporte');


        $response = $s3Uploader->getArchiveS3($this->_getImagem());

        $this->strPhpNameImagem = $backup;

        return $response;
    }

}
