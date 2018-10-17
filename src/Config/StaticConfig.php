<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 10/10/18
 * Time: 4:54 PM
 */

namespace Jeeno\LaravelIntegration\Config;

/**
 * Class StaticConfig
 *
 * @package Jeeno\LaravelIntegration\Config
 */
class StaticConfig implements JeenoConfig
{
    /**
     * @return string
     */
    public function getAppPath(): string
    {
        return app_path();
    }

    /**
     * @return string
     */
    function getEntitiesFolder(): string
    {
        return 'Entities';
    }

    /**
     * @return string
     */
    function getRepositoriesFolder(): string
    {
        return 'Repositories';
    }

    /**
     * @return string
     */
    public function getControllerFolder(): string
    {
        return 'Controllers';
    }

    /**
     * @return string
     */
    public function getProviderFolder(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRoutesFolder(): string
    {
        return '';
    }


}