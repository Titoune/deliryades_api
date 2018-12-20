<?php

namespace App\ORM\Association\Loader;

use Cake\Database\Expression\IdentifierExpression;
use Cake\ORM\Association\Loader\SelectLoader;

class GroupLimitedSelectLoader extends SelectLoader
{
    /**
     * @var integer
     */
    protected $limit;

    /**
     * @var \Cake\ORM\Table
     */
    protected $target;

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->limit = $options['limit'];
        $this->target = $options['target'];
    }

    protected function _defaultOptions()
    {
        return parent::_defaultOptions() + [
                'limit' => $this->limit
            ];
    }

    protected function _buildQuery($options)
    {
        if (!isset($options['fields'])) {
            $options['fields'] = [];
        }

        $key = $this->_linkField($options);

        $rankedSubQuery =
            $this->target->find();

        $rowNumberField = $rankedSubQuery
            ->newExpr()
            ->add('@row_number')
            ->setConjunction(':=')
            ->add(
                $rankedSubQuery->func()->IF([
                    $rankedSubQuery->newExpr()->eq(
                        '@foreign_key',
                        new IdentifierExpression($options['foreignKey'])
                    ),
                    '@row_number + 1' => 'literal',
                    1 => 'literal'
                ])
            );

        $foreignKeyField = $rankedSubQuery
            ->newExpr()
            ->add('@foreign_key')
            ->setConjunction(':=')
            ->add(new IdentifierExpression($options['foreignKey']));

        $columns = $this->target->getSchema()->columns();
        $rankedSubQuery = $this->target
            ->find()
            ->select([
                '__row_number' => $rowNumberField,
                '__foreign_key' => $foreignKeyField
            ])
            ->select(array_combine($columns, $columns))
            ->from([
                $this->targetAlias => $this->target->getTable(),
                '__init_variables' => '(SELECT @row_number := 0, @foreign_key := 0)'
            ])
            ->where($options['conditions'])
            ->order([
                $key => 'ASC'
            ]);

        $rankedSubQuery = $this->_addFilteringCondition($rankedSubQuery, $key, $options['keys']);

        if (!empty($options['sort'])) {
            $rankedSubQuery->order($options['sort']);
        }

        $finder = $this->finder;
        $fetchQuery = $finder()
            ->select($options['fields'])
            ->from([$this->targetAlias => $rankedSubQuery])
            ->where([
                $this->targetAlias . '.__row_number <=' => $options['limit']
            ], [
                $this->targetAlias . '.__row_number' => 'integer',
            ])
            ->eagerLoaded(true)
            ->enableHydration($options['query']->isHydrationEnabled());

        if (!empty($options['contain'])) {
            $fetchQuery->contain($options['contain']);
        }

        if (!empty($options['queryBuilder'])) {
            $fetchQuery = $options['queryBuilder']($fetchQuery);
        }

        $this->_assertFieldsPresent($fetchQuery, (array)$key);

        return $fetchQuery;
    }
}
