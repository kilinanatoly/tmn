<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitb1eb0f9d0df5062035707550d49b1f61
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitb1eb0f9d0df5062035707550d49b1f61', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitb1eb0f9d0df5062035707550d49b1f61', 'loadClassLoader'));

        if (PHP_VERSION_ID >= 50600) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(\Composer\Autoload\ComposerStaticInitb1eb0f9d0df5062035707550d49b1f61::getInitializer($loader));
        } else {
            $map = require __DIR__ . '/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require __DIR__ . '/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        if (PHP_VERSION_ID >= 50600) {
            $includeFiles = Composer\Autoload\ComposerStaticInitb1eb0f9d0df5062035707550d49b1f61::$files;
        } else {
            $includeFiles = require __DIR__ . '/autoload_files.php';
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequireb1eb0f9d0df5062035707550d49b1f61($fileIdentifier, $file);
        }

        return $loader;
    }
}

function composerRequireb1eb0f9d0df5062035707550d49b1f61($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}
