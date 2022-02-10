<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoanService{


    //Calculate rate of interest (BTW we can check from stored data)
    public function calculateInterest($principle)
    {
        if($principle >= 10000 && $principle < 50000){
            return 4;
        }

        if($principle >= 50000 && $principle < 100000){
            return 3;
        }

        if($principle >= 100000){
            return 2;
        }
    }

    //Calculate amount
    public function calculateAmount($principle, $weeksToRepay, $interestRate)
    {
        $applicationFee = 500;
        $interestPerWeek = ($principle/100) * $interestRate;
        $totalInterest = $interestPerWeek * $weeksToRepay;
        Log::info('totalInterest', [$totalInterest]);
        Log::info('interestPerWeek', [$interestPerWeek]);
        return $totalInterest + $applicationFee + (int)$principle;
    }
}
