<?php


namespace salpay\helpers;


class DateHelper
{
    /**
     * Reserve weekday for bonus payment
     * 1 - monday
     * 7 - sunday
     */
    const RESERVE_BONUS_DATE = 3;
    const RESERVE_PAYDAY_DATE = 5;
    const SHIFT_UP = 'up';
    const SHIFT_DOWN = 'down';

    /**
     * Generates a date string
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @return string
     */
    public function generateDateString(string $year, string $month, string $day): string
    {
        return $year . '-' . $month . '-' . $day;
    }

    /**
     * Shifts date depending on direction.
     * @param \DateTime $obj
     * @param int $condition
     * @param string $direction
     * @return \DateTime
     */
    public function shiftDate(\DateTime $obj, int $condition, string $direction): \DateTime
    {
        $directStr = $direction === self::SHIFT_UP ? '+1 day' : '-1 day';
        while((int)$obj->format('N') !== $condition) {
            $obj->modify($directStr);
        }

        return $obj;
    }
}
