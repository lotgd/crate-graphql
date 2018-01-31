<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;


class CharacterStatIntType extends CharacterStatType
{
    private $current;
    private $max;

    public function __construct(string $id, string $name, int $value = null)
    {
        parent::__construct($id, $name, (string)$value);
        $this->current = (int)$value;
    }

    public function getValue(): int
    {
        return $this->current;
    }

    public function getType(): string
    {
        return "CharacterStatInt";
    }
}