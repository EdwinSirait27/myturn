<?php
namespace App\Imports;
use App\Models\Payrolls;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Crypt;

class PayrollsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $errors;
    public function __construct(&$errors)
    {
        $this->errors = &$errors;
    }
    public function model(array $row)
    
    {
    $createdat = null;
    if (!empty($row[18])) {
        if (is_numeric($row[18])) {
            $createdat = Date::excelToDateTimeObject($row[18])->format('Y-m-d H:i:s');
        } else {
            $createdat = Carbon::parse($row[18])->format('Y-m-d H:i:s');
        }
    }

    $monthyear = null;
    if (!empty($row[17])) {
        if (is_numeric($row[17])) {
            $monthyear = Date::excelToDateTimeObject($row[17])->format('Y-m-d');
        } else {
            $monthyear = Carbon::parse($row[17])->format('Y-m-d');
        }
    }

    if ($row[0] !== null && !Payrolls::where('employee_id', $row[0])->exists()) {
        
        $attendance          = isset($row[1]) ? $row[1] : 0;
        $daily_allowance     = isset($row[2]) ? (float) $row[2] : 0.0;
$house_allowance     = isset($row[3]) ? (float) $row[3] : 0.0;
$meal_allowance      = isset($row[4]) ? (float) $row[4] : 0.0;
$transport_allowance = isset($row[5]) ? (float) $row[5] : 0.0;
$bonus               = isset($row[6]) ? (float) $row[6] : 0.0;
$overtime            = isset($row[7]) ? (float) $row[7] : 0.0;

$late_fine           = isset($row[8]) ? (float) $row[8] : 0.0;
$punishment          = isset($row[9]) ? (float) $row[9] : 0.0;
$bpjs_kes            = isset($row[10]) ? (float) $row[10] : 0.0;
$bpjs_ket            = isset($row[11]) ? (float) $row[11] : 0.0;
$tax                 = isset($row[12]) ? (float) $row[12] : 0.0;
$debt                 = isset($row[13]) ? (float) $row[13] : 0.0;
$deductions = $late_fine + $punishment + ($bpjs_kes * 2)   
            + ($bpjs_ket * 2) + $tax + $debt;
$salary = ($attendance * $daily_allowance)
        + $house_allowance
        + $meal_allowance
        + $transport_allowance
        + $bonus
        + $overtime;
$take_home = $salary - $deductions;        
        return new Payrolls([
            'employee_id'         => $row[0],
            'attendance'          => $attendance,
            'daily_allowance'     => $daily_allowance,
            'house_allowance'     => $house_allowance,
            'meal_allowance'      => $meal_allowance,
            'transport_allowance' => $transport_allowance,
            'bonus'               => $bonus,
            'overtime'            => $overtime,
            'late_fine'           => $late_fine,
            'punishment'          => $punishment,
            'bpjs_kes'            => $bpjs_kes,
            'bpjs_ket'            => $bpjs_ket,
            'tax'                 => $tax,
            'debt'                 => $debt,
            'deductions'          => $deductions,
            'salary'              => $salary,
            'take_home'              => $take_home,
            'month_year'          => $monthyear ?? null,
            'created_at'          => $createdat ?? null,
            'period'              => $row[19] ?? null,
        ]);
    }

    return null;
}

}
