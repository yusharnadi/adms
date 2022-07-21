<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\Checkinout\EloquentCheckinoutRepository;
use Illuminate\Support\Facades\Validator;

class CheckinoutController extends Controller
{
    protected $eloquentCheckinout;

    public function __construct(EloquentCheckinoutRepository $eloquentChekinout)
    {
        $this->eloquentCheckinout = $eloquentChekinout;
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric',
            'SN' => 'required',
            'checktype' => 'required',
            'verifycode' => 'required',
            'sensorid' => 'required',
            'Workcode' => 'required',
            'Reserved' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'error' => $validator->errors()], 422);
        }

        $params = [
            'userid' => $request->userid,
            'SN' => $request->SN,
            'checktype' => $request->checktype,
            'verifycode' => $request->verifycode,
            'sensorid' => $request->sensorid,
            'Workcode' => $request->Workcode,
            'Reserved' => $request->Reserved
        ];

        $store = $this->eloquentCheckinout->store($params);

        if (!empty($store)) {
            return response()->json(['status' => 'success', 'data' => $store], 201);
        }

        return response()->json(['status' => 'error', 'error' => 'Cannot store data, internal server error'], 500);
    }
}
