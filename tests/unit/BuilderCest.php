<?php namespace tests;
use tests\UnitTester;
use PDO;
use CoralSQL\Builder;
use CoralSQL\Builder\Table;
use CoralSQL\Builder\Conditions;
use CoralSQL\Builder\Order;

class BuilderCest
{
    public function specific_table_name_to_form(UnitTester $I)
    {
        $sql = (new Builder())->from('user')->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_table_name_with_alias_to_from(UnitTester $I)
    {
        $sql = (new Builder())->from('user', 'user1')->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user` AS `user1`
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_table_instance_to_from(UnitTester $I)
    {
        $table = new Table('user', 'user1');
        $sql = (new Builder())->from($table)->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user` AS `user1`
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_table_instance_with_alias_to_from(UnitTester $I)
    {
        $table = new Table('user', 'user1');
        $sql = (new Builder())->from($table, 'user2')->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user` AS `user2`
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_column(UnitTester $I)
    {
        $sql = (new Builder())
            ->from('user')
            ->column('id')
            ->column('name', 'user_name')
            ->column(Builder::unescape('COUNT(*)'), 'count')
            ->toSQL();
        $expectation = <<<SQL
SELECT
    `id`,
    `name` AS `user_name`,
    COUNT(*) AS `count`
FROM
    `user`
SQL;
        $I->assertEquals($sql, $expectation);

    }

    public function specific_column_with_alias(UnitTester $I)
    {
        $sql = (new Builder())
            ->from('user')
            ->column('user.id', 'id')
            ->toSQL();
        $expectation = <<<SQL
SELECT
    `user`.`id` AS `id`
FROM
    `user`
SQL;
        $I->assertEquals($sql, $expectation);

    }
    public function specific_order_by_asc(UnitTester $I)
    {
        $sql = (new Builder())
            ->from('user')
            ->orderBy('id', Order::ASC)
            ->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
ORDER BY
    `id` ASC
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_order_by_desc(UnitTester $I)
    {
        $sql = (new Builder())
            ->from('user')
            ->orderBy('id', Order::DESC)
            ->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
ORDER BY
    `id` DESC
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specific_multiple_order(UnitTester $I)
    {
        $sql = (new Builder())
            ->from('user')
            ->orderBy('id', Order::DESC)
            ->orderBy('name', Order::ASC)
            ->toSQL();
        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
ORDER BY
    `id` DESC,
    `name` ASC
SQL;
        $I->assertEquals($sql, $expectation);
    }

    public function specify_condition_that_matching_numerical_value(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where('id', 1)
            ;

        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    (`id` = ?)
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => 1,
                'dataType' => PDO::PARAM_INT
            ]
        ]);
    }

    public function specify_condition_matching_in_numerical_value(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where('id', [1, 2, 3])
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    (`id` IN (?,?,?))
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => 1,
                'dataType' => PDO::PARAM_INT
            ],
            [
                'value' => 2,
                'dataType' => PDO::PARAM_INT
            ],
            [
                'value' => 3,
                'dataType' => PDO::PARAM_INT
            ]
        ]);
    }

    public function specify_conditions_by_instance(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where(
                (new Conditions())
                    ->and('id', 1)
            )
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    ((`id` = ?))
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => 1,
                'dataType' => PDO::PARAM_INT
            ]
        ]);
    }

    public function specify_condition_that_matching_or_condition(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where(
                (new Conditions())
                    ->and('id', 1)
                    ->or('name', 'test')
            )
            ->where('status', 2)
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    ((`id` = ?) OR (`name` = ?)) AND (`status` = ?)
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => 1,
                'dataType' => PDO::PARAM_INT
            ],
            [
                'value' => 'test',
                'dataType' => PDO::PARAM_STR
            ],
            [
                'value' => 2,
                'dataType' => PDO::PARAM_INT
            ],
        ]);
    }

    public function specify_condition_that_matching_like_condition(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where(
                (new Conditions())
                    ->and('name', 'like', '%テスト%')
            )
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    ((`name` LIKE ?))
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => '%テスト%',
                'dataType' => PDO::PARAM_STR
            ],
        ]);
    }

    public function specify_condition_that_not_equal_condition(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->where(
                (new Conditions())
                    ->and('id', '!=', 1)
            )
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    ((`id` != ?))
SQL;

        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [
                'value' => 1,
                'dataType' => PDO::PARAM_INT
            ],
        ]);
    }

    public function specify_table_to_left_join(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->leftJoin('user_attribute', 'user.id = user_attribute.user_id')
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
LEFT JOIN `user_attribute` ON user.id = user_attribute.user_id
SQL;
        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, []);
    }

    public function specify_table_with_alias_to_left_join(UnitTester $I)
    {
        $builder = (new Builder());
        $builder
            ->from('user')
            ->leftJoin('user_attribute', 'attr', 'user.id = attr.user_id')
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
LEFT JOIN `user_attribute` AS `attr` ON user.id = attr.user_id
SQL;
        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, []);
    }

    public function specify_table_instance_to_left_join(UnitTester $I)
    {
        $login = new Table('user_login', 'login');

        $builder = (new Builder());
        $builder
            ->from('user')
            ->leftJoin('user_attribute', 'attr', 'user.id = attr.user_id')
            ->leftJoin($login, 'user.id = login.user_id')
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
LEFT JOIN `user_attribute` AS `attr` ON user.id = attr.user_id
LEFT JOIN `user_login` AS `login` ON user.id = login.user_id
SQL;
        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, []);
    }

    public function specify_table_instance_with_alias_to_left_join(UnitTester $I)
    {
        $login = new Table('user_login', 'login');

        $builder = (new Builder());
        $builder
            ->from('user')
            ->leftJoin('user_attribute', 'attr', 'user.id = attr.user_id')
            ->leftJoin($login, 'll', 'user.id = ll.user_id')
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
LEFT JOIN `user_attribute` AS `attr` ON user.id = attr.user_id
LEFT JOIN `user_login` AS `ll` ON user.id = ll.user_id
SQL;
        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, []);
    }

    public function specify_sub_query_in_condition(UnitTester $I)
    {
        $login = (new Builder())
            ->column(Builder::unescape('COUNT(*)'), 'amount')
            ->from('user_login', 'login')
            ->where('created_at', '>', '2019-07-06 00:00:00')
            ;

        $builder = (new Builder());
        $builder
            ->from('user')
            ->where($login, '>', 10)
            ;
        $sql = $builder->toSQL();
        $params = $builder->getBindParams();

        $expectation = <<<SQL
SELECT
    *
FROM
    `user`
WHERE
    ((SELECT     COUNT(*) AS `amount` FROM     `user_login` AS `login` WHERE     (`created_at` > ?)) > ?)
SQL;
        $I->assertEquals($sql, $expectation);
        $I->assertEquals($params, [
            [ 'value' => '2019-07-06 00:00:00', 'dataType' => PDO::PARAM_STR] ,
            [ 'value' => 10, 'dataType' => PDO::PARAM_INT] ,
        ]);
    }

    public function specific_having_column_value(UnitTester $I)
    {
        $builder = (new Builder)
            ->from('user')
            ->having('age', 10)
            ;
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
HAVING
    (`age` = ?)
SQL;
        expect($I, $builder, $sql, [
            ['value' => 10, 'dataType' => PDO::PARAM_INT],
        ]);
    }

    public function specific_having_column_value_list(UnitTester $I)
    {
        $builder = (new Builder)
            ->from('user')
            ->having('age', [10, 15, 20])
            ;
        $sql  = <<<SQL
SELECT
    *
FROM
    `user`
HAVING
    (`age` IN (?,?,?))
SQL;
        expect($I, $builder, $sql, [
            ['value' => 10, 'dataType' => PDO::PARAM_INT],
            ['value' => 15, 'dataType' => PDO::PARAM_INT],
            ['value' => 20, 'dataType' => PDO::PARAM_INT],
        ]);
    }

    public function specific_having_column_operator_value(UnitTester $I)
    {
        $builder = (new Builder)
            ->from('user')
            ->having('age', '>', 10)
            ;
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
HAVING
    (`age` > ?)
SQL;
        expect($I, $builder, $sql, [
            ['value' => 10, 'dataType' => PDO::PARAM_INT],
        ]);
    }

    public function specific_having_conditions(UnitTester $I)
    {
        $conditions = (new Conditions())
            ->and('age', '<', 10)
            ->or('age', '>=', 50)
            ;
        $builder = (new Builder)
            ->from('user')
            ->having($conditions)
            ;

        $sql = <<<SQL
SELECT
    *
FROM
    `user`
HAVING
    ((`age` < ?) OR (`age` >= ?))
SQL;

        expect($I, $builder, $sql, [
            ['value' => 10, 'dataType' => PDO::PARAM_INT],
            ['value' => 50, 'dataType' => PDO::PARAM_INT],
        ]);
    }

    public function specific_group_by_column(UnitTester $I)
    {
        $builder = (new Builder)->from('user')->groupBy('age');
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
GROUP BY
    `age`
SQL;
        expect($I, $builder, $sql, []);
    }

    public function specific_group_by_columns(UnitTester $I)
    {
        $builder = (new Builder)->from('user')->groupBy(['age', 'gender']);
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
GROUP BY
    `age`, `gender`
SQL;
        expect($I, $builder, $sql, []);
    }

    public function specific_limit(UnitTester $I)
    {
        $builder = (new Builder)->from('user')->limit(10);
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
LIMIT 10
SQL;
        expect($I, $builder, $sql, []);
    }

    public function specific_offset(UnitTester $I)
    {
        $builder = (new Builder)->from('user')->offset(10);
        $sql = <<<SQL
SELECT
    *
FROM
    `user`
OFFSET 10
SQL;
        expect($I, $builder, $sql, []);
    }
}


function expect(UnitTester $I, Builder $builder, string $expect_sql, array $expect_params)
{
    $sql = $builder->toSQL();
    $params = $builder->getBindParams();

    $I->assertEquals($sql, $expect_sql);
    $I->assertEquals($params, $expect_params);
}
