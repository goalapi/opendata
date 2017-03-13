<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \GoalAPI\SDKBundle\GoalAPISDKBundle(),
            new \GoalAPI\OpenData\Bundle\AppBundle\AppBundle(),
        );
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        }

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

    /**
     * @return string
     */
    public function getLogDir()
    {
        $envParams = $this->getEnvParameters();
        if (array_key_exists('logs_dir', $envParams)) {
            $relativeLogsPath = $envParams['logs_dir'];
        } else {
            $relativeLogsPath = 'var/logs';
        }

        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$relativeLogsPath;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return __DIR__.'/../var/cache/'.$this->environment;
    }
}
