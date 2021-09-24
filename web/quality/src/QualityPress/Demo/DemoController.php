<?php

namespace QualityPress\Demo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class DemoController extends Controller
{

    public function indexAction(Request $request)
    {
        return $this->render('home/index.html.twig');
    }

}