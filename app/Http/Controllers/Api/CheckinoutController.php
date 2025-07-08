<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkinout;
use App\Models\Userinfo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Repository\Checkinout\EloquentCheckinoutRepository;
use Illuminate\Http\Response;
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

    public function getByDate(Request $request)
    {
        $request->validate([
            'badge_number' => ['required', 'string'], // Added string validation
            'date' => ['required', 'date_format:Y-m-d'], // Specific date format for consistency
        ]);

        try {
            // Find the user directly using the User model or a dedicated service/repository
            // This assumes 'badge_number' is a column in your User model
            $user = Userinfo::where('badgenumber', $request->badge_number)->first();

            if (!$user) {
                // Return a 404 for not found users, adhering to REST principles
                return response()->json([
                    'status' => 'Error',
                    'message' => 'User with given badge number not found.'
                ], Response::HTTP_NOT_FOUND); // Using Symfony Response for clarity
            }

            // Directly query Checkinout using the user's ID
            $data = Checkinout::where('userid', $user->userid) // Assuming 'userid' is the foreign key
                                ->whereDate('checktime', $request->date)
                                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Catch any unexpected errors
            return response()->json([
                'status' => 'Error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getByMonth(Request $request)
    {
        $request->validate([
            'badge_number' => ['required', 'string'],
            'month' => ['required', 'integer', 'between:1,12'], // Validate month as integer between 1 and 12
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date("Y") + 10)], // Validate year as integer within a reasonable range
        ]);

        try {
            $user = Userinfo::where('badgenumber', $request->badge_number)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'User with given badge number not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = Checkinout::where('userid', $user->userid)
                ->whereYear('checktime', $request->year)
                ->whereMonth('checktime', $request->month)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
