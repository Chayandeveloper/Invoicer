<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('phone');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('client_logo')->nullable()->after('client_address');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('client_logo')->nullable()->after('client_address');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('client_logo');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('client_logo');
        });
    }
};
