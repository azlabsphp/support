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

namespace Drewlabs\Support\Traits;

use ArrayIterator;
use Drewlabs\Contracts\Support\FuncArgument;
use Drewlabs\Contracts\Support\OverloadedPartialMethodHandler;
use Drewlabs\Support\Exceptions\OverloadMethodCallExpection;
use Drewlabs\Support\Exceptions\TooManyMatchingMethodOverload;
use Drewlabs\Support\MethodOverload\OverloadedMethodHandler;
use Drewlabs\Support\Types\AbstractTypes;
use Iterator;

trait Overloadable
{
    /**
     * Provide a method overload implementations to PHP classes.
     *
     * @param array $args
     * @param array $signatures
     *
     * @return mixed
     */
    public function overload($args, $signatures)
    {
        $fallbacks = [];
        $handlers = drewlabs_core_iter_filter(
            drewlabs_core_iter_map(
                new ArrayIterator($signatures ?? []),
                function ($value, $key) {
                    return new OverloadedMethodHandler($value, $key, $this);
                }
            ),
            static function (OverloadedMethodHandler $candidate) use ($args, $fallbacks) {
                $matches = $candidate->matches($args ?? []);
                if ($candidate->isFallback()) {
                    $fallbacks[] = $candidate;
                }
                return $matches;
            },
            false
        );
        $total_handlers = \iterator_count($handlers);
        if (
            $total_handlers === 1 &&
            (count($fallbacks) === 1) &&
            (null !== $method = $fallbacks[0])
        ) {
            return $method->call($args);
        } else if ($total_handlers === 1) {
            if ($method = $this->getMethod($handlers)) {
                return $method->call($args);
            }
        } else {
            // Look for the method having a more specific argument type definition
            //
            $handler = drewlabs_core_iter_reduce(
                $handlers,
                static function (?OverloadedPartialMethodHandler $carry, OverloadedPartialMethodHandler $curr) {
                    if (null === $carry) {
                        return $curr;
                    }
                    $arguments = $curr->getArguments();
                    $carry_arguments = $carry->getArguments();
                    /**
                     * @var FuncArgument
                     */
                    foreach (drewlabs_core_array_zip($arguments, $carry_arguments) as $value) {
                        // TODO : If the string formatting of the current method argument is "*:", then use the carry method 
                        if (drewlabs_core_strings_contains($value[0] ?? '', sprintf("%s:", AbstractTypes::ANY))) {
                            $carry = $carry;
                            break;
                        }
                        // TODO : If the string formatting of the carry method argument is "*:", then use the current method
                        if (drewlabs_core_strings_contains($value[1] ?? '', sprintf("%s:", AbstractTypes::ANY))) {
                            $carry = $curr;
                            break;
                        }
                    }
                    return $carry;
                },
                null
            );
            if ($handler) {
                return $handler->call($args);
            }
            throw new TooManyMatchingMethodOverload(sprintf('%d method provide the same method definition', $total_handlers));
        }
        throw new OverloadMethodCallExpection('None suitable overloaded method found.');
    }

    /**
     * 
     * @param Iterator $values 
     * @return OverloadedMethodHandler 
     */
    private function getMethod(\Iterator $values)
    {
        $values->rewind();
        return $values->current();
    }
}
