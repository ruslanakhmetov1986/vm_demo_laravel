<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';

    protected $fillable = ['key','val'];

    public function getValue(){
        return $this->val;
    }
}
