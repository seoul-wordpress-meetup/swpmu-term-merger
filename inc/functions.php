<?php
// global namespace

use SWPMU\TermMerger\Vendor\Bojaghi\Continy\Continy;
use SWPMU\TermMerger\Vendor\Bojaghi\Continy\ContinyException;
use SWPMU\TermMerger\Vendor\Bojaghi\Continy\ContinyFactory;
use SWPMU\TermMerger\Vendor\Bojaghi\Continy\ContinyNotFoundException;

if (!defined('ABSPATH')) {
    exit;
}

const SWPMU_TMGR_NS_PREFIX = 'SWPMU\\TermMerger\\Vendor\\';

if (!function_exists('strPascalToKebab')) {
    function strPascalToKebab(string $input): string
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '-', $input));
    }
}

if (!function_exists('swpmuAutoloader')) {
    function swpmuAutoloader(string $className): bool
    {
        if (str_starts_with($className, SWPMU_TMGR_NS_PREFIX)) {
            $origin   = substr($className, strlen(SWPMU_TMGR_NS_PREFIX));
            $exploded = explode('\\', $origin);

            if (count($exploded)) {
                $pathPart  = implode(
                    DIRECTORY_SEPARATOR,
                    array_map(fn($ns) => strPascalToKebab($ns), array_slice($exploded, 0, -1)),
                );
                $classPart = $exploded[count($exploded) - 1];
                $thePath   = sprintf(
                    implode(DIRECTORY_SEPARATOR, ['%s', 'vendor', '%s', 'src', '%s.php']),
                    dirname(__DIR__),
                    $pathPart,
                    $classPart,
                );
                if (file_exists($thePath)) {
                    require_once $thePath;
                    return true;
                }
            }
        }
        return false;
    }
}

if (!function_exists('swpmuTmgr')) {
    /**
     * Wrapper function
     *
     * @return Continy
     */
    function swpmuTmgr(): Continy
    {
        static $continy = null;

        if (is_null($continy)) {
            $continy = ContinyFactory::create(dirname(__DIR__) . '/conf/continy.php');
        }

        return $continy;
    }
}

if (!function_exists('swpmuTmgrGet')) {
    /**
     * @template T
     * @param class-string<T> $id
     *
     * @return T|object|null
     */
    function swpmuTmgrGet(string $id)
    {
        try {
            $instance = swpmuTmgr()->get($id);
        } catch (ContinyException|ContinyNotFoundException $e) {
            $instance = null;
        }

        return $instance;
    }
}
