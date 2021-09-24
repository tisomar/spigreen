<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 22/02/2018
 * Time: 08:51
 */

namespace classes;


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class S3Uploader
{

    protected $bucket;

    protected $s3Cliente;

    const KEY_S3 = 'AKIAJC6PL56TFGZUCTRA';
    const SECRET_S3 = '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';
    const VERSION = 'latest';
    const REGION = 'sa-east-1';
    const HTTP_VERIFY = 'C:/xampp/php/ext/cacert.pem';
    const BUCKET_HASHKEY = 'prd-spigreen';
    const DS = '/';
    const HTTP_REQUEST = 'https://';
    const LINK_SITE = '.s3.amazonaws.com';
    const URL_CDN = 'cdn.redefacilbrasil.com.br';

    /**
     *
     * Construtor
     *
     * @param string $bucket
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    function __construct($bucket)
    {
        if (!$bucket) {
            throw new \InvalidArgumentException('Bucket não informado.');
        }
        $this->setBucket($bucket);
        
        $configS3 = $this->configS3Client();

        $cont = false;

        try {
            $s3 = new S3Client($configS3);
            $cont = count($s3->listBuckets()['Buckets']);
        } catch (S3Exception $e) {
            $cont = false;

            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            $cont = false;
            throw new \Exception($e->getMessage());
        }

        if ($cont === false) {
            throw new \Exception('S3 não está acessível.');
        }

        $this->setS3Cliente($s3);

        if (!$this->getS3Cliente()->doesBucketExist(S3Uploader::BUCKET_HASHKEY)) {
            $this->getS3Cliente()->createBucket(array('Bucket' => S3Uploader::BUCKET_HASHKEY));
        }
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return S3Client
     */
    public function getS3Cliente()
    {
        return $this->s3Cliente;
    }

    /**
     * @param S3Client $s3Cliente
     */
    public function setS3Cliente($s3Cliente)
    {
        $this->s3Cliente = $s3Cliente;
    }

    /**
     *
     * Faz o upload dos arquivos/imagens no S3 para uso posterior.
     *
     * @param $nomeArquivo
     * @param $pathArquivo
     * @param string $contentType
     * @param $isResize
     * @throws \Exception
     * @return bool
     *
     */

    public function uploadArchiveS3($nomeArquivo, $pathArquivo, $contentType = null, $isResize = null, $nomeArquivoUpload = null)
    {

        try {
            $s3 = $this->getS3Cliente();
            $cont = count($s3->listBuckets()['Buckets']);
        } catch (\Exception $e) {
            $cont = false;
            throw new \Exception('S3 não está acessível.');
        }

        if ($cont === false) {
            throw new \Exception('S3 não está acessível.');
        }

        if (is_null($contentType)) {
            $contentType = 'image/jpeg';
        }

        if (!$this->getBucket()) {
            throw new \Exception('Bucket não informado, inicie a classe S3 e informe o Bucket.');
        }

        /*if(!@getimagesize($pathArquivo.$nomeArquivo)){
            throw new \Exception('Arquivo não encontrado no servidor.');
        }*/

        $contentTypeValides = $this->getContentTypesValides();

        if (!in_array($contentType, $contentTypeValides)) {
            throw new \Exception('Content Type não suportado.');
        }

        $linkArquivo = $pathArquivo . $nomeArquivo;

        if (!is_null($nomeArquivoUpload)) {
            $linkArquivo = $pathArquivo . $nomeArquivoUpload;
        }

        $contentTypeResizable = $this->getContentTypesResizables();

        if (!is_null($isResize) && in_array($contentType, $contentTypeResizable)) {
            if (!is_array($isResize) || !isset($isResize['size']) || !isset($isResize['name'])) {
                throw new \Exception('Dados para resize faltando.');
            }

//            $qpressBase = new \QPressBase();
//            $linkArquivo = $qpressBase->getResizeForS3($isResize['size'], $pathArquivo, $nomeArquivo);
//            $nomeArquivo = $isResize['name'].'-'.$nomeArquivo;
        }


        try {
            $result = $s3->putObject(array(
                'Bucket' => S3Uploader::BUCKET_HASHKEY,
                'Key'   => $this->getBucket() . '/' . $nomeArquivo,
                'SourceFile' => $linkArquivo,
                'contentType' => $contentType,
                'ACL'          => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY'
            ));

            if ($result['@metadata']['statusCode'] == 200) {
                return $result['@metadata']['effectiveUri'];
            } else {
                throw new \Exception('Upload não foi possível, algum erro no upload do arquivo.');
            }
        } catch (\Exception $e) {
            var_dump(1, $e->getMessage());
            die;
            throw new \Exception('Upload não foi possível, algum erro no upload do arquivo.');
        }

        return null;
    }

    /**
     *
     * Trás arquivos do S3
     *
     * @param $nomeArquivo
     * @param $locale
     * @param bool $getMetadata
     * @param string $typeArchive
     * @throws \Exception
     * @return string|null
     *
     */

    public function getArchiveS3($nomeArquivo, $getMetadata = false, $typeArchive = null)
    {
        try {
            $s3 = $this->getS3Cliente();
            $cont = count($s3->listBuckets()['Buckets']);
        } catch (\Exception $e) {
            $cont = false;
            throw new \Exception('S3 não está acessível.');
        }

        if ($cont === false) {
            throw new \Exception('S3 não está acessível.');
        }


        if (!$this->getBucket()) {
            throw new \Exception('Bucket não informado, inicie a classe S3 e informe o Bucket.');
        }

        if (is_null($nomeArquivo)) {
            throw new \Exception('Nome do arquivo inválido.');
        }


        try {
            $result = $s3->getObject(array(
                'Bucket' =>  S3Uploader::BUCKET_HASHKEY,
                'Key' => $this->getBucket() . '/' . $nomeArquivo,
            ));
            
            if ($result['@metadata']['statusCode'] == 200) {
                if ($getMetadata) {
                    return $result['@metadata']['effectiveUri'];
                } else {
                    //return 'https://'.$this->getBucket().'.s3.amazonaws.com/'.$nomeArquivo.(is_null($typeArchive)? '': '.'.$typeArchive);
                    return $result['@metadata']['effectiveUri'];
                }
            } else {
                return null;
                //throw new \Exception('Upload não foi possível, algum erro no upload do arquivo.');
            }
        } catch (\Exception $e) {
            // Catch an S3 specific exception.
            return null;
            //throw new \Exception('Não foi possível buscar o objeto. Ele está inacessível ou nome inválido.');
        }
    }


    /**
     *
     * Trás arquivos do S3
     *
     * @param $nomeArquivo
     * @param $locale
     * @param bool $getMetadata
     * @param string $typeArchive
     * @throws \Exception
     * @return string|null
     *
     */

    public function getArchiveWithoutLocaleS3($nomeArquivo, $getMetadata = false, $typeArchive = null)
    {
        try {
            $s3 = $this->getS3Cliente();
            $cont = count($s3->listBuckets()['Buckets']);
        } catch (\Exception $e) {
            $cont = false;
            throw new \Exception('S3 não está acessível.');
        }

        if ($cont === false) {
            throw new \Exception('S3 não está acessível.');
        }


        if (!$this->getBucket()) {
            throw new \Exception('Bucket não informado, inicie a classe S3 e informe o Bucket.');
        }

        if (is_null($nomeArquivo)) {
            throw new \Exception('Nome do arquivo inválido.');
        }


        try {
            $result = $s3->getObject(array(
                'Bucket' =>  S3Uploader::BUCKET_HASHKEY,
                'Key' => $this->getBucket() . '/' . $nomeArquivo,
            ));

            if ($result['@metadata']['statusCode'] == 200) {
                if ($getMetadata) {
                    return $result['@metadata']['effectiveUri'];
                } else {
                    //return 'https://'.$this->getBucket().'.s3.amazonaws.com/'.$nomeArquivo.(is_null($typeArchive)? '': '.'.$typeArchive);
                    return $result['@metadata']['effectiveUri'];
                }
            } else {
                throw new \Exception('Upload não foi possível, algum erro no upload do arquivo.');
            }
        } catch (\Exception $e) {
            // Catch an S3 specific exception.
            return null;
            //throw new \Exception('Não foi possível buscar o objeto. Ele está inacessível ou nome inválido.');
        }
    }

    /**
     *
     * Trás imagem do S3 e formata em HTML
     *
     * @param $nomeArquivo
     * @param $locale
     * @param bool $getMetadata
     * @param string $typeArchive
     * @param array $atributes
     * @return string|null
     *
     */
    public function getImageS3Formatted($nomeArquivo, $locale = 'pt', $getMetadata = false, $typeArchive = null, $atributes = array())
    {

        $srcImagem = $this->getArchiveS3($nomeArquivo, $locale, $getMetadata, $typeArchive);

        $qpressBase = new \QPressBase();
        return $qpressBase->getThumbS3($srcImagem, $atributes);
    }

    /**
     *
     * Configurações usadas para criar o S3 Client
     *
     * @return array
     *
     */
    protected function configS3Client()
    {
        return array (
            'signature'     => 'v4',
            'version'       => S3Uploader::VERSION,
            'region'        => S3Uploader::REGION,
            'credentials'   => [
                'key'       =>  S3Uploader::KEY_S3,
                'secret'    =>  S3Uploader::SECRET_S3,
            ],
        );
    }

    /**
     *
     * Content-type valides para uso do S3
     *
     * JPG = 'image/jpeg'
     * PDF = 'application/pdf'
     * PNG = 'image/png'
     *
     * @return array
     *
     */
    protected function getContentTypesValides()
    {


        return array (
            'application/pdf',
            'image/jpeg',
            'image/png'
        );
    }

    /**
     *
     * Content-type valides para resize
     *
     * JPG = 'image/jpeg'
     * PNG = 'image/png'
     *
     * @return array
     *
     */
    protected function getContentTypesResizables()
    {


        return array (
            'image/png',
            'image/jpeg'
        );
    }

    public function getLinkS3($nomeArquivo, $locale = null, $size = null, $cdnLink = false)
    {

        $name = $nomeArquivo;
        if (!is_null($size)) {
            $name = $size . '-' . $name;
        }

        if (!is_null($locale)) {
            $name = $locale . '-' . $name;
        }
        if ($cdnLink) {
            return S3Uploader::HTTP_REQUEST . S3Uploader::URL_CDN . S3Uploader::DS . $this->getBucket() . S3Uploader::DS . $name;
        } else {
            return S3Uploader::HTTP_REQUEST . S3Uploader::BUCKET_HASHKEY . S3Uploader::LINK_SITE . S3Uploader::DS . $this->getBucket() . S3Uploader::DS . $name;
        }
    }
}
