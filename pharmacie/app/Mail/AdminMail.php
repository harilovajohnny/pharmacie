<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $fichier_ordonnance;
    public $data_cart;


    /**
     * Create a new message instance.
     */
    public function __construct($data,$fichier_ordonnances,$data_carts)
    {
      
        $this->data = $data;
        $this->fichier_ordonnance = $fichier_ordonnances;
        $this->data_cart = $data_carts;
        // parent::__construct($data);
    }

    public function build()
    {
        $mime =  mime_content_type(storage_path('app/' . $this->fichier_ordonnance));
        $filePath = storage_path('app/' . $this->fichier_ordonnance);
        $pathInfo = pathinfo($filePath);
        return $this->subject('Email for Admin')
                    ->view('front.email.admin-mail')
                    ->attach($filePath, [
                        'as' => 'prescription.'. $pathInfo['extension'],
                        'mime' => $mime,
                    ]);
    }

}
