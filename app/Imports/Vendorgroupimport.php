<?php
namespace App\Imports;
use App\Models\Vendorgroup;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
class Vendorgroupimport implements ToModel, WithHeadingRow, WithChunkReading,WithValidation, SkipsOnFailure
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
        if (empty($name)) return null;

        $code = $row['code'] ?? $this->generateCode();

        return new Vendorgroup([
            'name' => $name,
            'code' => $code,
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
                    $exists = Vendorgroup::whereRaw('UPPER(name) = ?', [$upperName])->exists();
                    if ($exists) {
                        $fail("Vendor Group Name \"$upperName\" already exists.");
                    }
                }
            ],
        ];
}
/**
 * Generate kode otomatis (contoh: 0006 → 0007)
 */
private function generateCode()
{
    $lastCode = Vendorgroup::orderBy('code', 'desc')->value('code');
    if (!$lastCode) {
        return '0001'; // nilai awal jika belum ada data
    }
    $number = (int) $lastCode; // konversi string '0006' ke integer 6
    $newNumber = $number + 1;
    return str_pad($newNumber, 4, '0', STR_PAD_LEFT); // hasil: 0007
}
    /**
     * Set chunk size
     */
    public function chunkSize(): int
    {
        return 500; // bisa disesuaikan
    }
}