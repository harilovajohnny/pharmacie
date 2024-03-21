<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison_information extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'value_id'
    ];
}
