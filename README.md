# Deferred Collection - PHP collections with deferred data processing

[![Build Status](https://travis-ci.org/kofan/deferred-collection.svg?branch=master)](https://travis-ci.org/kofan/deferred-collection)
[![Coverage Status](https://coveralls.io/repos/kofan/deferred-collection/badge.svg?branch=master&service=github)](https://coveralls.io/github/kofan/deferred-collection?branch=master)
[![StyleCI](https://styleci.io/repos/95515563/shield?style=flat&branch=master)](https://styleci.io/repos/95515563)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Introduction and motivation

The main purpose of this library is to defer execution of different operations on collection items until your application really needs it.
It turns out that very often the reason of application poor performance is an execution of unneeded operations on the collection of items.
Moreover, performing heavy operations on the whole collection at once is usually very inefficient and takes too much memory.
So, for example, let's take a look at the code

```php
<?php 

$query  = 'SELECT id, name, department FROM my_table';
$result = $mysqli->query($query, MYSQLI_USE_RESULT)
$models = [];

// ... Dummy code ...
while ($record = $result->fetch_assoc()) {
    if ($record['department'] === 'developers') {
        $models[] = new MyModel($record);
    }
}

// ...
if (somethingWrongHappened()) {
    throw new RuntimeException('Error happened!!!');
}
// ...

// ... Somewhere in the view ... //
foreach ($models as $model) {
    echo "<div>{$model->id} - {$model->name}</div>";

    if (isEnough()) {
        break;
    }
}
```

So there are several problems with this code there.

1. First of all, it tries to read all the models to the process memory at once. So if there are 1000 records found then it will try to read all 1000 into the process memory.
2. Second of all, it could be that this query is not needed at all if `somethingWrongHappened()` because in that case, an error is going to be thrown. So in this situation, the database query and its results are the waste of resources.
3. And, of course, during the data rendering it might happen that we won't need to iterate through all queries at all, even though we have fetched them from the database.

Thus, Deferred Collections are aiming to solve this problem.
All the operations are actually going to be performed only when the collection is started being iterated through.
For example:

```php
<?php

$collection = new DeferredCollection(function() {
    $query  = 'SELECT id, name, department FROM my_table';
    $result = $mysqli->query($query, MYSQLI_USE_RESULT)

    while ($record = $result->fetch_assoc()) {
        yield $record;
    }
});

// The collection is not going to be executed yet
$collection
    ->matchProperty('department', 'developers')
    ->instantiate(MyModel::class)

// ...
if (somethingWrongHappened()) {
    throw new RuntimeException('Error happened!!!');
}
// ...

// NOW the collection is going to be executed, but only 1 record per time will be processed
// we are not collecting all the records in a single array as it was done before,
// instead of that, Deferred Collection uses generators for all the data manipulations behind the scenes
foreach ($collection as $model) {
    echo "<div>{$model->id} - {$model->name}</div>";

    if (isEnough()) {
        break;
    }
}
```
