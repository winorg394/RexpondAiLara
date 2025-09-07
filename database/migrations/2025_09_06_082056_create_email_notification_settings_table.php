<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('high_priority_email_alerts')->default(true);
            $table->boolean('all_new_emails')->default(true);
            $table->boolean('desktop_badges')->default(true);
            $table->boolean('enable_quiet_time')->default(false);
            $table->time('quiet_time_start')->nullable();
            $table->time('quiet_time_end')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_notification_settings');
    }
};
