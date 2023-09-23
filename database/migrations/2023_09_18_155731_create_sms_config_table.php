<?php

use App\Library\SMS\SmsProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_configs', function (Blueprint $table) {
            $table->id();
            $table->string("provider", 50)->unique();
            //either username and password is present or api_key or both of the them
            $table->string("username")->nullable();
            $table->string("password")->nullable();
            $table->string("api_key")->nullable();
            $table->string("sender_number")->nullable();
            $table->boolean("active")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_config');
    }
};
