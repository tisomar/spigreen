<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_hotsite' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Hotsite extends BaseHotsite
{
	public $strPrefixFileName = 'HOTSITE'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
	public $strPathImg = '/arquivos/hotsite/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
	public $strPhpNameImagem = 'Foto'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
	public $strNodeName = 'hotsite';
	public $allowedExtentions = array('jpg', 'jpeg', 'png', 'gif');
	
	public function delete(PropelPDO $con = null)
	{
		try {
			$this->deleteImagem(); // apaga a imagem na chamada do metodo delete.
			parent::delete($con);
		} catch (Exception $e) {
			throw $e;
		}
		
	}
	
	/**
	 * Função que sobreescreve os valores padrao da função getThumb do BaseObject
	 * @date 06/04/2010
	 * @author Jaison Vargas Veneri
	 * @see BaseObject->getThumb
	 * @param string $strArgs
	 * @param array $arrAtributtes
	 * @param boolean $boolUseImagemPadrao
	 * @return string
	 */
	public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
	{
		$arrAtributtes['alt'] = escape($this->getUrl());
		$arrAtributtes['title'] = escape($this->getUrl());
		
		return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
	}
}
