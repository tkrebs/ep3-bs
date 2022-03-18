<?php

namespace Base\Manager;

use Base\Entity\AbstractEntity;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractLocaleEntityManager extends AbstractEntityManager
{

    protected $locale;

    /**
     * Creates a new manager object.
     *
     * @param TableGateway $entityTable
     * @param TableGateway $entityMetaTable
     * @param string $locale
     */
    public function __construct(TableGateway $entityTable, TableGateway $entityMetaTable, $locale)
    {
        parent::__construct($entityTable, $entityMetaTable);

        $this->locale = $locale;
    }

    /**
     * Gets the entity meta select predicate.
     *
     * @param string $id
     * @param array $eids
     * @return Select
     */
    protected function getByMetaSelect($id, array $eids)
    {
        $where = new Where();
        $where->in($id, $eids);
        $where->and;

        $nestedWhere = $where->nest();
        $nestedWhere->equalTo('locale', $this->locale);
        $nestedWhere->or;
        $nestedWhere->isNull('locale');
        $nestedWhere->unnest();

        $metaSelect = $this->entityMetaTable->getSql()->select();
        $metaSelect->where($where);
        $metaSelect->order('locale ASC');

        return $metaSelect;
    }

    /**
     * Inserts an entity meta record.
     *
     * @param string $id
     * @param int $eid
     * @param string $key
     * @param mixed $value
     * @param AbstractEntity $entity
     */
    protected function saveMetaByInsert($id, $eid, $key, $value, AbstractEntity $entity)
    {
        $this->entityMetaTable->insert(array(
            $id => $eid,
            'key' => $key,
            'value' => $value,
            'locale' => $entity->getMetaLocale($key),
        ));
    }

    /**
     * Updates an entity meta record.
     *
     * @param string $id
     * @param int $eid
     * @param string $key
     * @param mixed $value
     * @param AbstractEntity $entity
     */
    protected function saveMetaByUpdate($id, $eid, $key, $value, AbstractEntity $entity)
    {
        $locale = $entity->getMetaLocale($key);

        if ($locale) {
            $where = array($id => $eid, 'key' => $key, 'locale' => $locale);
        } else {
            $where = array($id => $eid, 'key' => $key, new IsNull('locale'));
        }

        $this->entityMetaTable->update(array(
            'value' => $value,
        ), $where);
    }

    /**
     * Deletes an entity meta record.
     *
     * @param string $id
     * @param int $eid
     * @param string $key
     * @param AbstractEntity $entity
     */
    protected function deleteMeta($id, $eid, $key, AbstractEntity $entity)
    {
        $locale = $entity->getMetaLocale($key);

        if ($locale) {
            $where = array($id => $eid, 'key' => $key, 'locale' => $locale);
        } else {
            $where = array($id => $eid, 'key' => $key, new IsNull('locale'));
        }

        $this->entityMetaTable->delete($where);
    }

}