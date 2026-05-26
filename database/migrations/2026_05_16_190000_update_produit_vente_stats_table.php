<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('produit_vente_stats', 'product_id')) {
            return;
        }

        Schema::table('produit_vente_stats', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->cascadeOnDelete();
            $table->string('saison', 50)->after('product_id');
            $table->unsignedInteger('rang')->nullable()->after('saison');
            $table->unsignedInteger('total_quantite_vendue')->default(0)->after('rang');
            $table->decimal('total_chiffre_affaires', 12, 2)->default(0)->after('total_quantite_vendue');
            $table->unsignedInteger('nombre_ventes')->default(0)->after('total_chiffre_affaires');
            $table->timestamp('derniere_analyse')->nullable()->after('nombre_ventes');
            $table->unique(['product_id', 'saison']);
            $table->index(['saison', 'rang']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('produit_vente_stats', 'product_id')) {
            return;
        }

        Schema::table('produit_vente_stats', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropUnique(['product_id', 'saison']);
            $table->dropIndex(['saison', 'rang']);
            $table->dropColumn([
                'product_id',
                'saison',
                'rang',
                'total_quantite_vendue',
                'total_chiffre_affaires',
                'nombre_ventes',
                'derniere_analyse',
            ]);
        });
    }
};
