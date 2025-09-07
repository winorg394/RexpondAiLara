<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('priority_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('condition');
            $table->json('keywords');
            $table->enum('action', ['set_priority', 'mark_as_span']);
            $table->enum('priority_type', ['high_priority', 'mid_priority', 'low_priority', 'span'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('priority_rules');
    }
};
