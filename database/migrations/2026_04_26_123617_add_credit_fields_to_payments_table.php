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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'use_credit')) {
                $table->boolean('use_credit')->default(false)->after('business_id');
            }
            if (!Schema::hasColumn('payments', 'credit_amount')) {
                $table->decimal('credit_amount', 15, 2)->default(0)->after('use_credit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['use_credit', 'credit_amount']);
        });
    }
};
