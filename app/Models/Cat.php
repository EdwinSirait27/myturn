<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Cat extends Model
{
     use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function boot()
    {
//         parent::boot();

//        static::creating(function ($model) {
//     if (!$model->getKey()) {
//         $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
//     }

//     $department = Depart::find($model->departments_id);
//     if ($department && $department->code) {
//         $prefix = $department->code;

//         // Kunci baris yang berkaitan selama dalam transaksi
//         $lastCode = self::where('code', 'like', $prefix . '%')
//             ->lockForUpdate() // <-- ini mencegah race condition
//             ->orderBy('code', 'desc')
//             ->value('code');

//         $lastNumber = $lastCode ? (int)substr($lastCode, 2, 2) : 0;
//         $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
//         $model->code = $prefix . $newNumber;
//     }
// });
//     }
 parent::boot();

    static::creating(function ($model) {
        // Jalankan dalam transaksi agar lockForUpdate efektif
        \DB::transaction(function () use ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
            }

            $department = Depart::find($model->departments_id);
            if ($department && $department->code) {
                $prefix = $department->code;

                // Lock baris terakhir berdasarkan prefix
                $lastCode = self::where('code', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('code', 'desc')
                    ->value('code');

                // Ambil 2 digit terakhir dari kode (setelah prefix)
                $lastNumber = $lastCode ? (int)substr($lastCode, strlen($prefix), 2) : 0;

                // Tambahkan +1 dan format jadi 2 digit
                $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

                // Set code dengan prefix + nomor urut
                $model->code = $prefix . $newNumber;
            } else {
                throw new \Exception('Departemen tidak ditemukan atau tidak memiliki kode.');
            }
        });
    });
}

    protected $table = 'categories'; 
    protected $fillable = [
        'departments_id',
        'name',
        'code',
    ];
     public function departments()
    {
        return $this->belongsTo(Depart::class, 'departments_id', 'id');
    }
}
