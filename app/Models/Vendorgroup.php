<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class Vendorgroup extends Model
{
use HasFactory, LogsActivity;

    protected $table = 'vendorgroups';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('vendorgroup')
            ->logOnly(['name', 'code', 'description']) // kolom yang dicatat
            ->logOnlyDirty() // hanya saat ada perubahan
            ->dontSubmitEmptyLogs(); // skip kalau tidak ada perubahan
    }


    public function getDescriptionForEvent(string $eventName): string
    {
        return "Vendor Group {$eventName}";
    }
public function getIdHashedAttribute()
{
    return substr(hash('sha256', $this->id . env('APP_KEY')), 0, 8);
}
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'vendor_group_id');
    }
//    protected static function booted()
// {
//     Activity::created(function ($log) {
//         if ($log->log_name !== 'vendorgroup' || !$log->causer_id) {
//             return;
//         }

//         $userId = $log->causer_id;

//         $count = Activity::where('log_name', 'vendorgroup')
//             ->where('causer_id', $userId)
//             ->count();

//         if ($count > 5) {
//             $toDelete = $count - 5;

//             Activity::where('log_name', 'vendorgroup')
//                 ->where('causer_id', $userId)
//                 ->orderBy('created_at', 'asc')
//                 ->limit($toDelete)
//                 ->delete();
//         }
//     });
// }

}