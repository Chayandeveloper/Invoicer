<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = \App\Models\Invoice::find(6);
if ($invoice) {
    $data = [
        'invoice_id' => $invoice->id,
        'invoice_client_phone' => $invoice->client_phone,
        'invoice_sender_phone' => $invoice->sender_phone,
        'client_name' => $invoice->client_name,
        'master_client' => null
    ];

    $client = \App\Models\Client::where('name', $invoice->client_name)->first();
    if ($client) {
        $data['master_client'] = [
            'id' => $client->id,
            'phone' => $client->phone,
            'email' => $client->email
        ];
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    echo json_encode(['error' => 'Invoice not found']);
}
