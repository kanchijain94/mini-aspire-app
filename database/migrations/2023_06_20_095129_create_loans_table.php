<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->double('principal_amount')->default(0);
            $table->double('interest')->default(0);
            $table->integer('term');
            $table->double('repay_amount')->default(0);
            $table->double('ewi')->default(0);
            $table->double('total_amount_paid')->default(0);
            $table->integer('term_paid')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=Pending, 2=Approved, 3=Rejected, 4=Paid');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
