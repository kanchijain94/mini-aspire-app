<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoanService{


    //Calculate rate of interest (BTW we can check from stored data)
    public function calculateInterest($principal_amount)
    {
        if($principal_amount >= 10000 && $principal_amount < 50000){
            return 4;
        }

        if($principal_amount >= 50000 && $principal_amount < 100000){
            return 3;
        }

        if($principal_amount >= 100000){
            return 2;
        }
    }

    //Calculate amount
    public function calculateAmount($principal_amount, $term, $interestRate)
    {
        $applicationFee = 500;
        $interestPerWeek = ($principal_amount/100) * $interestRate;
        $totalInterest = $interestPerWeek * $term;
        Log::info('totalInterest', [$totalInterest]);
        Log::info('interestPerWeek', [$interestPerWeek]);
        return $totalInterest + $applicationFee + (int)$principal_amount;
    }
}
