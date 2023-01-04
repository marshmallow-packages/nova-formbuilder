<?php

use Marshmallow\NovaFormbuilder\Models\Question;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nova_formbuilder_question_answer_options', function (Blueprint $table) {
            $table->id();
            $table->integer('order_column');
            $table->foreignIdFor(Question::class);
            $table->string('key');
            $table->string('value');
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
        Schema::dropIfExists('nova_formbuilder_question_answer_options');
    }
};
