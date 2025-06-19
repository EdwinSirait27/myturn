<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Ramsey\Uuid\Uuid;



class Activity extends Model
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
    protected $table = 'activity_logs'; 

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_time',
        'device_lan_mac',
        'device_wifi_mac',
    ];
 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
