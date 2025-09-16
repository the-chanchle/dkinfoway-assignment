<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StockEntry;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = StockEntry::with('store');

        if ($request->has('filter') && strlen(trim($request->input('filter'))) > 0) {
            $f = $request->input('filter');
            $query->where(function ($q) use ($f) {
                $q->where('item_name', 'like', "%{$f}%")
                    ->orWhere('item_code', 'like', "%{$f}%")
                    ->orWhere('location', 'like', "%{$f}%");
            });
        }

        if ($request->has('sortField')) {
            $dir = $request->input('sortDir', 'asc');
            $query->orderBy($request->input('sortField'), $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $page = max(1, (int)$request->input('page', 1));
        $size = (int)$request->input('size', 20);
        $total = $query->count();
        $data = $query->skip(($page - 1) * $size)->take($size)->get();

        return response()->json([
            'data'      => $data,
            'last_page' => (int)ceil($total / $size),
            'total'     => $total,
            'page'      => $page
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'                 => 'required|array|min:1',
            'items.*.item_code'     => 'required|string',
            'items.*.item_name'     => 'required|string',
            'items.*.quantity'      => 'required|integer',
            'items.*.store_id'      => 'required|integer|exists:stores,id',
            'items.*.in_stock_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $items = $request->input('items');

        DB::beginTransaction();
        try {
            $saved = [];
            foreach ($items as $row) {
                $entry = StockEntry::create([
                    'item_code'     => $row['item_code'],
                    'item_name'     => $row['item_name'],
                    'quantity'      => (int)$row['quantity'],
                    'location'      => isset($row['location']) ? $row['location'] : null,
                    'store_id'      => (int)$row['store_id'],
                    'in_stock_date' => $row['in_stock_date'],
                    'status'        => 'pending'
                ]);

                DB::table('stock_entries')->where('id', $entry->id)
                    ->update(['stock_no' => 100000 + $entry->id]);

                $saved[] = $entry->fresh();
            }

            DB::commit();
            return response()->json(['data' => $saved], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $entry = StockEntry::findOrFail($id);
        $entry->delete();
        return response()->json(null, 204);
    }
}