<?php

namespace App\Http\Controllers\API\Driver\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rules;
use App\Models\UserInfo;
use App\Models\ListOption;
use App\Models\RuleAssign;

class CycleRuleAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $cycle = Rules::where('show_id', 2)
                ->where('is_active', 1)
                ->get();

            $cycleIds = $cycle->pluck('id');

            $data['selectCycle'] = RuleAssign::whereIn('rule_id', $cycleIds)->where('user_id', $userId)->select('id', 'rule_id', 'user_id')->with('rule')->first();

            $restart = Rules::where('show_id', 4)
                ->where('is_active', 1)
                ->get();

            $restartIds = $restart->pluck('id');

            $data['selectRestart'] = RuleAssign::whereIn('rule_id', $restartIds)->where('user_id', $userId)->select('id', 'rule_id', 'user_id')->with('rule')->first();

            $break = Rules::where('show_id', 3)
                ->where('is_active', 1)
                ->get();

            $breakIds = $break->pluck('id');

            $data['selectBreak'] = RuleAssign::whereIn('rule_id', $breakIds)->where('user_id', $userId)->select('id', 'rule_id', 'user_id')->with('rule')->first();

            $advrs = Rules::where('show_id', 5)
                ->where('is_active', 1)
                ->get();

            $advIds = $advrs->pluck('id');

            $data['selectAdverse'] = RuleAssign::whereIn('rule_id', $advIds)->where('user_id', $userId)->with('rule')->get();

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $cargoTypeId = $userInfo->cargo_type_id;

            $listOptions = ListOption::where('list_id', 'cargo_type')
                ->where('option_id', $cargoTypeId)
                ->select('option_id', 'title')
                ->first();

            $data['selectCargo'] = $listOptions;

            $data['cargo'] = ListOption::where('list_id', 'cargo_type')->select('option_id', 'title')->get();

            $data['cycle'] = Rules::where('show_id', 2)->select('id', 'name', 'title')->where('is_active', 1)->get();

            $data['restart'] = Rules::where('show_id', 4)->select('id', 'name', 'title')->where('is_active', 1)->get();

            $data['break'] = Rules::where('show_id', 3)->select('id', 'name', 'title')->where('is_active', 1)->get();

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => "Data fetched successfully!",
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 401,
                'message' => "User not authenticated!",
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $restBreak = $request->rest_break;
            $restart = $request->restart;
            $cycleRule = $request->cycle_rule;
            $cargoType = $request->cargo_type;
            $adverse = $request->adverse_condition;

            RuleAssign::where('user_id', '=', $userId)->delete();

            // Initialize rules array with fixed values
            $rules = [
                ['rule_id' => 1],
                ['rule_id' => 2],
                ['rule_id' => 7],
                ['rule_id' => 9],
            ];

            // Add conditional rule if the condition is met
            if ($adverse == '1') {
                $rules[] = ['rule_id' => 10];
            }

            // Add rules based on request values, ensuring they are arrays
            if ($request->rest_break) {
                $rules[] = ['rule_id' => $restBreak];
            }
            if ($request->restart) {
                $rules[] = ['rule_id' => $restart];
            }
            if ($request->cycle_rule) {
                $rules[] = ['rule_id' => $cycleRule];
            }

            $userInfo->update([
                'cargo_type_id' => $cargoType
            ]);

            // Insert each rule into the database
            foreach ($rules as $rule) {
                RuleAssign::create([
                    'rule_id' => $rule['rule_id'],
                    'user_id' => $user->id,
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                    'updated_by' => Auth::user()->id,
                ]);
            }

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => 'Data updated successfully!',
            ], 200);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 401,
                'message' => "User not authenticated!",
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
