<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 10/10/18
 * Time: 4:51 PM
 */

namespace Jeeno\LaravelIntegration\Config;


/**
 * Interface JeenoConfig
 *
 * @package Jeeno\LaravelIntegration\Config
 */
interface JeenoConfig
{
    /**
     * @return string
     */
    public function getAppPath():string;

    /**
     * @return string
     */
    public function getEntitiesFolder():string;

    /**
     * @return string
     */
    public function getRepositoriesFolder():string;

    /**
     * @return string
     */
    public function getControllerFolder():string;

    /**
     * @return string
     */
    public function getProviderFolder():string;


    /**
     * @return string
     */
    public function getRoutesFolder():string;
}