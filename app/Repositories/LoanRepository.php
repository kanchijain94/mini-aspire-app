<?php

namespace App\Repositories;

use App\Interfaces\LoanRepositoryInterface;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class LoanRepository implements LoanRepositoryInterface
{
    use ApiResponser;
    public function newLoanRequest($request)
    {
        try{
            //if user doesn't exist then return
            $user = User::find(auth()->user()->id);
            if(!$user){
                return $this->errorResponse('Invalid User', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
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
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function approveLoan($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if(!$user){
                return $this->errorResponse('Invalid User', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

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
}
