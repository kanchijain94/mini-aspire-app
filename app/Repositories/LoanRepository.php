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
            $principle = $request->input('principle');
            $interestRate = (new LoanService())->calculateInterest($principle);
            $weeksToRepay = $request->input('weeksToRepay');
            $repayAmount = (new LoanService())->calculateAmount($principle, $weeksToRepay, $interestRate);
            $ewi = $repayAmount / $weeksToRepay;

            if(!$user->is_admin){
                $loan = Loan::create([
                    'user_id' => $user->id,
                    'principle' => $principle,
                    'interest' => $interestRate,
                    'weeksToRepay' => $weeksToRepay,
                    'repayAmount' => $repayAmount,
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
            $loan = Loan::where('id', $request->input('loan_id'))->where('user_id', $user->id)->where('status', 2)->first();
            if(!$loan){
                return $this->errorResponse('You don\'t have loan.', Response::HTTP_BAD_REQUEST);
            }
            $payable_amount = $request->input('payable_amount');
            $paid_by = $user->id;
            $loan_id = $request->input('loan_id');

            $weeklyRepays = new WeeklyRepay();
            $weeklyRepays->loan_id = $loan_id;
            $weeklyRepays->payable_amount = $payable_amount;
            $weeklyRepays->paid_by = $paid_by;
            if($weeklyRepays->save()) return $this->successResponse(WeeklyRepayResource::make($weeklyRepays),'Weekly repayed successfully', Response::HTTP_CREATED);
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
