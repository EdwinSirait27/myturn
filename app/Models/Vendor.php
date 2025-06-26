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

// public static function generateCode($vendorGroupId, $consignment)
// {
//     $vendorgroup = Vendorgroup::findOrFail($vendorGroupId);
//     $groupCode = str_pad($vendorgroup->code, 3, '0', STR_PAD_LEFT); // contoh: 001 → 3 digit
//     $prefix = $consignment === 'Yes' ? 'C' : 'D';

//     // Ambil kode terakhir di seluruh Vendor (tanpa peduli C/D)
//     $lastCode = self::orderByDesc('code')->value('code'); // contoh: D0050012

//     if ($lastCode) {
//         $lastNumber = (int) substr($lastCode, -4); // ambil 4 digit paling belakang
//         $newNumber = $lastNumber + 1;
//     } else {
//         $newNumber = 1;
//     }

//     $runningNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

//     return $prefix . $groupCode . $runningNumber;
// }
public static function generateCode($vendorGroupId, $consignment)
{
    // Ambil kode grup (misalnya: '001')
    $vendorgroup = Vendorgroup::findOrFail($vendorGroupId);
    $groupCode = str_pad($vendorgroup->code, 4, '0', STR_PAD_LEFT); // pastikan 3 digit

    // Tentukan prefix
    $prefix = $consignment === 'Yes' ? 'C' : 'D';

    // Gabungkan prefix + kode grup → jadi base prefix
    $codePrefix = $prefix . $groupCode; // contoh: C001

    // Cari kode vendor terakhir berdasarkan prefix itu
    $lastCode = self::where('code', 'like', $codePrefix . '%')
        ->orderByDesc('code')
        ->value('code'); // misalnya C0010011

    if ($lastCode) {
        $lastNumber = (int) substr($lastCode, strlen($codePrefix)); // ambil 4 digit terakhir
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1; // awal jika belum ada
    }

    // Format nomor urut jadi 4 digit (0001, 0002, dst)
    $runningNumber = str_pad($newNumber, 2, '0', STR_PAD_LEFT);

    // Gabungkan semua bagian
    return $codePrefix . $runningNumber;
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