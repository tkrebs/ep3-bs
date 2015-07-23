<?php

namespace Booking\Service;

use Base\Manager\OptionManager;
use Base\Service\AbstractService;

class BookingStatusService extends AbstractService
{

    protected $optionManager;

    protected $statusColorsBuffer;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function checkStatusColors($statusColors)
    {
        if (! $statusColors) {
            return true;
        }

        $statusColorsArray = $this->parseStatusColors($statusColors);

        if (empty($statusColorsArray)) {
            return false;
        }

        if (! isset($statusColorsArray['pending'])) {
            return false;
        }

        if (! isset($statusColorsArray['paid'])) {
            return false;
        }

        if (! isset($statusColorsArray['cancelled'])) {
            return false;
        }

        if (! isset($statusColorsArray['uncollectable'])) {
            return false;
        }

        return true;
    }

    public function checkStatus($slug)
    {
        $statusColors = $this->getStatusColors();

        if (isset($statusColors[$slug])) {
            return true;
        } else {
            return false;
        }
    }

    public function setStatusColors($statusColors, $locale = null)
    {
        $this->optionManager->set('service.status-values.billing', $statusColors, $locale);
    }

    public function getStatusColors()
    {
        if (! $this->statusColorsBuffer) {
            $this->statusColorsBuffer = $this->parseStatusColors($this->getStatusColorsRaw());
        }

        return $this->statusColorsBuffer;
    }

    public function getStatusColor($slug)
    {
        $statusColorsArray = $this->getStatusColors();

        if (isset($statusColorsArray[$slug])) {
            return $statusColorsArray[$slug]['color'];
        }

        return null;
    }

    public function getStatusTitle($slug)
    {
        $statusColorsArray = $this->getStatusColors();

        if (isset($statusColorsArray[$slug])) {
            return $statusColorsArray[$slug]['title'];
        }

        return strtoupper($slug);
    }

    public function getStatusTitles()
    {
        $statusTitles = array();

        $statusColorsArray = $this->getStatusColors();

        foreach ($statusColorsArray as $slug => $statusColorsItem) {
            $statusTitles[$slug] = $statusColorsItem['title'];
        }

        return $statusTitles;
    }

    public function getStatusColorsRaw()
    {
        return $this->optionManager->get('service.status-values.billing',
            $this->translate("Pending (pending)\nPaid (paid)\nCancelled (cancelled)\nUncollectable (uncollectable)"));
    }

    protected function parseStatusColors($statusColors)
    {
        $statusColorsArray = array();

        $lines = explode("\n", $statusColors);

        foreach ($lines as $line) {

            $lineContent = trim($line);

            if (strlen($lineContent) > 3) {

                preg_match('~^(.*) *(\(.*\))? *(#[a-f0-9]+)?$~Uis', $lineContent, $matches);

                if (isset($matches[1]) && $matches[1]) {
                    $title = trim(stripslashes(strip_tags($matches[1])));
                } else {
                    $title = null;
                }

                if (isset($matches[2]) && $matches[2]) {
                    $slug = $this->slugify(trim(trim($matches[2], '( )')));
                } else {
                    $slug = $this->slugify($title);
                }

                if (isset($matches[3]) && $matches[3]) {
                    $color = trim($matches[3]);
                } else {
                    $color = null;
                }

                if ($title && $slug) {

                    $statusColorsArray[$slug] = array(
                        'title' => $title,
                        'color' => $color,
                    );
                }
            }
        }

        return $statusColorsArray;
    }

    protected function slugify($slug)
    {
        $slug = str_replace(array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'), array('Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss'), $slug);

        $slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);
        $slug = trim($slug, '-');
        $slug = strtolower($slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        return $slug;
    }

}
