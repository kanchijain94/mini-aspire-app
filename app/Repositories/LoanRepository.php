<?php

namespace App\Repositories;

use App\Http\Resources\Loan\WeeklyRepayResource;
use App\Interfaces\LoanRepositoryInterface;
use App\Models\Loan;
use App\Models\Outstanding;
use App\Models\User;
use App\Models\WeeklyRepay;
use App\Services\LoanService;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LoanRepository implements LoanRepositoryInterface
{
    use ApiResponser;
    public function newLoanRequest($request)
    {
        try{
            $user = User::find(auth()->user()->id);
            $principal_amount = $request->input('principal_amount');
            $interestRate = (new LoanService())->calculateInterest($principal_amount);
            $term = $request->input('term');
            $repay_amount = (new LoanService())->calculateAmount($principal_amount, $term, $interestRate);
            $ewi = $repay_amount / $term;

            if(!$user->is_admin){
                $loan = Loan::create([
                    'user_id' => $user->id,
                    'principal_amount' => $principal_amount,
                    'interest' => $interestRate,
                    'term' => $term,
                    'repay_amount' => $repay_amount,
                    'ewi' => $ewi
                ]);
                return $this->successResponse($loan,'Loan Request made successfully', Response::HTTP_CREATED);
            }
            return $this->errorResponse('Access forbidden', Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function approveLoan($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if($user->is_admin){
                $loan = Loan::where('id', $request->input('loan_id'))->update([
                    'status' => 2
                ]);
                return $this->successResponse($loan,'Loan Approved successfully', Response::HTTP_CREATED);
            }else{
                return $this->errorResponse('Unauthorized user', Response::HTTP_UNAUTHORIZED);
            }
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function weeklyRepay($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $loan = Loan::where('id', $request->input('loan_id'))->where('user_id', $user->id)->first();
            if(!$loan){
                return $this->errorResponse('You don\'t have loan.', Response::HTTP_BAD_REQUEST);
            } 
            if($loan->status == 1) { //pending
                return $this->errorResponse('This loan is yet to be approved.', Response::HTTP_BAD_REQUEST);
            }
            if($loan->status == 3) { //rejected
                return $this->errorResponse('This loan has been rejected.', Response::HTTP_BAD_REQUEST);
            }
            if($loan->status == 4) { //paid
                return $this->errorResponse('This loan is already repaid.', Response::HTTP_BAD_REQUEST);
            }
            
            $payable_amount = $request->input('payable_amount');
            $paid_by = $user->id;
            $loan_id = $request->input('loan_id');

            $weeklyRepays = new WeeklyRepay();
            $weeklyRepays->loan_id = $loan_id;
            $weeklyRepays->payable_amount = $payable_amount;
            $weeklyRepays->paid_by = $paid_by;
            
            if($payable_amount < $loan->ewi && (($loan->repay_amount - $loan->total_amount_paid) > $payable_amount)){
                if(($loan->term - $loan->term_paid) == 1){
                    return $this->errorResponse('Remaining amount to be paid is : $'.($loan->repay_amount - $loan->total_amount_paid), Response::HTTP_BAD_REQUEST);
                }
                return $this->errorResponse('The scheduled amount to be paid is : $'.$loan->ewi, Response::HTTP_BAD_REQUEST);
            }
            
            if(($loan->repay_amount - $loan->total_amount_paid) < $payable_amount) {
                return $this->errorResponse('Remaining payment is only: $' . ($loan->repay_amount - $loan->total_amount_paid) . '. Please try again with exact balance amount to close your loan.', Response::HTTP_BAD_REQUEST);
            }
            
            $repayment = $weeklyRepays->save();
            if(($loan->repay_amount - $loan->total_amount_paid) == $payable_amount) {
                Loan::where('id', $loan->id)->update(['term_paid' => $loan->term_paid + 1, 'total_amount_paid' => $loan->total_amount_paid + $payable_amount, 'status' => 4]);
                return $this->successResponse($repayment,'Weekly amount and loan has been marked paid successfully', Response::HTTP_CREATED);
            } else {
                Loan::where('id', $loan->id)->update(['term_paid' => $loan->term_paid + 1, 'total_amount_paid' => $loan->total_amount_paid + $payable_amount]);
                return $this->successResponse($repayment,'Weekly amount paid successfully', Response::HTTP_CREATED);
            }
           
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
    public function showLoans($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if (auth()->user()->is_admin) {
                $loans = Loan::all();
            } else {
                $loans = Loan::where('user_id', $user->id)->get();
            }

            return response()->json(['loans' => $loans]);
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
