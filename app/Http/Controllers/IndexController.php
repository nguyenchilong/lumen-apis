<?php

namespace App\Http\Controllers;

use App\Services\WagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @var WagerService
     */
    private $wagerService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WagerService $wagerService)
    {
        $this->wagerService = $wagerService;
    }

    /**
     * create wager
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createWager(Request $request): \Illuminate\Http\JsonResponse
    {
        /**
         * validate
         */
        $validator = Validator::make($request->all(), [
            'total_wager_value' => 'required|integer',
            'odds' => 'required|integer',
            'selling_percentage' => 'required|integer|min:1|max:100',
            'selling_price' => 'required|numeric|between:0,99.99'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'NOT_ACCEPTABLE',
                'message' => $validator->errors()->all()
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $sellingPercentage = floatval($request->input('total_wager_value') * ($request->input('selling_percentage') / 100));
        if (floatval($request->input('selling_price')) <= $sellingPercentage) {
            return response()->json([
                'error' => 'NOT_ACCEPTABLE',
                'message' => 'The value of [selling_price] must be greater than the result of [total_wager_value * (selling_percentage / 100)]'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        /**
         * start create wager
         */
        $result = $this->wagerService->doCreateWager($request);
        if (!empty($result)) {
            return response()->json($result, Response::HTTP_CREATED);
        }
        return response()->json([
            'error' => 'NOT_ACCEPTABLE',
            'message' => 'There was an error during processing, please try again'
        ], Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * buy wager by id
     * @param Request $request
     * @param int $wagerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyWager(Request $request, int $wagerId): \Illuminate\Http\JsonResponse
    {
        /**
         * validate input
         */
        $validator = Validator::make($request->all(), [
            'buying_price' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'NOT_ACCEPTABLE',
                'message' => $validator->errors()->all()
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        /**
         * check exist in db
         */
        $hasWager = $this->wagerService->getOne($wagerId);
        if (empty($hasWager->id)) {
            return response()->json([
                'error' => 'NOT_FOUND',
            ], Response::HTTP_NOT_FOUND);
        }
        if (floatval($request->input('buying_price')) > $hasWager->current_selling_price) {
            return response()->json([
                'error' => 'NOT_ACCEPTABLE',
                'message' => '[buying_price] must be lesser or equal to [current_selling_price] of the [wager_id]'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        /**
         * start buy
         */
        $result = $this->wagerService->doBuyWager($request, $wagerId);
        if (!empty($result)) {
            return response()->json($result, Response::HTTP_CREATED);
        }
        return response()->json([
            'error' => 'NOT_ACCEPTABLE',
            'message' => 'There was an error during processing, please try again'
        ], Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * get list wagers
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listWager(Request $request): \Illuminate\Http\JsonResponse
    {
        $results = $this->wagerService->getListWagers($request);
        return response()->json($results);
    }
}
