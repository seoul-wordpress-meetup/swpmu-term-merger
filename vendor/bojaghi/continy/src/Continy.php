<?php

declare(strict_types=1);

namespace SWPMU\TermMerger\Vendor\Bojaghi\Continy;

use SWPMU\TermMerger\Vendor\Bojaghi\Contract\Container;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Continy - A tiny container class for WordPress plugin and theme development that supports really simple D.I.
 *
 * Glossary
 * --------
 * Component: Any instance which Continy can store. Usually continy itself, module instances, and support instances.
 * Module:    A special form of component for initializing plugin or theme.
 *            Instantiated by add_action() function call when Continy is getting started.
 * Support:   Another special form of component. As the name says, it is to assist module components in many ways.
 * Alias:
 */
class Continy implements Container
{
    /* Priority constants */
    public const PR_URGENT    = -10000;
    public const PR_VERY_HIGH = 1;
    public const PR_HIGH      = 5;
    public const PR_DEFAULT   = 10;
    public const PR_LOW       = 50;
    public const PR_VERY_LOW  = 100;
    public const PR_LAZY      = 10000;

    private string $mainFile = '';
    private string $version  = '';

    /**
     * Component storage.
     *
     * Key:   FQCN
     * Value: object
     *
     * @var array<string, mixed>
     */
    private array $storage = [];

    /**
     * Array of component names that are resolved.
     *
     * Key:   FQCN, or alias string
     * Value: FQCN
     *
     * @var array<string, string>
     */
    private array $resolved = [];

    /**
     * Array of arguments
     */
    private array $arguments = [];

    /**
     * @throws \Bojaghi\Continy\ContinyException
     */
    public function __construct(array $args = [])
    {
        $defaults = [
            'main_file' => '',
            'version'   => '0.0.0',
            'hooks'     => [],
            'bindings'  => [],
            'modules'   => [],
            'arguments' => [],
        ];

        $args = wp_parse_args($args, $defaults);

        if (empty($args['main_file'])) {
            throw new ContinyException("'main_file' is required");
        }

        if (empty($args['version'])) {
            $args['version'] = $defaults['version'];
        }

        $this->mainFile  = $args['main_file'];
        $this->version   = $args['version'];
        $this->arguments = $args['arguments'];

        $this->initialize($args['hooks'], $args['bindings'], $args['modules']);
    }

    /**
     * More specific initialization
     *
     * @param array $hooks
     * @param array $bindings
     * @param array $modules
     *
     * @used-by __construct()
     *
     * @return void
     * @throws ContinyException
     */
    protected function initialize(array $hooks, array $bindings, array $modules): void
    {
        // Manually assign continy itself.
        $this->resolved['continy'] = __CLASS__;
        $this->resolved[__CLASS__] = __CLASS__;
        $this->storage[__CLASS__]  = $this;

        // Binding initialization.
        foreach ($bindings as $alias => $fqcn) {
            $this->resolved[$alias] = $fqcn;
        }

        // Planned hooks.
        $hooks = wp_parse_args($hooks, ['admin_init' => 0, 'init' => 0]);

        if (isset($modules['_'])) {
            $items = $modules['_'];
            unset($modules['_']);

            foreach ($items as $alias) {
                if (is_callable($alias)) {
                    $this->call($alias);
                } elseif (isset($this->resolved[$alias])) {
                    call_user_func($this->bindModule($alias));
                }
            }

        }

        // Module initialization.
        foreach ($modules as $hook => $groups) {
            $numArgs = (int)($hooks[$hook] ?? 1);

            foreach ($groups as $priority => $items) {
                $priority = (int)$priority;

                foreach ($items as $alias) {
                    if (is_callable($alias)) {
                        $callback = $alias;
                    } elseif (isset($this->resolved[$alias])) {
                        $callback = $this->bindModule($alias);
                    } else {
                        $callback = null;
                    }
                    if ($callback) {
                        add_action($hook, $callback, $priority, $numArgs);
                    }
                }
            }
        }
    }

