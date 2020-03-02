<?php

namespace salpay\models;

use salpay\helpers\DateHelper;
use \DateTime;

class MainModel
{
    private $_year;
    private $_content;

    public $filename;
    public $message;

    const COLUMNS = ['month', 'bonus_date', 'payday_date'];
    const INCLUDE_FIRST_MONTH_BONUS = false;

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

            $bonusDateFormat = $i === 1 && !self::INCLUDE_FIRST_MONTH_BONUS ? '-' : $bonusDate->format('d-m-Y');
            $paydayDateFormat = $paydayDate->format('d-m-Y');

        $result[$paydayDate->format('F')] = [
                'bonus_date' => $bonusDateFormat,
                'payday_date' => $paydayDateFormat
            ];
        }

        return $result;
    }

    /**
     * Stick together an array of dates and column names.
     *
     * @param array $initialFields
     * @return bool
     */
    public function generateContent(array $initialFields): bool
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
     * Create a file with given name and generated dates. If file already exists
     * throws \Exception.
     *
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function createFile(string $name): bool
    {
        $isCreatedContent = $this->generateContent(self::COLUMNS);

        if (!$isCreatedContent) {
            return false;
        }

        $this->filename = dirname(dirname(__DIR__)) . "/output/${name}.csv";
        $resource = fopen($this->filename, 'xt');

        if (!$resource) {
            throw new \Exception("The filename '{$name}' is already taken. Try to choose another");
        }

        array_map(function ($el) use ($resource) {
            fputcsv($resource, $el);
        }, $this->_content);
        return true;
    }
}
