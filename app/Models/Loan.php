<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'principle', 'interest', 'weeksToRepay', 'repayAmount', 'ewi'];

    public function weeklyRepays()
    {
        $this->hasMany(WeeklyRepay::class);
    }
}
