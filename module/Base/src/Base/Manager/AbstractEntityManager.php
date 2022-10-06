<?php

namespace Base\Manager;

use Base\Entity\AbstractEntity;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractEntityManager extends AbstractManager
{

    protected $entityTable;
    protected $entityMetaTable;

    /**
     * Creates a new manager object.
     *
     * @param TableGateway $entityTable
     * @param TableGateway $entityMetaTable
     */
    public function __construct(TableGateway $entityTable, TableGateway $entityMetaTable)
    {
        $this->entityTable = $entityTable;
        $this->entityMetaTable = $entityMetaTable;
    }

    /**
     * Saves (updates or creates) an entity.
     *
     * @param AbstractEntity $entity
     * @throws Exception
     * @return AbstractEntity
     */
    public function save(AbstractEntity $entity)
    {
        $connection = $this->entityTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            $id = $entity->getPrimary();

            if ($entity->get($id)) {

                /* Update existing entity */

                /* Determine updated properties */

                $updates = array();

                foreach ($entity->need('updatedProperties') as $property) {
                    $updates[$property] = $entity->get($property);
                }

                if ($updates) {
                    $this->entityTable->update($updates, array($id => $entity->get($id)));
                }

                /* Determine new meta properties */

                foreach ($entity->need('insertedMetaProperties') as $metaProperty) {
                    $this->saveMetaByInsert($id, $entity->get($id), $metaProperty, $entity->needMeta($metaProperty), $entity);
                }

                /* Determine updated meta properties */

                foreach ($entity->need('updatedMetaProperties') as $metaProperty) {
                    $this->saveMetaByUpdate($id, $entity->get($id), $metaProperty, $entity->needMeta($metaProperty), $entity);
                }

                /* Determine removed meta properties */

                foreach ($entity->need('removedMetaProperties') as $metaProperty) {
                    $this->deleteMeta($id, $entity->get($id), $metaProperty, $entity);
                }

                $entity->reset();

                $this->getEventManager()->trigger('save.update', $entity);

            } else {

                /* Insert entity */

                $insertValues = $this->getInsertValues($entity);

                if ($entity->getExtra('n' . $id)) {
                    $insertValues[$id] = $entity->getExtra('n' . $id);
                }

                $this->entityTable->insert($insertValues);

                $eid = $this->entityTable->getLastInsertValue();

                if (! (is_numeric($eid) && $eid > 0)) {
                    throw new RuntimeException('Failed to save entity');
                }

                foreach ($entity->need('meta') as $key => $value) {
                    $this->saveMetaByInsert($id, $eid, $key, $value, $entity);

                    if (! $this->entityMetaTable->getLastInsertValue()) {
                        throw new RuntimeException( sprintf('Failed to save entity meta key "%s"', $key) );
                    }
                }

                $entity->add($id, $eid);

                $this->getEventManager()->trigger('save.insert', $entity);
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('save', $entity);

            return $entity;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
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
        $this->entityMetaTable->update(array(
            'value' => $value,
        ), array($id => $eid, 'key' => $key));
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
        $this->entityMetaTable->delete(array($id => $eid, 'key' => $key));
    }

    /**
     * Gets the insert values for a new entity.
     *
     * @param AbstractEntity $entity
     * @return array
     */
    abstract protected function getInsertValues(AbstractEntity $entity);

    /**
     * Gets all entities that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        $select = $this->entityTable->getSql()->select();

        if ($where) {
            $select->where($where);
        }

        if ($order) {
            $select->order($order);
        }

        if ($limit) {
            $select->limit($limit);

            if ($offset) {
                $select->offset($offset);
            }
        }

        $resultSet = $this->entityTable->selectWith($select);

        $entities = $this->getByResultSet($resultSet);

        if (! ($entities && $loadMeta)) {
            return $entities;
        }

        /* Load entity meta data */

        $entity = current($entities);

        $id = $entity->getPrimary();

        $eids = array();

        foreach ($entities as $entity) {
            $eids[] = $entity->need($id);
        }

        reset($entities);

        $metaSelect = $this->getByMetaSelect($id, $eids);

        $metaResultSet = $this->entityMetaTable->selectWith($metaSelect);

        return $this->getByMetaResultSet($metaResultSet, $entities);
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
        $metaSelect = $this->entityMetaTable->getSql()->select();
        $metaSelect->where(new In($id, $eids));

        return $metaSelect;
    }

    /**
     * Gets all entities from a result set.
     *
     * @param Traversable $resultSet
     * @return array
     */
    abstract protected function getByResultSet(Traversable $resultSet);

    /**
     * Gets all entity's meta data from a meta result set.
     *
     * @param Traversable $metaResultSet
     * @param array $entities
     * @return array
     */
    abstract protected function getByMetaResultSet(Traversable $metaResultSet, array $entities);

    /**
     * Gets all entities.
     *
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getAll($order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        return $this->getBy(null, $order, $limit, $offset, $loadMeta);
    }

    /**
     * Deletes an entity and all respective meta properties (through database foreign keys).
     *
     * @param AbstractEntity|int $entity
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($entity)
    {
        if ($entity instanceof AbstractEntity) {
            $eid = $entity->need( $entity->getPrimary() );
        } else {
            $eid = $entity;
        }

        if (! (is_numeric($eid) && $eid > 0)) {
            throw new InvalidArgumentException('Entity id must be numeric');
        }

        $entity = $this->get($eid);

        $id = $entity->getPrimary();

        $deletion = $this->entityTable->delete(array($id => $eid));

        $this->getEventManager()->trigger('delete', $entity);

        return $deletion;
    }

}
