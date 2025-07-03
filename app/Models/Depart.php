<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;


class Depart extends Model
{
   use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
       parent::boot();

 static::creating(function ($model) {
    if (!$model->getKey()) {
        $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
    }

    if (empty($model->code)) {
        // Jalankan query dalam transaction dan lock
        $lastCode = self::whereNotNull('code')
            ->lockForUpdate() // <- Cegah race condition!
            ->orderBy('code', 'desc')
            ->value('code');

        if ($lastCode) {
            $nextCode = str_pad((int) $lastCode + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $nextCode = '01';
        }

        $model->code = $nextCode;
    }
});
        
    }
    protected $table = 'departments'; 
    protected $fillable = [
        'name',
        'code',
    ];
}

