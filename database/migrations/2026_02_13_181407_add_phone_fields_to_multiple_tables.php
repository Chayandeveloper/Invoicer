<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('sender_phone')->nullable()->after('sender_website');
            $table->string('client_phone')->nullable()->after('client_address');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('sender_phone')->nullable()->after('sender_website');
            $table->string('client_phone')->nullable()->after('client_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['sender_phone', 'client_phone']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['sender_phone', 'client_phone']);
        });
    }
};
