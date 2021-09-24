<?php
//var_dump(123);die;

include __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Description of QPTranslator
 *
 * @author André Garlini
 */

class QPTranslator
{
    
    const SESSION_KEY = 'qp_locale';
    
    const LOCALE_PADRAO = 'pt';
    
    /**
     *
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    protected static $translator;

        /**
     * Retorna o locale ativo.
     *
     * @return string
     */
    public static function getLocale()
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            return $_SESSION[self::SESSION_KEY];
        }
        
        $ret = self::LOCALE_PADRAO;
        
        //tenta detectar pelo header enviado pelo browser
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $language = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            if (in_array($language, array_keys(self::getOptionsLocales()))) {
                $ret = $language;
            }
        }
        
        $_SESSION[self::SESSION_KEY] = $ret;
        
        return $ret;
    }
    
    /**
     * Coloca o locale passado como argumento na sessão.
     *
     * @param string $locale
     * @throws InvalidArgumentException
     */
    public static function setLocale($locale)
    {
        $locale = strtolower($locale);
        
        $options = self::getOptionsLocales();
        
        if (isset($options[$locale])) {
            $_SESSION[self::SESSION_KEY] = $locale;
        } else {
            throw new InvalidArgumentException('Locale inválido.');
        }
    }
    
    /**
     * Retorna um array de opções de locales.
     *
     * @return array
     */
    public static function getOptionsLocales($locale = null)
    {
        $options =  array(
            'pt'    => 'Português',
            'fr'    => 'Francês',
            'en'    => 'Inglês',
            'es'    => 'Espanhol',
            'de'    => 'Alemão'
        );

        if ($locale == null) {
            return $options;
        } else {
            return $options[$locale];
        }
    }

    /**
     * Retorna um array de opções de locales.
     *
     * @return array
     */
    public static function getTitleLocales($locale = null)
    {
        $options =  array(
            'pt'    => 'Idioma',
            'fr'    => 'Langage',
            'en'    => 'Language',
            'es'    => 'Idioma',
            'de'    => 'Sprache'
        );

        if ($locale == null) {
            return $options;
        } else {
            return $options[$locale];
        }
    }

    /**
     *
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public static function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {

        if (!self::$translator) {
            self::loadTranslator();
        }
        
        if (null === $locale) {
            $locale = self::getLocale();
        }
        
        return self::$translator->trans($id, $parameters, $domain, $locale);
    }
    
    
    /**
     *
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public static function transChoice($id, $number, $parameters = array(), $domain = null, $locale = null)
    {
        if (!self::$translator) {
            self::loadTranslator();
        }
        
        if (null === $locale) {
            $locale = self::getLocale();
        }
        
        return self::$translator->transChoice($id, $number, $parameters, $domain, $locale);
    }
    
    /**
     *
     * @return Symfony\Component\Translation\TranslatorInterface
     */
    protected static function loadTranslator()
    {
        $cacheDir = __DIR__ . '/../cache/translations/';

//        $debug = (bool)$GLOBALS['modoDebug'];

        self::$translator = $translator = new Translator(self::LOCALE_PADRAO, null, $cacheDir);

        $translator->addLoader('yaml', new YamlFileLoader());
        
        $path = __DIR__ . '/../translations';
        $translator->addResource('yaml', $path . '/messages.pt.yml', 'pt');
        $translator->addResource('yaml', $path . '/messages.fr.yml', 'fr');
        $translator->addResource('yaml', $path . '/messages.en.yml', 'en');
        $translator->addResource('yaml', $path . '/messages.es.yml', 'es');
        $translator->addResource('yaml', $path . '/messages.de.yml', 'de');
        
        $translator->setFallbackLocales(array(self::LOCALE_PADRAO));
        
        return self::$translator;
    }
}
