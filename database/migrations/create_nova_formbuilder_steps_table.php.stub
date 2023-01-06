<?php

use Marshmallow\NovaFormbuilder\Models\Form;
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
        Schema::create('nova_formbuilder_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Form::class);
            $table->string('name');
            $table->string('title')->nullable()->default(null);
            $table->text('subtitle')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->integer('step_number')->nullable()->default(null);
            $table->string('info')->nullable()->default(null);
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
        Schema::dropIfExists('nova_formbuilder_steps');
    }
};
