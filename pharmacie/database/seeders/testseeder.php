<?php

namespace Database\Seeders;

use App\Mail\AdminMail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class testseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['name' => 'John Doe'];
        Mail::to('ambalafenofikambanana@gmail.com')->send(new AdminMail($data));
        dd('ok');
        // if (Mail::failures()) {
        //     // Le message a échoué à être envoyé
        //     $this->command->info('E-mail sending failed!');
        // } else {
        //     // Le message a été envoyé avec succès
        //     $this->command->info('E-mail sent successfully!');
        // }
    }
}
