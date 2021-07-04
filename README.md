Explained Condition
=

- Example

```php
$conditionList = ConditionList::AND([
    'Likes vegetables' => true,
    'Likes at least one fruit' => ConditionList::OR([
        'Banana' => true,
        'Apple' => false
    ])
]);

// Condition result
$conditionList->isTrue();
$conditionList->isFalse();

// Pretty prints
$conditionList->getResolvedExplanations();
$conditionList->stringifyResolvedConditions();
$conditionList->stringifyResolvedAcceptations();
$conditionList->stringifyResolvedRejections();
```

- Output (with `stringifyResolvedConditions`)

```
Likes vegetables : ✅
AND Likes at least one fruit : ✅
_____Banana : ✅
_____OR Apple : ❌
```