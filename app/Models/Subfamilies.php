<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Subfamilies extends Model
{
  use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
 protected static function boot()
    {
 
 
    parent::boot();

    static::creating(function ($model) {
        \DB::transaction(function () use ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Uuid::uuid7()->toString();
            }

            $families = \App\Models\Family::find($model->families_id);
            if ($families && $families->code) {
                // Pastikan prefix 6 digit
                $prefix = str_pad($families->code, 8, '0', STR_PAD_LEFT); // contoh: "000123"

                // Lock baris terakhir berdasarkan prefix
                $lastCode = self::where('code', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('code', 'desc')
                    ->value('code');

                // Ambil 2 digit terakhir dari kode (setelah prefix)
                $lastNumber = $lastCode ? (int)substr($lastCode, strlen($prefix), 2) : 0;

                // Tambah +1 dan format jadi 2 digit
                $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
                // Gabungkan jadi 8 digit kode
                $model->code = $prefix . $newNumber;
            } else {
                throw new \Exception('families not found or doesnt have a code.');
            }
        });
    });
}

    protected $table = 'subfamily'; 
    protected $fillable = [
        'families_id',
        'name',
        'code',
    ];
     public function families()
    {
        return $this->belongsTo(Family::class, 'families_id', 'id');
    }
}
