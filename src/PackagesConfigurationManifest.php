<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support;

use Drewlabs\Support\Exceptions\UnsupportedConfigurationFileException;
use Drewlabs\Support\Types\ConfigureMethod;

class PackagesConfigurationManifest
{
    /**
     * A cache that hold already loaded PHP file so that the file is only loaded once per execution.
     *
     * It holds in method previously required PHP configuration files so that they are not required more that once
     *
     * @var array
     */
    private static $REQUIRE_FILES = [];
    /**
     * A cache that hold already loaded JSON file so that the file is only loaded once per execution.
     *
     * It holds in method previously required .json configuration files so that they are not required more that once
     *
     * @var array
     */
    private static $REQUIRE_JSON_FILES = [];

    /**
     * Configure provided packages configuration classes.
     *
     * @throws \RuntimeException
     * @throws UnsupportedConfigurationFileException
     * @throws \ReflectionException
     *
     * @return void
     */
    public static function load(array $manifest)
    {
        foreach ($manifest as $key => $value) {
            // code...
            // $key is the configuration class name
            // $value is either string or or array
            if (\is_array($value)) {
                $total_count = \count($value);
                $method = $total_count > 1 ? $value[0] : ConfigureMethod::VALUE;
                $params = $total_count > 1 ? $value[1] : $value[0];
            } else {
                $method = ConfigureMethod::VALUE;
                $params = $value;
            }
            // If the params is array transform the first item of the array
            if (\is_array($params)) {
                $params[0] = static::readConfiguration($params[0]);
            } else {
                $params = [static::readConfiguration($params)];
            }
            static::callConfigManagerConfigurationMethod($key, $method, $params);
        }
    }

    private static function readConfiguration(string $path)
    {
        $basename = basename($path);
        if (drewlabs_core_strings_ends_with($basename, '.json')) {
            return static::readJSONConfiguration($path);
        }
        if (drewlabs_core_strings_ends_with($basename, '.php')) {
            return static::readPHPConfiguration($path);
        }
        if (drewlabs_core_strings_ends_with($basename, '.yml')) {
            return static::readYAMLConfiguration($path);
        }
        // Throw new Exception
        throw new UnsupportedConfigurationFileException(['json', 'php', 'yaml']);
    }

    /**
     * Try load configuration from a php file.
     *
     * @return array
     */
    private static function readPHPConfiguration(string $path)
    {
        $key = drewlabs_core_strings_before('.', basename($path));
        if (\array_key_exists($key, static::$REQUIRE_FILES)) {
            return static::$REQUIRE_FILES[$key];
        }
        if (file_exists($path)) {
            $buffer = require $path;
            $buffer = (array) $buffer;
            static::$REQUIRE_FILES[$key] = $buffer;

            return $buffer;
        }

        return [];
    }

    /**
     * Try load configuration from a yaml file.
     *
     * @return array
     */
    private static function readYAMLConfiguration(string $path)
    {
        if (!\function_exists('yaml_parse')) {
            throw new \RuntimeException('YAML PECL extension is required to parse .yml files');
        }
        if (file_exists($path)) {
            $buffer = \call_user_func('yaml_parse_file', $path);

            return (array) $buffer;
        }

        return [];
    }

    /**
     * Try load configuration from a json file.
     *
     * @return array
     */
    private static function readJSONConfiguration(string $path)
    {
        // Because reading from file is a blocking task, reading from
        // the json configuration file will only being perform once
        $key = drewlabs_core_strings_before('.', basename($path));
        if (\array_key_exists($key, static::$REQUIRE_JSON_FILES)) {
            return static::$REQUIRE_JSON_FILES[$key];
        }
        if (file_exists($path)) {
            $buffer = json_decode(file_get_contents($path), true);
            static::$REQUIRE_JSON_FILES[$key] = $buffer;

            return $buffer;
        }

        return [];
    }

    private static function callConfigManagerConfigurationMethod(string $clazz, string $method, array $params)
    {
        $reflectionMethod = new \ReflectionMethod($clazz, $method);
        $required_parameters = array_filter($reflectionMethod->getParameters(), static function ($p) {
            return !$p->isOptional();
        });
        if (\count($params) < \count($required_parameters)) {
            throw new \RuntimeException(sprintf('Configuration methods signature incorrectly defined for %s', $clazz));
        }
        if ($reflectionMethod->isStatic() && $reflectionMethod->isPublic()) {
            return $reflectionMethod->invoke(null, ...$params);
        }
        throw new \RuntimeException('The configuration method must be a static method');
    }
}
