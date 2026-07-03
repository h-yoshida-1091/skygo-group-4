<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_characters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('character_type', 50); // 熊、猫など
            $table->string('nickname', 50)->nullable(); // ニックネーム

            $table->unsignedInteger('level')->default(1); // レベル
            $table->unsignedInteger('exp')->default(0); // 経験値

            $table->string('title', 50)->default('新人ワーカー'); // 称号

            $table->unsignedInteger('total_work_time')->default(0); // 総勤務時間（分）
            $table->unsignedInteger('login_count')->default(0); // 出勤回数

            $table->string('clock_in_voice', 255)->nullable(); // 出勤ボイス
            $table->string('clock_out_voice', 255)->nullable(); // 退勤ボイス
            $table->string('image', 255)->nullable(); // キャラ画像

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_characters');
    }
};