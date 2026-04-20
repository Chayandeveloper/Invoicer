<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable()->after('id');
            $table->string('payment_method')->nullable()->after('expense_date');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('amount');
            $table->string('reference_number')->nullable()->after('category');
            $table->string('status')->default('Paid')->after('receipt_path');

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn(['business_id', 'payment_method', 'tax_amount', 'reference_number', 'status']);
        });
    }
};
