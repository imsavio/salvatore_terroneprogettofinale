<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_id', 10)->unique()->nullable()->after('id');
        });

        // Genera public_id per gli utenti esistenti
        \DB::table('users')->get()->each(function ($user) {
            \DB::table('users')
                ->where('id', $user->id)
                ->update(['public_id' => strtoupper(substr(md5($user->id . $user->email), 0, 8))]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};
