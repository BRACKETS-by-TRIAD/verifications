<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('verifiable_id');
            $table->string('verifiable_type');
            $table->string('code');
            $table->dateTime('expires_at');
//            $table->dateTime('verified_until')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'verifiable_id', 'verifiable_type', 'code']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('verification_codes');
    }
}
