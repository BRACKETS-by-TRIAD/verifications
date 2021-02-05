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
            $table->unsignedBigInteger('verifiable_id');
            $table->string('verifiable_type');
            $table->string('code');
            $table->string('action_name');                      // name of the action that the code verifies
            $table->dateTime('expires_at');                     // dateTime until the code should be submitted in verification form
            $table->dateTime('verifies_until')->nullable();     // dateTime until the verification is valid (used_at + config value)
            $table->dateTime('used_at')->nullable();            // dateTime when the code has been submitted by verification form
            $table->dateTime('last_touched_at')->nullable();
            $table->ipAddress('ip_address');
            $table->timestamps();

            $table->unique(['verifiable_id', 'verifiable_type', 'code', 'action_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_codes');
    }
}