    protected function bindModule(callable|string $alias): \Closure
    {
        return function () use ($alias) {
            if (is_callable($alias)) {
                call_user_func_array($alias, func_get_args());

                return;
            }

            $split = explode('@', $alias, 2);
            $count = count($split);

            try {
                if (1 === $count) {
                    if (is_callable($split[0])) {
                        call_user_func_array($split[0], func_get_args());
                    } else {
                        $this->instantiate($split[0]);
                    }
                } else {
                    // 2 === $count
                    $callback = [$this->instantiate($split[0]), $split[1]];
                    if (is_callable($callback)) {
                        call_user_func_array($callback, func_get_args());
                    }
                }
            } catch (ContinyException $e) {
                // Skip the module.
                error_log('ContinyException: ' . $e->getMessage());
            }
        };
    }

    /**
     * Instantiate a fully-qualified class
     *
     * @param string        $fqcnOrAlias     Our module name to look for.
     * @param callable|bool $constructorCall True:     instances are always created
     *                                       False:    instance is re-used if container has one. This is default.
     *                                       Callable: In-place drop-in. You can override the instance.
     *
     * @return mixed
     * @throws \Bojaghi\Continy\ContinyNotFoundException
     * @throws \Bojaghi\Continy\ContinyException
     */
    protected function instantiate(
        string        $fqcnOrAlias,
        callable|bool $constructorCall = false,
    ): mixed
    {
        $fqcn = $this->resolve($fqcnOrAlias);
        if (!$fqcn) {
            throw new ContinyNotFoundException("Module '$fqcnOrAlias' does not exist");
        }

        // Drop-in.
        if (is_callable($constructorCall)) {
            return call_user_func_array($constructorCall, [$this, $fqcn, $fqcnOrAlias]);
        }

        // Reuse.
        if (false === $constructorCall && isset($this->storage[$fqcn])) {
            return $this->storage[$fqcn];
        }

        $args = $this->arguments[$fqcn] ?? $this->arguments[$fqcnOrAlias] ?? null;
        if (is_null($args)) {
            $args = [];
        } elseif (is_callable($args)) {
            $args = (array)call_user_func($args, $this);
        } elseif (!is_array($args)) {
            $args = (array)$args;
        }

        $typeNames = $this->detectParams($fqcn, $args);
        foreach ($typeNames as $typeName) {
            $args[] = $this->get($typeName);
        }

        // As of PHP 8.0+, unpacking array with string keys are possible.
        $instance = new $fqcn(...$args);

        if (!$constructorCall) {
            $this->storage[$fqcn] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $fqcnOrAlias
     *
     * @return string|false FQCN of the module, return false if failed.
     */
    protected function resolve(string $fqcnOrAlias): string|false
    {
        if (!isset($this->resolved[$fqcnOrAlias])) {
            $fqcn = $this->getComponentFqcn($fqcnOrAlias);

            // Make sure that $this->$name is instantiated by now.
            $this->resolved[$fqcnOrAlias] = $fqcn;

            if ($fqcn !== $fqcnOrAlias) {
                $this->resolved[$fqcn] = $fqcn;
            }
        }

        return $this->resolved[$fqcnOrAlias];
    }

    /**
     * Get FQCN (Fully Qualified Class Name) from module name
     *
     * @param string $componentOrAlias
     *
     * @return string|false FQN of module, or false
     */
    protected function getComponentFqcn(string $componentOrAlias): string|false
    {
        if (class_exists($componentOrAlias)) {
            return $componentOrAlias;
        }

        return false;
    }

    /**
     * @template T
     * @param class-string<T> $id
     *
     * @return T|object|null
     * @throws \Bojaghi\Continy\ContinyException
     * @throws \Bojaghi\Continy\ContinyNotFoundException
     */
    public function get(string $id): mixed
    {
        if (empty($id)) {
            throw new ContinyException("'$id' is required");
        }

        if (!$this->has($id)) {
            throw new ContinyNotFoundException(sprintf("The container does not have '%s' item.", $id));
        }

        if (func_num_args() > 1) {
            $constructorCall = func_get_arg(1);
        } else {
            $constructorCall = false;
        }

        return $this->instantiate($id, $constructorCall);
    }

    public function has(string $id): bool
    {
        return class_exists($id) || isset($this->resolved[$id]);
    }

    public function __get(string $name)
    {
        try {
            return $this->get($name);
        } catch (ContinyException|ContinyNotFoundException $e) {
            return null;
        }
    }

    public function __set(string $name, $value)
    {
        $this->storage[$name] = $value;
    }

    public function __isset(string $name)
    {
        return $this->has($name);
    }

    /**
     * @param callable|array|string       $callable
     * @param array|string|callable|false $args
     *
     * @return mixed
     * @throws ContinyException
     */
    public function call(
        callable|array|string       $callable,
        array|string|callable|false $args = false,
    ): mixed
    {
        if (!is_callable($callable)) {
            throw new ContinyException('$callable is not callable');
        }

        if (false === $args) {
            // Find key for callable
            if (is_array($callable)) {
                $className = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
                $method    = $callable[1];
                $key       = "$className::$method";
            } elseif (is_string($callable)) {
                $key = $callable;
            } else {
                $key = null;
            }
            if (isset($this->arguments[$key])) {
                $args = $this->arguments[$key];
            } else {
                $typeNames = $this->detectParams($callable);
                $args      = array_map(fn($typeName) => $this->get($typeName), $typeNames);
            }
            if ($args && is_callable($args)) {
                $args = (array)call_user_func_array($args, [$this, $callable, $key]);
            }
        } elseif (is_callable($args)) {
            $args = (array)call_user_func($args, [$this, $callable]);
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * @param array|callable|string $target Class name or method name to detect parameters.
     * @param array                 $args   Initial arguments
     *
     * @return string[] array of FQCN
     * @throws ContinyException
     */
    protected function detectParams(callable|array|string $target, array $args = []): array
    {
        $output = [];

        try {
            if (is_string($target) && class_exists($target)) {
                $reflection  = new ReflectionClass($target);
                $constructor = $reflection->getConstructor();
                $parameters  = $constructor ? $constructor->getParameters() : [];
            } elseif (is_callable($target)) {
                if (is_array($target) && 2 === count($target)) {
                    $reflection = new ReflectionMethod($target[0], $target[1]);
                } else {
                    $reflection = new ReflectionFunction($target);
                }
                $parameters = $reflection->getParameters();
            } else {
                throw new ContinyException("'$target' is not callable");
            }

            // Continy allows incomplete arguments found in the configuration.
            // Code below let Continy guess missing arguments.
            $lenArgs   = count($args);
            $lenParams = count($parameters);
            if (0 < $lenArgs) {
                $parameters = array_slice($parameters, $lenArgs);
            }

            foreach ($parameters as $parameter) {
                $typeName   = $parameter->getType()->getName();
                $isNullable = $parameter->allowsNull();

                if ($parameter->getType()->isBuiltin()) {
                    if ($parameter->isOptional()) {
                        $output[] = $parameter->getDefaultValue();
                    } elseif ($isNullable) {
                        $output[] = null;
                    } else {
                        throw new ContinyException(
                            sprintf(
                                "Error while injecting parameter '%s' to '%s'." .
                                " Built-in type should have default value or can be nullish," .
                                " or invoke an explicit injection function.",
                                $parameter->getName(),
                                self::formatName($target),
                            ),
                        );
                    }
                    continue;
                }

                // Remove heading '?' for nullish parameters.
                if ($isNullable && str_starts_with($typeName, '?')) {
                    $typeName = substr($typeName, 1);
                }

                $output[] = $typeName;
            }
        } catch (\ReflectionException $e) {
            throw new ContinyException('ReflectionException: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $output;
    }

    public static function concatName(string $className, string $methodName): string
    {
        return $className . '::' . $methodName;
    }

    public static function formatName(mixed $maybeCallable): string
    {
        if (is_array($maybeCallable) && 2 === count($maybeCallable)) {
            $object = is_object($maybeCallable[0]) ? get_class($maybeCallable[0]) : $maybeCallable[0];
            $method = $maybeCallable[1];
            return "$object::$method";
        } elseif (is_string($maybeCallable)) {
            return $maybeCallable;
        } elseif ($maybeCallable instanceof \Closure) {
            return '{closure}';
        }

        return (string)$maybeCallable;
    }

    public function getMain(): string
    {
        return $this->mainFile;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
