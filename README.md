# CoralSQL

[![Build Status](https://travis-ci.org/rymizuki/coral-sql.svg?branch=master)](https://travis-ci.org/rymizuki/coral-sql)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/203554ef8b6f450fbdd0c970f8e4199f)](https://www.codacy.com/app/ry-mizuki/CoralSQL?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=rymizuki/CoralSQL&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/203554ef8b6f450fbdd0c970f8e4199f)](https://www.codacy.com/app/ry-mizuki/CoralSQL?utm_source=github.com&utm_medium=referral&utm_content=rymizuki/CoralSQL&utm_campaign=Badge_Coverage)

The PHP module for building SQL by  complex and related like a coral.

## about

```php
use CoralSQL\Builder;

$login = (new Builder)
    ->column(Builder::unescape('COUNT(*)'), 'amount')
    ->from('user_login', 'login')
    ->where('login.user_id', Builder::unescape('`user`.`id`'))
    ->where(
        (new Conditions())
            ->and('login.created_at', 'between', ['2019-06-01 00:00:00', '2019-06-30 23:59:59'])
    )
    ;

$builder = (new Builder)
    ->column('id')
    ->column('name')
    ->column('attr.address', 'address')
    ->form('user')
    ->leftJoin('user_attribute', 'attr', '`user`.`id` = `attr`.`user_id`')
    ->where('user.status', 1)
    ->where((new Conditions())->add($login, '>', 1))
    ->orderBy('id', 'desc')
    ;
```

```sql
SELECT
    `id`,
    `name`,
    `attr`.`address` AS `address`
FROM
    `user`
LEFT JOIN `user_attribute` AS `attr` ON `user`.`id` = `attr`.`user_id`
WHERE `user`.`status` = 1 AND (SELECT
    COUNT(*) AS `amount`
FROM
    `user_login` AS `login`
WHERE
    (`login`.`user_id` = `user`.`id`) AND (`login`.`created_at` BETWEEN "2019-06-01 00:00:00" AND "2019-06-30 23:59:59")
) > 1
ORDER BY
    `id` DESC
```

## installation

## usage
