<?php

namespace App\Helpers;

class DateBetween
{
    protected $months;

    public function diffMonths($monthFrom, $monthTo)
    {
        date_default_timezone_set('Asia/Dhaka');

        $month_from = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $monthFrom))));
        $month_to = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $monthTo))));

        $diff =  $month_from->diff($month_to);

        $months = ($diff->y * 12 + $diff->m + $diff->d / 30) + 1;


        $this->months = (int) round($months);

        return $this->months;
    }
}
