<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 10/17/18
 * Time: 1:07 PM
 */

namespace Jeeno\LaravelIntegration\Config;

use League\Flysystem\Filesystem;
use Orchestra\Testbench\TestCase;

/**
 * Class LocalFileWriterFactoryTest
 *
 * @package Jeeno\LaravelIntegration\Config
 */
class LocalFileWriterFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider writerProvider
     *
     * @param string $folder
     * @param string $configMethod
     * @param string $factoryMethod
     */
    public function shouldGetAWriter(string $folder, string $configMethod, string $factoryMethod)
    {
        $domain  = 'MyDomain';
        $appPath = 'myProject/app';
        $config  = $this->mockConfig($appPath, $configMethod, $folder);
        $path    = "{$appPath}/Domain/{$domain}/{$folder}";

        $filesystemFactory = $this->mockFilesystemFactory($path, false);
        $fileWriterFactory = new LocalFileWriterFactory($config, $filesystemFactory);

        $fileWriterFactory->{$factoryMethod}($domain);
    }

    /**
     * @return array
     */
    public function writerProvider(): array
    {
        return [
            ['Controller', 'getControllerFolder', 'getControllerWriter'],
            ['Entity', 'getEntitiesFolder', 'getEntityWriter'],
            ['Provider', 'getProviderFolder', 'getProviderWriter'],
            ['Repository', 'getRepositoriesFolder', 'getRepositoryWriter'],
            ['Routes', 'getRoutesFolder', 'getRoutesWriter'],
        ];
    }

    /**
     * @param string $appPath
     * @param string $method
     * @param string $value
     *
     * @return JeenoConfig|\Mockery\MockInterface
     */
    private function mockConfig(string $appPath, string $method, string $value)
    {
        $mock = \Mockery::mock(JeenoConfig::class);

        $mock->shouldReceive('getAppPath')
             ->once()
             ->andReturn($appPath);

        $mock->shouldReceive($method)
             ->with()
             ->once()
             ->andReturn($value);

        return $mock;
    }


    /**
     * @return FilesystemFactory|\Mockery\MockInterface
     */
    private function mockFilesystemFactory(string $path, bool $append)
    {
        $mock = \Mockery::mock(FilesystemFactory::class);

        $mock->shouldReceive('getLocalFilesystem')
             ->with($path, $append)
             ->andReturn($this->mockFilesystem());

        return $mock;
    }

    /**
     * @return Filesystem|\Mockery\MockInterface
     */
    private function mockFilesystem()
    {
        $mock = \Mockery::mock(Filesystem::class);

        return $mock;
    }
}
