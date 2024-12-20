<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'attributable_id',
        'attributable_type'
    ];

    public function attributable()
    {
        return $this->morphTo();
    }
}
