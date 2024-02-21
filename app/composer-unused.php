<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use Webmozart\Glob\Glob;

return static function (Configuration $config): Configuration {
    $config
         ->addNamedFilter(NamedFilter::fromString('ext-iconv'))
         ->addNamedFilter(NamedFilter::fromString('doctrine/doctrine-migrations-bundle'))
         ->addNamedFilter(NamedFilter::fromString('league/flysystem-bundle'))
         ->addNamedFilter(NamedFilter::fromString('phpdocumentor/reflection-docblock'))
         ->addNamedFilter(NamedFilter::fromString('symfony/doctrine-messenger'))
         ->addNamedFilter(NamedFilter::fromString('symfony/flex'))
         ->addNamedFilter(NamedFilter::fromString('symfony/monolog-bridge'))
         ->addNamedFilter(NamedFilter::fromString('symfony/monolog-bundle'))
         ->addNamedFilter(NamedFilter::fromString('symfony/twig-bundle'))
         ->addNamedFilter(NamedFilter::fromString('symfony/webpack-encore-bundle'))
        ->setAdditionalFilesFor('icanhazstring/composer-unused', [
            __FILE__,
            ...Glob::glob(__DIR__ . '/config/*.php'),
        ]);

    // symfony/serializer with php8.1 installs a version that is no longer suggesting property-access
//    if (PHP_VERSION_ID >= 80100) {
//        $config->addNamedFilter(NamedFilter::fromString('symfony/property-access'));
//    }

    return $config;
};