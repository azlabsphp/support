<?php

namespace Drewlabs\Support\Collections;

class Operator
{
    /**
     * 
     * @var callable|\Closure|null
     */
    private $callback;

    public function __construct($callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * Creates an instance of the operator class
     * 
     * @param callable|\Closure|null $callback 
     * @return self 
     */
    public static function create($callback = null)
    {
        return new self($callback);
    }

    public function __invoke($data)
    {
        if ($accepts = (bool) ($data->accepts())) {
            return null === $this->callback ?
                $data :
                StreamInput::wrap(
                    is_string($this->callback) ?
                        call_user_func($this->callback, $data->value) : ($this->callback)($data->value),
                    $accepts
                );
        }

        return $data;
    }
}
