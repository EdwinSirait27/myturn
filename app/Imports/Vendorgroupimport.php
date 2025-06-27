<?php

namespace App\Imports;


use App\Models\Vendor;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class Vendorgroupimport implements ToModel, WithHeadingRow, WithChunkReading
{
   /**
     * Handle each row from the Excel file
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
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
            'name' => $row['name'] ?? null,
            'code' => $row['code'] ?? null,
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
