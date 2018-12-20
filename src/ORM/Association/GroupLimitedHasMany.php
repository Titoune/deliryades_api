<?php

namespace App\ORM\Association;

use App\ORM\Association\Loader\GroupLimitedSelectLoader;
use Cake\ORM\Association\HasMany;

class GroupLimitedHasMany extends HasMany
{
    /**
     * @var integer
     */
    protected $limit;

    /**
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param integer $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    protected function _options(array $opts)
    {
        parent::_options($opts);

        $this->setLimit($opts['limit']);
    }

    public function eagerLoader(array $options)
    {
        $loader = new GroupLimitedSelectLoader([
            'alias' => $this->getAlias(),
            'sourceAlias' => $this->getSource()->getAlias(),
            'targetAlias' => $this->getTarget()->getAlias(),
            'foreignKey' => $this->getForeignKey(),
            'bindingKey' => $this->getBindingKey(),
            'strategy' => $this->getStrategy(),
            'associationType' => $this->type(),
            'sort' => $this->getSort(),
            'finder' => [$this, 'find'],
            'target' => $this->getTarget(),
            'limit' => $this->getLimit()
        ]);

        return $loader->buildEagerLoader($options);
    }
}
