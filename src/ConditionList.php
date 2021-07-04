<?php

namespace Santeacademie\ExplainedCondition;

class ConditionList
{

    const DEFAULT_SEPARATOR = PHP_EOL;

    /** @var ConditionType  */
    protected $defaultType;
    protected $conditionMap;

    public function __construct(array|bool $conditionMap, ?ConditionType $defaultType = null)
    {
        $this->defaultType = $defaultType ?? ConditionType::AND();
        $this->conditionMap = is_array($conditionMap) ? $conditionMap : [$conditionMap];
    }

    public static function OR(array|bool $conditionMap)
    {
        return new self($conditionMap, ConditionType::OR());
    }

    public static function AND(array|bool $conditionMap)
    {
        return new self($conditionMap);
    }

    public function isTrue(?ConditionType $type = null): bool
    {
        $type = $type ?? $this->defaultType;

        if (!is_object($type)) {
            throw new \LogicException(sprintf('Unknown type \'%s\'.', $type));
        }

        return array_reduce($this->conditionMap, function($carry, $condition) use($type) {
            if ($condition instanceof ConditionList) {
                $condition = $condition->isTrue();
            }

            switch($type->getLabel()) {
                case ConditionType::TYPE_AND : return $carry && $condition;
                case ConditionType::TYPE_OR : return $carry || $condition;
                default: throw new \LogicException(sprintf('Unknown condition type \'%s\'.', get_class($type)));
            }
        }, $type->getLabel() === ConditionType::TYPE_AND ? true : false);
    }

    public function isFalse(?ConditionType $type = null): bool
    {
        return !$this->isTrue($type);
    }

    private static function getConditionIcon(bool $condition): string
    {
        return $condition ? '✅' : '❌';
    }

    public function getResolvedExplanations(?bool $onlyMatching = null, bool $stringified = false, int $depth = 0): array
    {
        $explanations = [];
        $counter = 0;

        foreach($this->conditionMap as $explanation => $conditionItem) {
            $isFirst = $counter === 0;
            $typePrefix = $isFirst ? '' : $this->defaultType->getLabel().' ';
            $depthPrefix = $stringified ? str_repeat('_____', $depth) : '';

            $conditionValue = $conditionItem instanceof ConditionList ? $conditionItem->isTrue() : $conditionItem;

            if (!is_null($onlyMatching) && (($onlyMatching && !$conditionValue) || (!$onlyMatching && $conditionValue))) {
                continue;
            }

            $explanations[] = sprintf('%s%s%s : %s',
                $depthPrefix,
                $typePrefix,
                $explanation,
                self::getConditionIcon($conditionValue)
            );

            if ($conditionItem instanceof ConditionList) {
                $explanations = array_merge(
                    $explanations,
                    $conditionItem->getResolvedExplanations($onlyMatching, $stringified,$depth + 1)
                );
            }

            $counter++;
        }

        return $explanations;
    }

    public function stringifyResolvedConditions(string $separator = self::DEFAULT_SEPARATOR): string
    {
        return implode($separator, $this->getResolvedExplanations(null, true));
    }

    public function stringifyResolvedRejections(string $separator = self::DEFAULT_SEPARATOR): string
    {
        return implode($separator, $this->getResolvedExplanations(false, true));
    }

    public function stringifyResolvedAcceptations(string $separator = self::DEFAULT_SEPARATOR): string
    {
        return implode($separator, $this->getResolvedExplanations(true, true));
    }

}