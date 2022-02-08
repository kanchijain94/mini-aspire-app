<?php

namespace App\Interfaces;

interface LoanRepositoryInterface
{
    public function newLoanRequest($request);
    public function approveLoan($request);
}
