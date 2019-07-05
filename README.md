# CoralSQL

## about

```php
$builder = CoralSQL::newBuilder()
    ->column('name')
    ->from('user')
    ->leftJoin('user_condition', 'cond', 'user.id = cond.user_id')
    ->where(
        CoralSQL::newCondition()
            ->set('cond.active', 1)
            ->and($subquery)
    )
```

```sql
SELECT
FROM

```

## installation

## usage