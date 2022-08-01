<?php

namespace App\Repository\Checkinout;

use App\Models\Checkinout;
use App\Models\Userinfo;

class EloquentCheckinoutRepository implements CheckinoutRepository
{
    public function store($params)
    {
        $checkinout = new Checkinout;

        $checkinout->userid = $params['userid'];
        $checkinout->checktime = now();
        $checkinout->checktype = $params['checktype'];
        $checkinout->verifycode = $params['verifycode'];
        $checkinout->SN = $params['SN'];

        $checkinout->save();

        return $checkinout;
    }

    public function getUserID($badge_number)
    {
        return Userinfo::findOrFail($badge_number);
    }
}
