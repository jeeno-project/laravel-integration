<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 10/17/18
 * Time: 1:26 PM
 */

namespace Jeeno\LaravelIntegration\Config;


use Jeeno\Core\Helper\PropertyHelper;
use League\Flysystem\Adapter\Local;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Assert;

/**
 * Class FilesystemFactoryTest
 *
 * @package Jeeno\LaravelIntegration\Config
 */
class FilesystemFactoryTest extends TestCase
{
    /**
     * @test
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function shouldReturnsLocalFilesystem()
    {
        $path = app_path('test.php');

        $factory = new FilesystemFactory();

        $filesystem = $factory->getLocalFilesystem($path);

        $adapter = $filesystem->getAdapter();

        Assert::assertNotNull($adapter);
        Assert::assertInstanceOf(Local::class, $adapter);

        Assert::assertEquals(LOCK_EX, PropertyHelper::getProperty($adapter, 'writeFlags'));
    }
}
