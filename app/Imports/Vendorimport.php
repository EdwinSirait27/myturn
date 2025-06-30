<?php

namespace App\Imports;

use App\Models\Country;
use App\Models\Vendor;
use App\Models\Banks;
use App\Models\Vendorgroup;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;







use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

class Vendorimport implements ToModel, WithHeadingRow, WithChunkReading,WithValidation, SkipsOnFailure
{
    /**
     * Handle each row from the Excel file
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */

   use SkipsFailures, Importable;
     public function model(array $row)
    {
        $vendorgroupId = trim($row['vendor_group_id'] ?? '');
        if (empty($vendorgroupId) || !Vendorgroup::where('id', $vendorgroupId)->exists()) {
            Log::warning("Skipped vendor: invalid vendorgroup_id '$vendorgroupId'");
            return null;
        }

        $bankId = trim($row['bank_id'] ?? '');
        if (empty($bankId) || !Banks::where('id', $bankId)->exists()) {
            Log::warning("Skipped vendor: invalid bank_id '$bankId'");
            return null;
        }

        $countryId = trim($row['country_id'] ?? '');
        if (empty($countryId) || !Country::where('id', $countryId)->exists()) {
            Log::warning("Skipped vendor: invalid country_id '$countryId'");
            return null;
        }

        $createdAt = null;
        if (!empty($row['created_at'])) {
            try {
                $createdAt = is_numeric($row['created_at'])
                    ? Date::excelToDateTimeObject($row['created_at'])->format('Y-m-d H:i:s')
                    : Carbon::parse($row['created_at'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                Log::warning("Invalid created_at date: {$row['created_at']}");
            }
        }

        $name = strtoupper(trim($row['name'] ?? ''));
        if (empty($name)) {
            Log::warning("Skipped vendor: name kosong");
            return null;
        }

        $code = $row['code'] ?? $this->generateCode($vendorgroupId, $row['consignment'] ?? 'No');

        return new Vendor([
            'vendor_group_id' => $vendorgroupId,
            'bank_id' => $bankId,
            'country_id' => $countryId,
            'type' => $row['type'] ?? null,
            'code' => $code,
            'name' => $name,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'email' => $row['email'] ?? null,
            'phonenumber' => $row['phonenumber'] ?? null,
            // 'consignment' => $row['consignment'] ?? null,
            // 'vendorfee' => $row['vendorfee'] ?? null,
            'consignment' => $row['consignment'] ?? null,
'vendorfee' => $row['vendorfee'] ?? null,

            'vendorpkp' => $row['vendorpkp'] ?? null,
            'salesname' => $row['salesname'] ?? null,
            'salescp' => $row['salescp'] ?? null,
            'npwpname' => $row['npwpname'] ?? null,
            'npwpnumber' => $row['npwpnumber'] ?? null,
            'npwpaddress' => $row['npwpaddress'] ?? null,
            'description' => $row['description'] ?? null,
            'created_at' => $createdAt,
        ]);
    }

    public function rules(): array
    {
          return [
            '*.name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $upperName = strtoupper(trim($value));
                    $exists = Vendor::whereRaw('UPPER(name) = ?', [$upperName])->exists();
                    if ($exists) {
                        $fail("Vendor Name \"$upperName\" already exists.");
                    }
                }
            ],
        ];
    }

    private function generateCode($vendorGroupId, $consignment)
    {
        $vendorgroup = Vendorgroup::findOrFail($vendorGroupId);
        $groupCode = str_pad($vendorgroup->code, 4, '0', STR_PAD_LEFT);

        $prefix = strtolower(trim($consignment)) === 'yes' ? 'C' : 'D';
        $codePrefix = $prefix . $groupCode;

        $lastCode = Vendor::where('code', 'like', $codePrefix . '%')
            ->orderByDesc('code')
            ->value('code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, strlen($codePrefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $runningNumber = str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        return $codePrefix . $runningNumber;
    }

    public function chunkSize(): int
    {
        return 500;
    }
    /**
 * Validasi nilai consignment hanya Yes atau No
 */
private function validateConsignment($value)
{
    $value = ucfirst(strtolower(trim($value)));

    if (!in_array($value, ['Yes', 'No'])) {
        // Default ke No jika tidak valid
        Log::warning("Consignment value Invalid: '$value', diset menjadi 'No'");
        return 'No';
    }

    return $value;
}

/**
 * Isi vendor fee default 1.5 jika null atau tidak numerik
 */
private function sanitizeVendorFee($value)
{
    if (!is_numeric($value)) {
        return 1.5;
    }

    return (float) $value;
}

}