<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Brands extends Model
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

        if (empty($model->brand_code)) {
            // Jalankan query dalam transaction dan lock
            $lastCode = self::whereNotNull('brand_code')
                ->lockForUpdate()
                ->orderBy('brand_code', 'desc')
                ->value('brand_code');

            if ($lastCode) {
                // Ambil angka dari BR00001 -> 00001
                $number = (int) substr($lastCode, 2);
                $nextNumber = $number + 1;
                $nextCode = 'BR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $nextCode = 'BR00001';
            }

            $model->brand_code = $nextCode;
        }
    });
}

    protected $table = 'brands_tables'; 
    protected $casts = [
        'created_at' => 'datetime:Y-m-d', // Format default MySQL
       
    ];
    protected $fillable = [
        'brand_code',
        'brand_name',
        'description',
    ];
 

   
}
