<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;


class CharacterStatRangeType extends CharacterStatType
{
    private $current;
    private $max;

    public function __construct(string $id, string $name, int $value = null)
    {
        parent::__construct($id, $name, (string)$value);
        $this->current = (int)$value;
        $this->max = $max;
    }

    public function getCurrentValue(): int
    {
        return $this->current;
    }

    public function getMaxValue(): int
    {
        return $this->max;
    }

    public function getType(): string
    {
        return "CharacterStatRange";
    }
}