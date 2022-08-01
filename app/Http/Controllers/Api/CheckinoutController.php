<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\Checkinout\EloquentCheckinoutRepository;
use Illuminate\Support\Facades\DB;
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
            'badge_number' => 'required',
            'SN' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'error' => $validator->errors()], 422);
        }


        try {
            $userID = $this->eloquentCheckinout->getUserID($request->badge_number);

            $params = [
                'userid' => $userID->userid,
                'SN' => $request->SN,
                'checktype' => 0,
                'verifycode' => 9,
            ];

            $store = $this->eloquentCheckinout->store($params);

            if (!empty($store)) {
                return response()->json(['status' => 'success', 'data' => $store], 201);
            }

            return response()->json(['status' => 'error', 'error' => 'Cannot store data, internal server error'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }
}
