<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit05e79f1c0a61bd31841b9c7a1b54b18c
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'ColorThief\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ColorThief\\' => 
        array (
            0 => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief',
        ),
    );

    public static $classMap = array (
        'ColorThief\\Color' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Color.php',
        'ColorThief\\ColorThief' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/ColorThief.php',
        'ColorThief\\Exception\\Exception' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Exception/Exception.php',
        'ColorThief\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Exception/InvalidArgumentException.php',
        'ColorThief\\Exception\\NotReadableException' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Exception/NotReadableException.php',
        'ColorThief\\Exception\\NotSupportedException' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Exception/NotSupportedException.php',
        'ColorThief\\Exception\\RuntimeException' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Exception/RuntimeException.php',
        'ColorThief\\Image\\Adapter\\AbstractAdapter' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/Adapter/AbstractAdapter.php',
        'ColorThief\\Image\\Adapter\\AdapterInterface' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/Adapter/AdapterInterface.php',
        'ColorThief\\Image\\Adapter\\GdAdapter' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/Adapter/GdAdapter.php',
        'ColorThief\\Image\\Adapter\\GmagickAdapter' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/Adapter/GmagickAdapter.php',
        'ColorThief\\Image\\Adapter\\ImagickAdapter' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/Adapter/ImagickAdapter.php',
        'ColorThief\\Image\\ImageLoader' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/Image/ImageLoader.php',
        'ColorThief\\PQueue' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/PQueue.php',
        'ColorThief\\VBox' => __DIR__ . '/..' . '/ksubileau/color-thief-php/src/ColorThief/VBox.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit05e79f1c0a61bd31841b9c7a1b54b18c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit05e79f1c0a61bd31841b9c7a1b54b18c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit05e79f1c0a61bd31841b9c7a1b54b18c::$classMap;

        }, null, ClassLoader::class);
    }
}
