<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
    public function getAllTransactions(Request $request)
    {
        $userId = $request->user()->id;
        // get user's transactions
        $transactionQuery = $this->transaction
            ->where('user_id', $userId);

        $transactions = $transactionQuery->get();

        $balance = $transactionQuery->sum('amount');

        return response()->json([
            'transactions' => $transactions,
            'balance' => $balance,
        ], 200);
    }
    public function getAllDeposits(Request $request)
    {
        $userId = $request->user()->id;
        // get user's deposit transactions
        $depositQuery = $this->transaction
            ->where('user_id', $userId)
            ->where('type', 'deposit');

        $transactions = $depositQuery->get();
        $balance = $depositQuery->sum('amount');

        return response()->json([
            'transactions' => $transactions,
            'balance' => $balance,
        ], 200);
    }

    public function deposit(TransactionRequest $request)
    {
        $input = $request->all();

        $input['user_id'] = $request->user()->id;
        $input['type'] = 'deposit';
        $input['fee'] = 0;

        $input['balance'] = $request->user()->balance + $input['amount'];

        DB::beginTransaction();
        try {
            $transaction = $this->transaction->create($input);

            $request->user()->update(['balance' => $input['balance']]);
            DB::commit();
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Transaction failed'], 500);
        }
    }

    public function getAllWithdrawn(Request $request)
    {
        $userId = $request->user()->id;
        // get user's withdrawal transactions
        $transactionQuery = $this->transaction
            ->where('user_id', $userId)
            ->where('type', 'withdrawal');

        $transactions = $transactionQuery->get();

        $balance = $transactionQuery->sum('amount');

        return response()->json([
            'transactions' => $transactions,
            'balance' => $balance,
        ], 200);
    }

    public function withdrawal(TransactionRequest $request)
    {
        $input = $request->all();
        $user = $request->user();
        $withdrawalAmount = $request->amount;
        $withdrawalRate = 0.0;
        if ($user->type == 'individual') {
            $dayOfWeek = date('l');

            if ($dayOfWeek == 'Friday') {
                $withdrawalRate = 0.0;
            } else {
                // check if withdrawal amount is less than 1000
                if ($withdrawalAmount <= 1000) {
                    $withdrawalRate = 0.0;
                } else {
                    // get total withdrawal this month
                    $totalWithdrawalThisMonth = $user->totalWithdrawnThisMonth();

                    // get remaining withdrawal this month
                    $remainingWithdrawalThisMonth = 5000 - $totalWithdrawalThisMonth;

                    // check if withdrawal amount is less than remaining withdrawal this month
                    if ($withdrawalAmount <= $remainingWithdrawalThisMonth) {
                        $withdrawalRate = 0.0;
                    } else {
                        $withdrawalRate = 0.015;
                    }
                }

            }
        } else if ($user->type == 'business') {
            // get total withdrawal
            $totalWithdrawn = $user->totalWithdrawn();
            if ($totalWithdrawn >= 50000) {
                $withdrawalRate = 0.015;
            } else {
                $withdrawalRate = 0.025;
            }
        }

        $withdrawalFee = $withdrawalAmount * $withdrawalRate;
        $input['user_id'] = $user->id;
        $input['type'] = 'withdrawal';
        $input['fee'] = $withdrawalFee;
        $input['balance'] = $user->balance - $withdrawalAmount - $withdrawalFee;

        DB::beginTransaction();

        try {
            $transaction = $this->transaction->create($input);
            $user->update(['balance' => $input['balance']]);
            DB::commit();
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Transaction failed'], 500);

        }
    }
}
