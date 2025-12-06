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
        // Se esiste la colonna 'content', copia i dati in 'body' e poi elimina 'content'
        if (Schema::hasColumn('articles', 'content')) {
            // Copia i dati da content a body se body è vuoto
            \DB::statement('UPDATE articles SET body = content WHERE (body IS NULL OR body = "") AND content IS NOT NULL');
        }
        
        Schema::table('articles', function (Blueprint $table) {
            // Elimina la colonna content se esiste
            if (Schema::hasColumn('articles', 'content')) {
                $table->dropColumn('content');
            }
            
            // Assicurati che body esista
            if (!Schema::hasColumn('articles', 'body')) {
                $table->text('body')->after('excerpt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // In caso di rollback, ripristina content se necessario
            if (!Schema::hasColumn('articles', 'content') && Schema::hasColumn('articles', 'body')) {
                $table->text('content')->after('excerpt');
                \DB::statement('UPDATE articles SET content = body WHERE body IS NOT NULL');
            }
        });
    }
};
