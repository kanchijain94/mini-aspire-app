<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'principal_amount', 'interest', 'term', 'repay_amount', 'ewi'];

    public function weeklyRepays()
    {
        $this->hasMany(WeeklyRepay::class);
    }
}
