<?php

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    protected $errors = [];
    protected $aliases = [];

    public function setAliases(array $aliases)
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function setAlias($field, $alias)
    {
        $this->aliases[$field] = $alias;
    }

    public function validate($request, array $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $alias = ! empty($this->aliases[$field]) ? $this->aliases[$field] : ucfirst($field);
                $rule->setName($alias)->assert($request->getParam($field));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }

        return $this;
    }

    public function failed()
    {
        return ! empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
