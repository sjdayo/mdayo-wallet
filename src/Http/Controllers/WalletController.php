<?php

namespace Mdayo\Wallet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mdayo\Wallet\Http\Resources\LedgerResource;
use Mdayo\Wallet\Http\Resources\WalletBalanceResource;
use Mdayo\Wallet\Http\Resources\WalletResource;
use Mdayo\Wallet\Models\Wallet;

/**
 * @group Wallet Management
 * APIs for managing wallets, balances, and ledgers
 */

/**
 * @OA\Tag(
 *     name="Wallet",
 *     description="Operations for wallet balances and ledgers"
 * )
 * @OA\Schema(
 *     schema="Wallet",
 *     type="object",
 *     title="Wallet",
 *     description="A user or owner wallet",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="owner_type", type="string", example="App\\Models\\User"),
 *     @OA\Property(property="owner_id", type="integer", example=1),
 *     @OA\Property(property="digital_address", type="string", example="EKRLE1234567890"),
 *     @OA\Property(
 *         property="balances",
 *         type="array",
 *         description="Balances for different currencies",
 *         @OA\Items(ref="#/components/schemas/WalletBalance")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-27T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-27T10:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="WalletBalance",
 *     type="object",
 *     title="Wallet Balance",
 *     description="Balance information for a specific currency in a wallet",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="wallet_id", type="integer", example=1),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="balance", type="number", format="float", example=100.50),
 *     @OA\Property(property="frozen", type="number", format="float", example=10.00),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-27T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-27T10:00:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Ledger",
 *     type="object",
 *     title="Wallet Ledger",
 *     description="Ledger entry representing a credit or debit operation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", enum={"credit","debit"}, example="credit"),
 *     @OA\Property(property="amount", type="number", format="float", example=50.25),
 *     @OA\Property(property="balance_before", type="number", format="float", example=100.50),
 *     @OA\Property(property="balance_after", type="number", format="float", example=150.75),
 *     @OA\Property(property="reference", type="string", example="102826382020123"),
 *     @OA\Property(property="meta", type="object", example={"note":"Order payment"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-27T10:00:00Z")
 * )
 */

class WalletController extends Controller
{
    private function successResponse(string $message, $data = null, ?array $meta = [])
    {
        $response = [
            'code' => 0,
            'success' => true,
            'error' => null,
            'message' => $message,
            'data' => $data,
        ];
        if ($meta) {
            $response['meta'] = $meta;
        }
        return response()->json($response);
    }

    /**
     * List all wallets of the authenticated user
     *
     * @OA\Get(
     *     path="/wallets",
     *     tags={"Wallet"},
     *     summary="List wallets with balances",
     *     description="Fetch all wallets of the authenticated user with their balances. Supports pagination.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="items_per_page",
     *         in="query",
     *         required=false,
     *         description="Number of wallets per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallets fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Wallets fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Wallet")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=45)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'items_per_page' => 'sometimes|integer'
        ]);
        $itemsPerPage = $validated['items_per_page'] ?? 15;
        $wallets = $user->wallets()->with('balances')->paginate($itemsPerPage);
        $data = WalletResource::collection($wallets);
        $meta = [
            'current_page' => $wallets->currentPage(),
            'last_page' => $wallets->lastPage(),
            'per_page' => $wallets->perPage(),
            'total' => $wallets->total(),
        ];
        return $this->successResponse('Wallets fetched successfully.', $data, $meta);
    }

    /**
     * Show a specific wallet
     *
     * @OA\Get(
     *     path="/wallets/{digital_address}",
     *     tags={"Wallet"},
     *     summary="Show a wallet with balances",
     *     description="Fetch a specific wallet by ID along with its balances.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="digital_address",
     *         in="path",
     *         required=true,
     *         description="Wallet digital address",
     *         @OA\Schema(type="string", example="D5XWTY9HEY")
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         required=false,
     *         description="Currency code",
     *         @OA\Schema(type="string", example="USD")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Wallet fetched successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Wallet")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Wallet not found")
     * )
     */
    
    public function show(Request $request,string $digital_address)
    {

        $wallet = $request->user()->wallet->where('digital_address',$digital_address)->firstOrFail();
        $currency = $request->currency??null; 
        $wallet->load([
            'balances' => function ($q) use ($currency) {
                $q->when($currency, fn($q) =>
                    $q->where('symbol', $currency)
                );
            }
        ]);

        $data = new WalletResource($wallet);
        return $this->successResponse('Wallet fetched successfully.', $data);
    }

    /**
     * Get ledger of a wallet by currency
     *
     * @OA\Get(
     *     path="/wallets/{digital_address}/ledger",
     *     tags={"Wallet"},
     *     summary="Get wallet ledger by currency",
     *     description="Fetch paginated ledger entries for a wallet filtered by currency.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="digital_address",
     *         in="path",
     *         required=true,
     *         description="Wallet digital address",
     *         @OA\Schema(type="string", example="D5XWTY9HEY")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Ledger type (credit or debit)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"credit", "debit"},  // enum definition
     *             example="credit"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         required=true,
     *         description="Currency code",
     *         @OA\Schema(type="string", example="USD")
     *     ),
     *     @OA\Parameter(
     *         name="items_per_page",
     *         in="query",
     *         required=false,
     *         description="Number of ledger items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet ledger fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=0),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="string", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="Wallet ledger fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Ledger")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=45)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Wallet or ledger not found")
     * )
     */
    public function ledger(Request $request, atring $digital_address)
    {
        $validated = $request->validate([
            'items_per_page' => 'sometimes|integer',
            'currency' => 'required|string',
            'type' => 'sometimes|string'
        ]);
        $itemsPerPage = $validated['items_per_page'] ?? 15;
        $currency = $validated['currency'];
        $type = $request->type??null;
        $wallet = $request->user()->wallet->where('digital_address',$digital_address)->firstOrfail();
        $balance = $wallet->balances()->whereHas('currency',fn($q)=>$q->where('symbol', $currency))->firstOrFail();
        $ledger = $balance->ledger()->when($type,fn($q)=>$q->where('type',$type))->with('ledgerable')->paginate($itemsPerPage);
       
        $data = LedgerResource::collection($ledger);
        $meta = [
            'current_page' => $ledger->currentPage(),
            'last_page' => $ledger->lastPage(),
            'per_page' => $ledger->perPage(),
            'total' => $ledger->total(),
        ];

        return $this->successResponse('Wallet ledger fetched successfully.', $data, $meta);
    }
}
