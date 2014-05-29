<?php

namespace Backend\Controller\Plugin\Booking;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DetermineFilters extends AbstractPlugin
{

    public function __invoke($search)
    {
        $controller = $this->getController();

        $matches = array();

        $filters = array();
        $filterParts = array();

        preg_match_all('/\(([^\(\)]+[<=>][^\(\)]+)\)/', $search, $matches);

        if ($matches) {

            /* Determine filters from matches */

            foreach ($matches[1] as $match) {
                $parts = preg_split('/([<=>])/', $match, -1, PREG_SPLIT_DELIM_CAPTURE);

                $key = strtolower(trim($parts[0]));
                $operator = trim($parts[1]);
                $value = trim($parts[2]);

                // Translate keys
                $key = str_replace(
                    array(
                        strtolower($controller->t('User')),
                        strtolower($controller->option('subject.square.type')),
                        strtolower($controller->t('Created')),
                    ),
                    array('user', 'sid', 'created'),
                    $key);

                // Translate values
                $value = str_replace(
                    array(
                        strtolower($controller->t('Single')),
                        strtolower($controller->t('Subscription')),
                        strtolower($controller->t('Cancelled'))),
                    array('single', 'subscription', 'cancelled'),
                    $value);

                // Transform dates
                try {
                    switch ($key) {
                        case 'created':
                            if (preg_match('/[0-3]?[0-9]\.[0-1]?[0-9]\.[1-2][0-9]{3}/', $value)) {
                                $value = implode('-', array_reverse(explode('.', $value)));
                            }

                            $value = (new \DateTime($value))->format('Y-m-d');
                    }
                } catch (\RuntimeException $e) {
                    break;
                }

                $filters[] = sprintf('%s %s "%s"', $key, $operator, $value);
                $filterParts[] = array($key, $operator, $value);
            }
        }

        return array(
            'search' => $search,
            'filters' => $filters,
            'filterParts' => $filterParts,
        );
    }

}