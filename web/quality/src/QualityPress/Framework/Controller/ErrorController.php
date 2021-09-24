<?php

namespace QualityPress\Framework\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ErrorController extends Controller
{

    public function showErrorAction(Request $request)
    {
        return new Response($this->renderView(DIR_ROOT . '/qcommerce/pagina-nao-encontrada/index.php', array(
            'request' => $request
        )));
    }

}