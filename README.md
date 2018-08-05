[![Build Status](https://travis-ci.org/LucaRainone/conditionbuilder.svg?branch=master)](https://travis-ci.org/LucaRainone/conditionbuilder)


This package allows you to build query conditions for PDO-like interfaces.

This is not an ORM or abstraction layers: it's a condition builder.

## Usage

```
<?php

require_once "vendor/autoload.php";

use rain1\ConditionBuilder\ConditionBuilder;
use rain1\ConditionBuilder\Operator\IsEqual;
use rain1\ConditionBuilder\Operator\IsLess;

$condition = new ConditionBuilder(\rain1\ConditionBuilder\ConditionBuilder::MODE_AND);

$condition->append(
	new IsEqual("id", 3),
	new IsLess("priority", 100),
	new IsEqual("myCol", [1,2,3]), // is internally converted in IN operator
	new IsEqual("anotherCol", null), // it will be ignored cause null value
	(new ConditionBuilder(ConditionBuilder::MODE_OR))
		->append(
			new IsEqual("col1", 3),
			new IsEqual("someflag", 0)
		)
);

$query =  "SELECT * FROM myTable WHERE $condition";

echo $query."\n";
print_r($condition()); // shortcut of $condition->values();

//$res = $pdo->prepare($query)->execute($condition->values());

```

This will produce the following outputs:

```

SELECT * FROM myTable WHERE (id = ? AND priority < ? AND myCol IN (?,?,?) AND (col1 = ? OR someflag = ?))
Array
(
    [0] => 3
    [1] => 100
    [2] => 1
    [3] => 2
    [4] => 3
    [5] => 3
    [6] => 0
)

```

In demo/index.php there is a real use case.