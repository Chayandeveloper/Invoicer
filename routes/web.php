<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\SalesReceiptController;
use App\Http\Controllers\CreditNoteController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Google Auth
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Clerk Auth
Route::get('/auth/clerk', [LoginController::class, 'redirectToClerk'])->name('login.clerk');
Route::get('/auth/clerk/register', [LoginController::class, 'redirectToClerkSignup'])->name('register.clerk');
Route::get('/auth/clerk/sync', function() { return view('auth.sync'); })->name('auth.clerk.sync');
Route::post('/auth/clerk/handshake', [LoginController::class, 'handshake'])->name('auth.clerk.handshake');
Route::get('/auth/clerk/callback', [LoginController::class, 'handleClerkCallback']);

// Mobile OTP Auth
Route::post('/auth/otp/send', [LoginController::class, 'sendOtp'])->name('otp.send');
Route::post('/auth/otp/verify', [LoginController::class, 'verifyOtp'])->name('otp.verify');

Route::get('/', function () {
    return auth()->check() ? view('home') : redirect()->route('login');
})->middleware('auth');

// Production Setup Route (Run this once after uploading to cPanel)
Route::get('/setup-production', function () {
    try {
        $targetFolder = storage_path('app/public');
        $publicPath = public_path('storage');
        
        // Suppress warning and force create required directories that FTP usually drops
        @mkdir($targetFolder, 0755, true);
        @mkdir(storage_path('framework/views'), 0755, true);
        @mkdir(storage_path('framework/cache/data'), 0755, true);
        @mkdir(storage_path('framework/sessions'), 0755, true);

        \Illuminate\Support\Facades\Artisan::call('storage:link');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        return "Setup successful! <br><br>Linked: <br>From: {$targetFolder} <br>To: {$publicPath}";
    } catch (\Throwable $e) {
        $targetFolder = storage_path('app/public');
        $publicPath = public_path('storage');
        $targetExists = @file_exists($targetFolder) ? 'Yes' : 'No';
        
        return "Error caught: " . $e->getMessage() . " on line " . $e->getLine() . " of " . basename($e->getFile()) . 
            "<br><br><b>Path Debug Information:</b><br>" .
            "Target (Exists properly? {$targetExists}): {$targetFolder}<br>" .
            "Public Link Path: {$publicPath}<br>" .
            "Public path parent exists? " . (@file_exists(dirname($publicPath)) ? 'Yes' : 'No') . "<br><br>" .
            "Tip: If your public folder is named something else (like public_html), Laravel might be looking in the wrong place.";
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::resource('invoices', InvoiceController::class);

    // Proforma Invoices
    Route::post('/proforma-invoices/{id}/send-email', [ProformaInvoiceController::class, 'sendEmail'])->name('proforma_invoices.sendEmail');
    Route::post('/proforma-invoices/{id}/convert', [ProformaInvoiceController::class, 'convert'])->name('proforma_invoices.convert');
    Route::resource('proforma-invoices', ProformaInvoiceController::class)->names('proforma_invoices');

    Route::get('/quotations/{id}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::post('/quotations/{id}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
    Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.updateStatus');
    Route::resource('quotations', QuotationController::class);

    Route::get('/expenses/{id}/download', [ExpenseController::class, 'download'])->name('expenses.download');
    Route::resource('expenses', ExpenseController::class);

    Route::get('/payments/{id}/download', [PaymentController::class, 'download'])->name('payments.download');
    Route::resource('payments', PaymentController::class);

    Route::get('/sales-receipts/{id}/download', [SalesReceiptController::class, 'download'])->name('sales-receipts.download');
    Route::resource('sales-receipts', SalesReceiptController::class);

    Route::resource('businesses', BusinessController::class);
    
    // Credit Notes
    Route::get('/credit-notes/{id}/download', [CreditNoteController::class, 'download'])->name('credit-notes.download');
    Route::get('/credit-notes/suggestions', [CreditNoteController::class, 'getSuggestions'])->name('credit-notes.suggestions');
    Route::resource('credit-notes', CreditNoteController::class);

    Route::get('/sales/clients', [SalesController::class, 'clients'])->name('sales.clients');

    Route::patch('/clients/{id}/status', [ClientController::class, 'toggleStatus'])->name('clients.toggleStatus');
    Route::resource('clients', ClientController::class);


    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
