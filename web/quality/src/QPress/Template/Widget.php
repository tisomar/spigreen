<?php

namespace QPress\Template;

class Widget
{

    public static function render($name, $variables = array(), $returnHtml = false)
    {
        if (is_string($variables))
        {
            $variables = array('content' => $variables);
        } else {
            if (!isset($variables['strIncludesKey'])) {
                $variables['strIncludesKey'] = $GLOBALS['strIncludesKey'];
            }
            $variables['container'] = $GLOBALS['container'];
        }


        if (file_exists($name))
        {
            $template = $name;
        }
        elseif (file_exists(__DIR__ . '/Resources/widget/' . $name . '.php'))
        {
            $template = __DIR__ . '/Resources/widget/' . $name . '.php';
        }
        else
        {
            throw new \Exception('Template não encontrado "' . $name . '"');
        }

        $content = static::load_contents($template, $variables);
        if ($returnHtml)
        {
            return $content;
        }

        echo $content;
    }

    public static function load_contents($_filename, $_args = array())
    {
        if (is_file($_filename))
        {
            if (ob_start())
            {
                extract($_args, EXTR_PREFIX_INVALID, 'arg');
                include $_filename;
                $_contents = ob_get_contents();
                ob_end_clean();
                return $_contents;
            }
            else
            {
                throw new \Exception('Não foi possível iniciar o buffer.');
            }
        }
        else
        {
            throw new \Exception(sprintf('Não foi possível localizar o arquivo "%s"', $_filename));
        }
    }

}
