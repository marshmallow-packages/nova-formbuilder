<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nova_formbuilder_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('width')->default('full');
            $table->string('on_cancel')->nullable()->default(null);
            $table->string('on_submit')->nullable()->default(null);
            $table->string('submit_event')->nullable()->default(null);
            $table->json('layout')->nullable()->default(null);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nova_formbuilder_forms');
    }
};
