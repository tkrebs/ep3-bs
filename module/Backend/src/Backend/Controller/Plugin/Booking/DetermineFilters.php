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
                        str_replace(' ', '_', strtolower($controller->t('User ID'))),
                        strtolower($controller->t('User')),
                        str_replace(' ', '_', strtolower($controller->t('Square ID'))),
                        str_replace(' ', '_', strtolower($controller->t('Billing status'))),
                        strtolower($controller->t('Visibility')),
                        strtolower($controller->t('Quantity')),
                        strtolower($controller->t('Created')),
                    ),
                    array('uid', 'uid', 'sid', 'status_billing', 'visibility', 'quantity', 'created'),
                    $key);

                // Translate values
                $value = str_replace(
                    array(
                        strtolower($controller->t('Single')),
                        strtolower($controller->t('Subscription')),
                        strtolower($controller->t('Cancelled')),
                        strtolower($controller->t('Pending')),
                        strtolower($controller->t('Paid')),
                        strtolower($controller->t('Uncollectable')),
                        strtolower($controller->t('Public')),
                        strtolower($controller->t('private')),
                        ),
                    array('single', 'subscription', 'cancelled', 'pending', 'paid', 'uncollectable', 'public', 'private'),
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
