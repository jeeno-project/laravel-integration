<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 10/17/18
 * Time: 1:09 PM
 */

namespace Jeeno\LaravelIntegration\Config;

use PHPUnit\Framework\Assert;
use Orchestra\Testbench\TestCase;

/**
 * Class StaticConfigTest
 *
 * @package Jeeno\LaravelIntegration\Config
 */
class StaticConfigTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnsTheRightPaths()
    {
        $config = new StaticConfig();

        Assert::assertEquals(app_path(), $config->getAppPath());
        Assert::assertEquals('Controllers', $config->getControllerFolder());
        Assert::assertEquals('Entities', $config->getEntitiesFolder());
        Assert::assertEquals('', $config->getProviderFolder());
        Assert::assertEquals('Repositories', $config->getRepositoriesFolder());
        Assert::assertEquals('', $config->getRoutesFolder());
    }
}
