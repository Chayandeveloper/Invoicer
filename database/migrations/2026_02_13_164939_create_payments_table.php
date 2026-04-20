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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // Bank, Cash, UPI, Cheque, etc.
            $table->string('reference_number')->nullable();
            $table->string('client_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
