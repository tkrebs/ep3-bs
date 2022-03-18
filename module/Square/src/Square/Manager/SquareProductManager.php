<?php

namespace Square\Manager;

use Base\Manager\AbstractManager;
use InvalidArgumentException;
use RuntimeException;
use Square\Entity\Square;
use Square\Entity\SquareProduct;
use Square\Entity\SquareProductFactory;
use Square\Table\SquareProductTable;
use Zend\Db\Sql\Where;

class SquareProductManager extends AbstractManager
{

    protected $squareProductTable;
    protected $locale;

    /**
     * Creates a new square product manager object.
     *
     * @param SquareProductTable $squareProductTable
     * @param string $locale
     */
    public function __construct(SquareProductTable $squareProductTable, $locale)
    {
        $this->squareProductTable = $squareProductTable;
        $this->locale = $locale;
    }

    /**
     * Saves (updates or creates) a square product.
     *
     * @param SquareProduct $product
     * @return SquareProduct
     * @throws RuntimeException
     */
    public function save(SquareProduct $product)
    {
        if ($product->get('spid')) {

            /* Update existing square product */

            /* Determine updated properties */

            $updates = array();

            foreach ($product->need('updatedProperties') as $property) {
                $updates[$property] = $product->get($property);
            }

            if ($updates) {
                $this->squareProductTable->update($updates, array('spid' => $product->get('spid')));
            }

            $product->reset();

            $this->getEventManager()->trigger('save.update', $product);

        } else {

            /* Insert square product */

            if ($product->getExtra('nspid')) {
                $spid = $product->getExtra('nspid');
            } else {
                $spid = null;
            }

            $this->squareProductTable->insert(array(
                'spid' => $spid,
                'sid' => $product->get('sid'),
                'name' => $product->need('name'),
                'description' => $product->get('description'),
                'options' => $product->need('options'),
                'price' => $product->need('price'),
                'rate' => $product->need('rate'),
                'gross' => $product->need('gross'),
                'priority' => $product->need('priority'),
                'locale' => $product->get('locale'),
            ));

            $spid = $this->squareProductTable->getLastInsertValue();

            if (! (is_numeric($spid) && $spid > 0)) {
                throw new RuntimeException('Failed to save product');
            }

            $product->add('spid', $spid);

            $this->getEventManager()->trigger('save.insert', $product);
        }

        $this->getEventManager()->trigger('save', $product);

        return $product;
    }

    /**
     * Gets the square product by primary id.
     *
     * @param int $spid
     * @param boolean $strict
     * @return SquareProduct
     * @throws RuntimeException
     */
    public function get($spid, $strict = true)
    {
        $product = $this->getBy(array('spid' => $spid));

        if (empty($product)) {
            if ($strict) {
                throw new RuntimeException('This product does not exist');
            }

            return null;
        } else {
            return current($product);
        }
    }

    /**
     * Gets all square products that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null)
    {
        $select = $this->squareProductTable->getSql()->select();

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

        $resultSet = $this->squareProductTable->selectWith($select);

        return SquareProductFactory::fromResultSet($resultSet);
    }

    /**
     * Gets all square products for the passed square.
     *
     * @param Square $square
     * @return array
     */
    public function getBySquare(Square $square)
    {
        $where = new Where();

        $where = $where->nest();
        $where->isNull('sid')->or->equalTo('sid', $square->need('sid'));
        $where = $where->unnest();

        $where = $where->and->nest();
        $where->isNull('locale')->or->equalTo('locale', $this->locale);
        $where = $where->unnest();

        return $this->getBy($where, 'priority ASC');
    }

    /**
     * Gets all square products.
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

    /**
     * Deletes one square product.
     *
     * @param int|SquareProduct $product
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($product)
    {
        if ($product instanceof SquareProduct) {
            $spid = $product->need('spid');
        } else {
            $spid = $product;
        }

        if (! (is_numeric($spid) && $spid > 0)) {
            throw new InvalidArgumentException('Product id must be numeric');
        }

        $product = $this->get($spid);

        $deletion = $this->squareProductTable->delete(array('spid' => $spid));

        $this->getEventManager()->trigger('delete', $product);

        return $deletion;
    }

}