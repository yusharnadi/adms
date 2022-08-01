<?php

namespace App\Repository\Checkinout;


interface CheckinoutRepository
{
    public function store($params);
    public function getUserID($badge_number);
}
