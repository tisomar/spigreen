<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_NEWSLETTER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class NewsletterPeer extends BaseNewsletterPeer
{

    const CSRF_HASH = '___hash_newsletter';

    public static function save($email, $nome = null)
    {

        $erros = array();
        $nomeFlash = '';

        $objNewsletter = new Newsletter();
        $objNewsletter->setEmail($email);

        if (!is_null($nome))
        {
            $objNewsletter->setNome($nome);
            $nomeFlash = ' '.$nome;
        }

        $objNewsletter->myValidate($erros);

        if (count($erros) == 0)
        {
            $objNewsletter->save();
            FlashMsg::success('Parabéns'.$nomeFlash.'! Você foi cadastrado com sucesso em nossa newsletter.');
            unset($objNewsletter);
        }
        else
        {
            return true;
            // Retirada a verificação de erro
            /*
            foreach ($erros AS $erro)
            {
                FlashMsg::danger($erro);
            }
            return false;*/
        }
        
        return true;
    }
}
