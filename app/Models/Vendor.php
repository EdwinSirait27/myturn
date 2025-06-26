<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class Vendor extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'vendor';
    protected $fillable = [
        'vendor_group_id',
        'country_id',
        'bank_id',
        'type',
        'code',
        'name',
        'address',
        'city',
        'email',
        'phonenumber',
        'consignment',
        
        'vendorpkp',
        'salesname',
        'salescp',
        'npwpname',
        'npwpaddress',
        'npwpnumber',
        'description',
    ];
    public function banks()
    {
        return $this->belongsTo(Banks::class, 'bank_id', 'id');
    }
    public function group()
    {
        return $this->belongsTo(Vendorgroup::class, 'vendor_group_id', 'id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function getIdHashedAttribute()
    {
        return substr(hash('sha256', $this->id . env('APP_KEY')), 0, 8);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('vendor')
            ->logOnly([
            'type',
            'code',
            'name',
            'address',
            'city',
            'country_id',
            'email',
            'phonenumber',
            'consignment',
            // 'store_id',
            'vendorpkp',
            'salesname',
            'salescp',
            'npwpname',
            'npwpnumber',
            'npwpaddress',
            'bank_id',
                // 'vendor_group_id',
                'description'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Vendor {$eventName}";
    }
}