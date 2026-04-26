<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        $type = $this->invoice->invoice_type === 'proforma' ? 'Proforma Invoice' : 'Invoice';
        return new Envelope(
            subject: $type . ' #' . $this->invoice->invoice_number . ' from ' . $this->invoice->sender_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice]);

        $filename = ($this->invoice->invoice_type === 'proforma' ? 'Proforma-' : 'Invoice-') . $this->invoice->invoice_number . '.pdf';
        return [
            Attachment::fromData(fn() => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
