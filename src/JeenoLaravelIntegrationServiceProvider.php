<?php

namespace Jeeno\LaravelIntegration;

use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\ServiceProvider;
use Jeeno\Core\Entity\Entity;
use Jeeno\Core\Helper\ModelSerializer;
use Jeeno\Core\Helper\ModelSerializerDefault;
use Jeeno\LaravelIntegration\Config\FileWriterFactory;
use Jeeno\LaravelIntegration\Config\JeenoConfig;
use Jeeno\LaravelIntegration\Config\LocalFileWriterFactory;
use Jeeno\LaravelIntegration\Config\StaticConfig;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class JeenoLaravelIntegrationServiceProvider
 *
 * @package Jeeno\LaravelIntegration
 */
class JeenoLaravelIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/doctrine.php' => config_path('doctrine.php'),
            __DIR__.'/../mappings/Entity.Namespace.dcm.xml' => config_path('mappings/Entity.Namespace.dcm.xml'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../templates', 'jeeno');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ModelSerializer::class,
            function () {
                $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

                $getSetMethodNormalizer = new ObjectNormalizer($classMetadataFactory);
                $getSetMethodNormalizer->setCircularReferenceLimit(0);
                $getSetMethodNormalizer->setCircularReferenceHandler(function (Entity $object) {
                    return $object->getId();
                });

                $normalizers = [new DateTimeNormalizer('Y-m-d H:i:s'), $getSetMethodNormalizer];
                $encoders    = [new JsonEncoder()];
                $serializer  = new Serializer ($normalizers, $encoders);

                return new ModelSerializerDefault($serializer);
            }
        );

        $this->app->bind(JeenoConfig::class, StaticConfig::class);
        $this->app->bind(FileWriterFactory::class, LocalFileWriterFactory::class);
    }
}
