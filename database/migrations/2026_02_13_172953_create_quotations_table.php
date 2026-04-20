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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            // Sender Details (Snapshot)
            $table->string('sender_name');
            $table->text('sender_address')->nullable();
            $table->string('sender_website')->nullable();
            $table->string('sender_logo')->nullable();

            // Client Details
            $table->string('client_name');
            $table->text('client_address')->nullable();

            $table->string('quotation_number')->unique();
            $table->date('quotation_date');
            $table->date('expiry_date')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_rate', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->string('status')->default('Pending'); // Pending, Accepted, Rejected, Invoiced
            $table->text('bank_details')->nullable();
            $table->string('payment_qr_link')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
