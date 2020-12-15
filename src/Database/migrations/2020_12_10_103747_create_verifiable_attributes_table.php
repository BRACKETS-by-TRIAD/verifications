<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifiableAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifiable_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verifiable_id');
            $table->string('verifiable_type');
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();

            $table->unique(['verifiable_id', 'verifiable_type', 'attribute_name']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifiable_attributes');
    }
}
