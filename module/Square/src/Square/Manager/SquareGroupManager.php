<?php

namespace Square\Manager;

use Base\Manager\AbstractManager;
use InvalidArgumentException;
use RuntimeException;
use Square\Entity\Square;
use Square\Entity\SquareGroup;
use Square\Entity\SquareGroupFactory;
use Square\Table\SquareGroupTable;
use Zend\Db\Sql\Where;

class SquareGroupManager extends AbstractManager
{

    protected $squareGroupTable;
    protected $locale;

    /**
     * Creates a new square group manager object.
     *
     * @param SquareGroupTable $squareGroupTable
     * @param string $locale
     */
    public function __construct(SquareGroupTable $squareGroupTable, $locale)
    {
        $this->squareGroupTable = $squareGroupTable;
        $this->locale = $locale;
    }

    /**
     * Saves (updates or creates) a square group.
     *
     * @param SquareGroup $group
     * @return SquareGroup
     * @throws RuntimeException
     */
    public function save(SquareGroup $group)
    {
        if ($group->get('sgid')) {

            /* Update existing square group */

            /* Determine updated properties */

            $updates = array();

            foreach ($group->need('updatedProperties') as $property) {
                $updates[$property] = $group->get($property);
            }

            if ($updates) {
                $this->squareGroupTable->update($updates, array('sgid' => $group->get('sgid')));
            }

            $group->reset();

            $this->getEventManager()->trigger('save.update', $group);

        } else {

            /* Insert square group */

            // if ($group->getExtra('nsgid')) {
            //     $sgid = $group->getExtra('nsgid');
            // } else {
            //     $sgid = null;
            // }
            
            $sgid = null;

            $this->squareGroupTable->insert(array(
                'sgid' => $sgid,
                'description' => $group->get('description'),
            ));

            $sgid = $this->squareGroupTable->getLastInsertValue();

            if (! (is_numeric($sgid) && $sgid > 0)) {
                throw new RuntimeException('Failed to save group');
            }

            $group->add('sgid', $sgid);

            $this->getEventManager()->trigger('save.insert', $group);
        }

        $this->getEventManager()->trigger('save', $group);

        return $group;
    }

    /**
     * Gets the square group by primary id.
     *
     * @param int $sgid
     * @param boolean $strict
     * @return SquareGroup
     * @throws RuntimeException
     */
    public function get($sgid, $strict = true)
    {
        $group = $this->getBy(array('sgid' => $sgid));

        if (empty($group)) {
            if ($strict) {
                throw new RuntimeException('This group does not exist');
            }

            return null;
        } else {
            return current($group);
        }
    }

    /**
     * Gets all square group that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null)
    {
        $select = $this->squareGroupTable->getSql()->select();

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

        $resultSet = $this->squareGroupTable->selectWith($select);

        return SquareGroupFactory::fromResultSet($resultSet);
    }

    /**
     * Gets all square groups.
     *
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($order = null, $limit = null, $offset = null)
    {
        return $this->getBy(null, $order, $limit, $offset);
    }

    public function getOptions()
    {
        $squaregroups = $this->getAll();
        $groupoptions = [];
        foreach ($squaregroups as $squaregroup) {
            $groupoptions[$squaregroup->get("sgid")] = $squaregroup->get("description");
        }  
        
        return $groupoptions;
        
    }

    /**
     * Deletes one square group.
     *
     * @param int|SquareGroup $group
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($group)
    {
        if ($group instanceof SquareGroup) {
            $sgid = $group->need('sgid');
        } else {
            $sgid = $group;
        }

        if (! (is_numeric($sgid) && $sgid > 0)) {
            throw new InvalidArgumentException('Group id must be numeric');
        }

        $group = $this->get($sgid);

        $deletion = $this->squareGroupTable->delete(array('sgid' => $sgid));

        $this->getEventManager()->trigger('delete', $group);

        return $deletion;
    }

}