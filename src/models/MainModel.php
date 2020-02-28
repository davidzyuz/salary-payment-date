<?php

namespace salpay\models;

use salpay\helpers\DateHelper;
use \DateTime;

class MainModel
{
    private $_year;
    private $_content;

    public function __construct(string $year = '')
    {
        $this->_year = empty($year) ? date('Y') : $year;
    }

    /**
     * Generates dates for salary payments
     *
     * @return array
     * @throws \Exception
     */
    public function generateDates(): array
    {
        $dthelper = new DateHelper();
        $result = [];

        for ($i = 1; $i <= 12; $i += 1) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $dateString = $dthelper->generateDateString($this->_year, $month, '15');
            $bonusDate = new DateTime($dateString);

            if ($bonusDate->format('N') > 5) {
                $bonusDate = $dthelper->shiftDate(
                    $bonusDate,
                    DateHelper::RESERVE_BONUS_DATE,
                    DateHelper::SHIFT_UP
                );
            }

            $dateString = $dthelper->generateDateString($this->_year, $month, $bonusDate->format('t'));
            $paydayDate = new DateTime($dateString);

            if ($paydayDate->format('N') > 5) {
                $paydayDate = $dthelper->shiftDate(
                    $paydayDate,
                    DateHelper::RESERVE_PAYDAY_DATE,
                    DateHelper::SHIFT_DOWN
                );
            }

        $result[$paydayDate->format('F')] = [
                'bonus_date' => $bonusDate->format('d'),
                'payday_date' => $paydayDate->format('d')
            ];
        }

        return $result;
    }

    /**
     * @param array $initialFields
     * @return bool
     */
    public function createContent(array $initialFields): bool
    {
        $fields = [$initialFields];
        try {
            $data = $this->generateDates();
        } catch (\Exception $e) {
            return false;
        }

        foreach ($data as $month => $dates) {
            $dateKeys = array_keys($dates);
            $fields[] = [$month, $dates[$dateKeys[0]], $dates[$dateKeys[1]]];
        }

        $this->_content = $fields;
        return true;
    }

    /**
     * @param string $name
     * @param array $initialFields
     * @return bool
     */
    public function generateFile(string $name): bool
    {
        $isCreatedContent = $this->createContent(['month', 'bonus_date', 'payday_date']);

        if (!$isCreatedContent) {
            return false;
        }

        $filename = './output/' . $name . '.csv';
        $resource = fopen($filename, 'x+t');

        if (!$resource) {
            return false;
        }

        array_map(function ($el) use ($resource) {
            fputcsv($resource, $el);
        }, $this->_content);

        return true;
    }
}
