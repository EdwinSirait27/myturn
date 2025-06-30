<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Subfamily extends Model
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
