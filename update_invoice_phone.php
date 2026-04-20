<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = \App\Models\Invoice::find(6);
if ($invoice) {
    echo "Updating Invoice #" . $invoice->id . "...\n";

    // Update Client Phone
    if (empty($invoice->client_phone)) {
        $client = \App\Models\Client::where('name', $invoice->client_name)->first();
        if ($client && !empty($client->phone)) {
            $invoice->client_phone = $client->phone;
            echo "Set Client Phone to: " . $client->phone . "\n";
        } else {
            echo "Client phone not found in master record.\n";
        }
    }

    // Update Sender Phone
    if (empty($invoice->sender_phone)) {
        $business = \App\Models\Business::where('name', $invoice->sender_name)->first();
        if ($business && !empty($business->phone)) {
            $invoice->sender_phone = $business->phone;
            echo "Set Sender Phone to: " . $business->phone . "\n";
        } else {
            echo "Business phone not found in master record.\n";
        }
    }

    $invoice->save();
    echo "Invoice updated successfully.\n";
} else {
    echo "Invoice 6 not found.\n";
}
