<?php

use GoalAPI\SDKBundle\GoalAPISDKBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new MonologBundle(),
            new GoalAPISDKBundle()
        );

        return $bundles;
    }

    function registerContainerConfiguration(LoaderInterface $loader)
    {
        $configToLoad = __DIR__.'/config/config_'.$this->getEnvironment().'.yml';
        if (!is_readable($configToLoad)) {
            $configToLoad = __DIR__.'/config/config.yml';
        }
        $loader->load($configToLoad);
    }
}
