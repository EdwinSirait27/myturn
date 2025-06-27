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
class Vendorimport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * Handle each row from the Excel file
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
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

        return new Vendor([
            'vendor_group_id' => $vendorgroupId,
            'bank_id' => $bankId,
            'country_id' => $countryId,
            'type' => $row['type'] ?? null,
            'code' => $row['code'] ?? null,
            'name' => $row['name'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'email' => $row['email'] ?? null,
            'phonenumber' => $row['phonenumber'] ?? null,
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

    /**
     * Set chunk size
     */
    public function chunkSize(): int
    {
        return 500; // bisa disesuaikan
    }
}
