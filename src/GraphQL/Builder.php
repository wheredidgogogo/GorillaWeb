<?php

namespace Gorilla\GraphQL;

class Builder
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @return string
     */
    public function __toString()
    {
        $fields = implode(',', $this->fields);

        return <<<EOF
            {$this->name} {
                {$fields}
            }
EOF;
    }
}