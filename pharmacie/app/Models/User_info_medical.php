<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_info_medical extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'current_medical_condition',
        'drug_allergie',
        'photo_prescription',
        'order_id'
    ];

}
