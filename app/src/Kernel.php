<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Override;

use function dirname;
use function is_file;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    #[Override]
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class() implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition('doctrine.orm.default_configuration')
                    ->addMethodCall(
                        'setIdentityGenerationPreferences',
                        [
                            [
                                PostgreSQLPlatform::class => ClassMetadataInfo::GENERATOR_TYPE_SEQUENCE,
                            ],
                        ]
                    );
            }
        });
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/services.yaml') === true) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
        } else {
            $path = dirname(__DIR__) . '/config/services.php';

            if (is_file($path) === true) {
                (include $path)($container->withPath($path), $this);
            }
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/routes.yaml') === true) {
            $routes->import('../config/routes.yaml');
        } else {
            $path = dirname(__DIR__) . '/config/routes.php';

            if (is_file($path) === true) {
                (include $path)($routes->withPath($path), $this);
            }
        }
    }
}
