<?php
// global namespace

use SWM\TermMerger\Vendor\Bojaghi\Continy\Continy;
use SWM\TermMerger\Vendor\Bojaghi\Continy\ContinyException;
use SWM\TermMerger\Vendor\Bojaghi\Continy\ContinyFactory;
use SWM\TermMerger\Vendor\Bojaghi\Continy\ContinyNotFoundException;

const SWM_TMGR_NS_PREFIX = 'SWM\\TermMerger\\Vendor\\';

if (!function_exists('strPascalToKebab')) {
    function strPascalToKebab(string $input): string
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '-', $input));
    }
}

if (!function_exists('swmTmgrAutoloader')) {
    function swmTmgrAutoloader(string $className): bool
    {
        if (str_starts_with($className, SWM_TMGR_NS_PREFIX)) {
            $origin   = substr($className, strlen(SWM_TMGR_NS_PREFIX));
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

if (!function_exists('swmTmgr')) {
    /**
     * Wrapper function
     *
     * @return Continy
     */
    function swmTmgr(): Continy
    {
        static $continy = null;

        if (is_null($continy)) {
            $continy = ContinyFactory::create(dirname(__DIR__) . '/conf/continy.php');
        }

        return $continy;
    }
}

if (!function_exists('swmTmgrGet')) {
    /**
     * @template T
     * @param class-string<T> $id
     *
     * @return T|object|null
     */
    function swmTmgrGet(string $id)
    {
        try {
            $instance = swmTmgr()->get($id);
        } catch (ContinyException|ContinyNotFoundException $e) {
            $instance = null;
        }

        return $instance;
    }
}
