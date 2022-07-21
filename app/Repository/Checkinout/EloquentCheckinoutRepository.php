<?php

namespace App\Repository\Checkinout;

use App\Models\Checkinout;

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
        $checkinout->sensorid = $params['sensorid'];
        $checkinout->Workcode = $params['Workcode'];
        $checkinout->Reserved = $params['Reserved'];

        $checkinout->save();

        return $checkinout;
    }
}
