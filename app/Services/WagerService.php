<?php

namespace App\Services;

use App\Models\Buy;
use App\Models\Wager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WagerService
{
    /**
     * get one row by ID
     * @param int $wagerId
     * @return mixed
     */
    public function getOne(int $wagerId)
    {
        return Wager::find($wagerId);
    }
    /**
     * get list wager and have paging
     * @param Request $request
     * @return array
     */
    public function getListWagers(Request $request): array
    {
        $page = $request->input('page', env('DEFAULT_PAGE'));
        $limit = $request->input('limit', env('DEFAULT_LIMIT'));
        $counter = Wager::all()->count();
        $wagers = Wager::forPage($page, $limit)->get();
        $results = [];
        if ($wagers->isNotEmpty()) {
            foreach ($wagers as $wager) {
                array_push($results, [
                    'id'                    => $wager->id,
                    'total_wager_value'     => $wager->total_wager_value,
                    'odds'                  => $wager->odds,
                    'selling_percentage'    => $wager->selling_percentage,
                    'selling_price'         => $wager->selling_price,
                    'current_selling_price' => $wager->current_selling_price,
                    'percentage_sold'       => $wager->percentage_sold,
                    'amount_sold'           =>  $wager->amount_sold,
                    'placed_at'             => Carbon::parse($wager->placed_at)->format('Y-m-d H:i:s')
                ]);
            }
            $results['total_pages'] = $counter;
        }
        return $results;
    }

    /**
     * buy wager
     * @param Request $request
     * @param int $wagerId
     * @return array
     */
    public function doBuyWager(Request $request, int $wagerId): array
    {
        $result = [];
        $boughtAt = Carbon::now()->format('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            $created = Buy::create([
                'wager_id'     => $wagerId,
                'buying_price' => $request->input('buying_price'),
                'bought_at'    => $boughtAt,
            ]);
            if (!empty($created->id)) {
                $result = [
                    'id'           => $created->id,
                    'wager_id'     => $wagerId,
                    'buying_price' => $request->input('buying_price'),
                    'bought_at'    => $boughtAt
                ];
                $sum = DB::table('wagers AS w')
                    ->selectRaw('sum(w.selling_price) as amount_sold, sum(w.selling_percentage) as percentage_sold')
                    ->join('buy AS b', 'w.id', '=', 'b.wager_id')
                    ->first();
                $wager = $this->getOne($wagerId);
                $wager->current_selling_price = $request->input('buying_price');
                $wager->percentage_sold = $sum->percentage_sold;
                $wager->amount_sold = $sum->amount_sold;
                $wager->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug('[WagerService->doBuyWager] Error Message => ' . $exception->getMessage());
        }
        return $result;
    }

    /**
     * create wager
     * @param Request $request
     * @return array
     */
    public function doCreateWager(Request $request): array
    {
        $result = [];
        DB::beginTransaction();
        try {
            $placedAt = Carbon::now()->format('Y-m-d H:i:s');
            $created = Wager::create([
                'total_wager_value'    => $request->input('total_wager_value'),
                'odds'                  => $request->input('odds'),
                'selling_percentage'    => $request->input('selling_percentage'),
                'selling_price'         => $request->input('selling_price'),
                'current_selling_price' => $request->input('selling_price'),
                'percentage_sold'       => 0,
                'amount_sold'           => 0,
                'placed_at'             => $placedAt,
            ]);
            if (!empty($created->id)) {
                $result = [
                    'id'                    => $created->id,
                    'total_wager_value'     => $request->input('total_wager_value'),
                    'odds'                  => $request->input('odds'),
                    'selling_percentage'    => $request->input('selling_percentage'),
                    'selling_price'         => $request->input('selling_price'),
                    'current_selling_price' => $request->input('selling_price'),
                    'percentage_sold'       => '0.00',
                    'amount_sold'           => '0.00',
                    'placed_at'             => $placedAt
                ];
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug('[WagerService->doCreateWager] Error Message => ' . $exception->getMessage());
        }
        return $result;
    }
}
