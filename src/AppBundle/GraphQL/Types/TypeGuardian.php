<?php
/**
 * Created by PhpStorm.
 * User: sauterb
 * Date: 15/03/17
 * Time: 16:34
 */

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;


class TypeGuardian implements TypeInterface
{
    private $whitelistedFields = [];
    private $type;

    public function __construct(BaseType $type, array $whitelistedFields = [])
    {
        $this->type = $type;
        foreach ($whitelistedFields as $fieldName) {
            $this->whitelistedFields[strtolower($fieldName)] = true;
        }
    }

    public function __get(string $fieldName)
    {
        $fieldName = strtolower($fieldName);
        $methodName = "get" . $fieldName;

        if (!isset($this->whitelistedFields[$fieldName])) {
            return null;
        }

        return $this->type->$methodName();
    }

    public function __call(string $methodName, $arguments = null)
    {
        $methodName = strtolower($methodName);

        // only relay get-methods
        if (substr($methodName, 0, 3) !== "get") {
            return null;
        }

        if (!isset($this->whitelistedFields[substr($methodName, 3)])) {
            return null;
        }

        return $this->type->$methodName();
    }

    public function isTypeOf($type): bool
    {
        return $this->type instanceof $type ? true : false;
    }
}