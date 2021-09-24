<?php

define('MEMORY_TO_ALLOCATE', '256M');
define('CURRENT_DIR', dirname(__FILE__));
define('CACHE_DIR_NAME', '/imagecache/');
define('RESIZE_DIR_NAME', '/resize');
define('CACHE_DIR', CURRENT_DIR . CACHE_DIR_NAME);

/**
 * Script para redimensionamento de imagens
 * @author Rodrigo Antunes
 * @date 10/10/2012
 * @param $path - Caminho onde se encontra a imagem original
 * @param $image - Nome da imagem com sua devida extensÃ£o
 * @param $imageWidth - Largura para a nova imagem (em pixels)
 * @param $imageHeight - Altura para a nova imagem (em pixels)
 * @param $imageCropratio - Cropratio (formato 1:1)
 * @param $imageQuality - Qualidade da nova imagem (Ex: 80 = 80% da qualidade da imagem original)
 * @param bool $force
 * @return string caminho para a imagem gerada, ou false, caso haja algum problema no redimensionamento
 */
function resizeImage($path, $image, $imageWidth = 0, $imageHeight = 0, $imageCropratio = 0, $imageQuality = 100, $force = false) {
    $imagePath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path) . $image;

    if (!file_exists($path . $image)) {
        return false;
    }

    $imageCache = $imageWidth . 'x' . $imageHeight . 'x' . $imageQuality;
    if ($imageCropratio !== 0) {
        $imageCache .= 'x' . (string) $imageCropratio;
    }

    $imageCache .= '-' . $imagePath;

    $imageMD5 = md5($imageCache);

    $pathImageMD5 = CACHE_DIR . $imageMD5;

    // Verifico se já foi gerado cache para a imagem, caso sim, apenas retorno o caminho deste cache
    if (file_exists($pathImageMD5)) {
        if ($force == false) {
            return BASE_URL_ASSETS . RESIZE_DIR_NAME . CACHE_DIR_NAME . $imageMD5;
        } else {
            unlink($pathImageMD5);
        }
    }

    // Recupero o MIME Type da imagem
    $size = getimagesize($path . $image);
    $mime = $size['mime'];

    // Verifico se $image é realmente uma imagem
    if (substr($mime, 0, 6) != 'image/') {
        return false;
    }

    $width = $size[0];
    $height = $size[1];

    $maxWidth = $imageWidth;
    if ($maxWidth == 0) {
        $maxWidth = ($width * $imageHeight) / $height;
    }

    $maxHeight = $imageHeight;
    if ($maxHeight == 0) {
        $maxHeight = ($height * $imageWidth) / $width;
    }

    if ($maxWidth == 0) {
        $maxWidth = $width;
    }
    if ($maxHeight == 0) {
        $maxHeight = $height;
    }

    $poswidth = 50;
    $posheight = 50;

    /*
     * Se os redimensionamentos da imagem forem menores que as dimensÃµes
     * de redimensionamento, apenas retornamos o caminho da imagem
     */
    if ($maxWidth >= $width && $maxHeight >= $height) {
        return $imagePath;
    }

    // Crop Ratio
    $offsetX = 0;
    $offsetY = 0;

    if ($imageCropratio !== 0) {
        $cropRatio = explode(':', (string) $imageCropratio);
        if (count($cropRatio) == 2) {
            $ratioComputed = $width / $height;
            $cropRatioComputed = (float) $cropRatio[0] / (float) $cropRatio[1];

            if ($ratioComputed < $cropRatioComputed) { // Imagem Ã© muito alta, cortamos em cima e em baixo
                $origHeight = $height;
                $height = $width / $cropRatioComputed;
                $offsetY = ($posheight > 0) ? ($origHeight - $height) / (100 / $posheight) : 0;
            } else if ($ratioComputed > $cropRatioComputed) { // Imagem Ã© muito larga, cortamos as laterais
                $origWidth = $width;
                $width = $height * $cropRatioComputed;
                $offsetX = ($poswidth > 0) ? ($origWidth - $width) / (100 / $poswidth) : 0;
            }
        }
    }

    $xRatio = $maxWidth / $width;
    $yRatio = $maxHeight / $height;

    if ($xRatio * $height < $maxHeight) {
        $tnHeight = ceil($xRatio * $height);
        $tnWidth = $maxWidth;
    } else {
        $tnWidth = ceil($yRatio * $width);
        $tnHeight = $maxHeight;
    }

    // Qualidade da imagem
    $quality = $imageQuality;

    // Nome da imagem
//    $resizedImageSource = $tnWidth . 'x' . $tnHeight . 'x' . $quality;
//
//    if ($imageCropratio !== 0) {
//        $resizedImageSource .= 'x' . (string) $imageCropratio;
//    }
//
//    $resizedImageSource .= '-' . $imagePath;
//    $resizedImage = md5($resizedImageSource);
//    $resized = CACHE_DIR . $resizedImage;

    $resizedImage = $imageMD5;
    $resized = $pathImageMD5;

    // Seto o limite de memÃ³ria
    ini_set('memory_limit', MEMORY_TO_ALLOCATE);

    // Crio uma imagem padrÃ£o com o novo tamanho
    $dst = imagecreatetruecolor($tnWidth, $tnHeight);

    // Seto as caracterÃ­sticas da imagem de acordo com o MIME Type
    switch ($size['mime']) {
        case 'image/gif':
            $creationFunction = 'ImageCreateFromGif';
            $outputFunction = 'ImagePng';
            $mime = 'image/png';
            $doSharpen = FALSE;
            $quality = round(10 - ($quality / 10));
            break;
        case 'image/x-png':
        case 'image/png':
            $creationFunction = 'ImageCreateFromPng';
            $outputFunction = 'ImagePng';
            $doSharpen = FALSE;
            $quality = round(10 - ($quality / 10));
            break;
        default:
            $creationFunction = 'ImageCreateFromJpeg';
            $outputFunction = 'ImageJpeg';
            $doSharpen = TRUE;
            break;
    }

    // Lemos a imagem original
    $src = $creationFunction($path . $image);

    // Seto a transparÃªncia de fundo para arquivos gif e png
    if (in_array($size['mime'], array('image/gif', 'image/png'))) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    // Copio a imagem com as novas dimensÃµes
    ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);

    if ($doSharpen) {
        $sharpness = findSharp($width, $tnWidth);

        $sharpenMatrix = array(
            array(-1, -2, -1),
            array(-2, $sharpness + 12, -2),
            array(-1, -2, -1)
        );
        $divisor = $sharpness;
        $offset = 0;
        imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
    }

    // Verifico se a pasta de cache existe, se não existir, crio esta pasta
    if (!file_exists(CACHE_DIR))
        mkdir(CACHE_DIR, 0755);

    // Verifico se tenho permissÃ£o para gravar na pasta
    if (!is_writable(CACHE_DIR)) {
        return false;
    }

    // Salvo a imagem redimensionada na pasta de cache
    $outputFunction($dst, $resized, $quality);

    // Limpo a memÃ³ria
    ImageDestroy($src);
    ImageDestroy($dst);

    // Retorno o caminho da imagem
    return BASE_URL_ASSETS . RESIZE_DIR_NAME . CACHE_DIR_NAME . $resizedImage;
}

function findSharp($orig, $final) { // function from Ryan Rud (http://adryrun.com)
    $final = $final * (750.0 / $orig);
    $a = 52;
    $b = -0.27810650887573124;
    $c = .00047337278106508946;

    $result = $a + $b * $final + $c * $final * $final;

    return max(round($result), 0);
}