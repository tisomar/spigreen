<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new QualityPress\Framework\QualityPressFrameworkBundle(),

            //new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', __DIR__, $this->getEnvironment()));
    }

    public function getLogDir()
    {
        return __DIR__ . '/../var/log';
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../var/cache/' . $this->getEnvironment();
    }

}