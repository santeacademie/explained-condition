<?php

namespace Santeacademie\ExplainedCondition;

class ConditionType
{

    const TYPE_AND = 'AND';
    const TYPE_OR = 'OR';

    private function __construct(private string $label)
    {

    }

    public static function AND(): ConditionType
    {
        return new self(self::TYPE_AND);
    }

    public static function OR(): ConditionType
    {
        return new self(self::TYPE_OR);
    }

    public function getLabel()
    {
        return $this->label;
    }

}