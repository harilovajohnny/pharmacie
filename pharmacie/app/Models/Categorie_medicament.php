<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie_medicament extends Model
{
    use HasFactory;

    protected $fillable = ['name','photo','descriptions','status'];


}
