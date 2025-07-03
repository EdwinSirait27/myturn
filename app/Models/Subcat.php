<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Subcat extends Model
{
   use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
 
    parent::boot();

    static::creating(function ($model) {
        \DB::transaction(function () use ($model) {
            // Generate UUID jika belum ada
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
            }
            // Ambil relasi kategori
            $categories = $model->categories; // sesuaikan nama relasi

            if ($categories && isset($categories->code)) {
                // Ambil 4 digit dari kode kategori
                $categoryCode = str_pad($categories->code, 4, '0', STR_PAD_LEFT);

                // Ambil kode terakhir dari model ini yang terkait kategori tsb (lock untuk hindari race condition)
                $lastCode = self::whereHas('categories', function ($query) use ($categories) {
                    $query->where('id', $categories->id);
                })->where('code', 'like', $categoryCode . '%')
                  ->lockForUpdate()
                  ->orderBy('code', 'desc')
                  ->value('code');

                // Ambil 2 digit terakhir dari kode setelah 4 digit kategori
                $lastNumber = $lastCode ? (int)substr($lastCode, -2) : 0;

                // Generate nomor baru 2 digit
                $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

                // Gabungkan: 4 digit kategori + 2 digit urutan
                $model->code = $categoryCode . $newNumber;
            } else {
                throw new \Exception('Kategori tidak ditemukan atau tidak memiliki kode.');
            }
        });
    });


    }
    protected $table = 'subcategories'; 
    protected $fillable = [
        'categories_id',
        'name',
        'code',
    ];
     public function categories()
    {
        return $this->belongsTo(Cat::class, 'categories_id', 'id');
    }
}
