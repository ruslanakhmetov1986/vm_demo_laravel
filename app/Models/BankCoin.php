<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankCoin extends Model
{
    use HasFactory;

    protected $primaryKey = 'name';
}
