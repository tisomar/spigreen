<?php

namespace QPress\Encrypter;

class Encrypter
{

    const SECRET_KEY = 'u@^nI2qs$9sTu%f';
    
    /**
     * crypt()
     * Encriptador de string
     * 
     * @param string $value
     * @return boolean
     */
    public static function crypt($value)
    {
        if (!$value)
        {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $key = hash('sha256', self::SECRET_KEY, true);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
        return trim(self::safe_b64encode($crypttext));
    }

    /**
     * decrypt()
     * Descripta string encriptada pelo método crypt
     * 
     * @param string $value
     * @return boolean
     */
    public static function decrypt($value)
    {
        if (!$value)
        {
            return false;
        }
        $crypttext = self::safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $key = hash('sha256', self::SECRET_KEY, true);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    private static function safe_b64encode($string)
    {
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($string));
    }

    private static function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4)
        {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}
