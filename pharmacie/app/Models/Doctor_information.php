<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_information extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'adresse',
        'zip_code',
        'country',
        'order_id',
    ];

}
