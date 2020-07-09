<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Account;
use App\Transaction;
use App\Balance;
use Carbon\Carbon;

class TransactionController extends Controller
{
    //

    public function index(){

       $transactions =  Transaction::where('user_id',Auth::user()->id)->with('reciever')->get();

        return response()->json(compact('transactions'), 200);
    }


    public function store(Request $request){


        $date = time("i");

        $numbers = 'ABCDEFGHIJKLMNOPQRSTUWYZabcdefghijklmnopqrstuvwyzx' . $date;
        $refNumberWithTimeStamp =  substr(str_shuffle($numbers), 0, 15);
        $account = Account::where('user_id',Auth::user()->id)->firstorfail();
        $creditNumber = $request->input('transfer_to');
        $this->amount =  $request->input('amount');

        /***
         * search if the account number matches an account in the DB;
         *
         *
         */

        $checkIfAccountNumberExists = Account::where("account_number","LIKE","%{$creditNumber}%")->first();


        if( $checkIfAccountNumberExists){

            $this->accountNumber = Account::where('account_number', $creditNumber)->first();

        }else{

            return response()->json(['error' => 'The Account Number Does not Exist'],201);
        }

        /**
         * Check if the account exists in the db ;
         * if not throw error account number does not exists
         * check if the money the sender wants to tranfer is not greater that the amount he/she has in the account as balance
         * Users wont be able to send any amountb if the balance is equal to zeror
         */
    if ($account->balance >= $request->input('amount') && $account->balance != 0) {

    //   $Lasttranx = Transaction::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

    // if ($Lasttranx->reference != $refNumberWithTimeStamp) {

        $transaction = Transaction::create([
                        'reference' => $refNumberWithTimeStamp,
                        'user_id'   => Auth::user()->id,
                        'account_id' => $account->id,
                        'amount'    => $request->input('amount'),
                        'payment_summary' => $request->input('payment_summary'),
                        'transfer_to'     => $this->accountNumber->account_number,
                    ]);
        // }else{

        //     return response()->json(['error'=>'Your request Cannot be completed at this time'],201);

        // }


        //update account details of the sender
        $this->updateAccountDetailsOfTheSender($account);

        //update account details of the receiver
        $this->updateAccountDetailsOfTheReceiver();

        //update balance tabel of the reciever
        $this->updateBalanceRecordOfTheReciever();

        //update balance tabel of the sender
        $this->updateBalanceRecordOfTheSender($account);

        return response()->json(compact('transaction'), 201);

        } else {

            return response()->json(['error' => 'Insufficient Balanace'], 200);
        }


    }




    public function updateBalanceRecordOfTheSender($account){

        Balance::where('account_id', $account->id)->update([
            'balance' => $account->balance - $this->amount,
        ]);

    }


    public function updateBalanceRecordOfTheReciever(){


        Balance::where('account_id', $this->accountNumber->id)->update([
            'balance' => $this->accountNumber->balance + $this->amount,
        ]);

    }


    public function updateAccountDetailsOfTheReceiver(){

        Account::where('account_number', $this->accountNumber->account_number)->update([

            'balance'   =>  $this->accountNumber->balance + $this->amount,
            'credit'    =>  $this->accountNumber->credit + $this->amount,

         ]);
    }


    public function updateAccountDetailsOfTheSender($account){

        Account::where('user_id', Auth::user()->id)->update([
            'balance' => $account->balance - $this->amount,
            'debit'   =>  $account->debit + $this->amount,
            'credit'  => $account->credit - $this->amount,
        ]);
    }

}
