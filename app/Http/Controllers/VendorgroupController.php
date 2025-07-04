<?php

namespace App\Http\Controllers;

use App\Models\Vendorgroup;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use App\Imports\Vendorgroupimport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use App\Helpers\UuidHashHelper;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class VendorgroupController extends Controller
{
    public function logs(Request $request)
    {
        // dd(Activity::where('log_name', 'vendorgroup')->latest()->first());
        if ($request->ajax()) {
            $data = Activity::where('log_name', 'vendorgroup')
                ->with('causer')
                ->latest();
            return DataTables::of($data)
                ->addColumn('user', function ($row) {
                    return $row->causer?->Employee?->employee_name ?? 'System'; // pakai employee_name sesuai model kamu
                })
                ->addColumn('waktu', function ($row) {
                    return $row->created_at->format('d M Y H:i:s');
                })
                ->addColumn('detail', function ($row) {
                    if ($row->properties->has('attributes')) {
                        return collect($row->properties['attributes'])->map(function ($val, $key) {
                            return "<div><strong>" . ucfirst($key) . ":</strong> $val</div>";
                        })->implode('');
                    }
                    return '-';
                })
                ->rawColumns(['detail']) // supaya detail HTML tidak di-escape
                ->make(true);
        }
    }
    public function index()
    {

        return view('pages.Vendorgroup.Vendorgroup');
    }

    public function create()
    {
        return view('pages.Vendorgroup.create');
    }

    // public function getVendorgroups()
    // {
    //     $isBuyer = auth()->user()->hasRole('Buyer');

    //     $vendorgroups = Vendorgroup::select(['id', 'name', 'code', 'description'])
    //         ->get()
    //         ->map(function ($vendorgroup) use ($isBuyer) {
    //             $vendorgroup->id_hashed = substr(hash('sha256', $vendorgroup->id . env('APP_KEY')), 0, 8);
    //             $vendorName = optional($vendorgroup)->name;

    //             $vendorgroup->action = $isBuyer
    //                 ? '<a href="' . route('Vendorgroup.edit', $vendorgroup->id_hashed) . '" 
    //      class="btn btn-sm btn-primary mx-1" 
    //      data-bs-toggle="tooltip" 
    //      title="Edit vendor group: ' . e($vendorName) . '">
    //      Edit
    //    </a>' .

    //                 '<a href="' . route('Vendorgroup.detail', $vendorgroup->id_hashed) . '" 
    //      class="btn btn-sm btn-success mx-1" 
    //      data-bs-toggle="tooltip" 
    //      title="See Details Vendor Group: ' . e($vendorName) . '">
    //      Details
    //    </a>'
    //                 : '';


    //             return $vendorgroup;
    //         });
    //     return DataTables::of($vendorgroups)
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }



  public function getVendorgroups()
{
  

    $query = Vendorgroup::query()->select(['id', 'name', 'code', 'description']);

   
    return datatables()->eloquent($query)
        ->addColumn('action', function ($vendorGroup) {
            $hashed = UuidHashHelper::encodeUuid($vendorGroup->id);
            $route = route('Vendorgroup.edit', $hashed); 
            $route2 = route('Vendorgroup.detail', $hashed);
            $vendorName = e($vendorGroup->name);

            return <<<HTML
<a href="{$route}" class="btn btn-sm btn-primary mx-1" 
   data-bs-toggle="tooltip" 
   title="Edit vendor group: {$vendorName}">
   Edit
</a>
<a href="{$route2}" class="btn btn-sm btn-success mx-1" 
   data-bs-toggle="tooltip" 
   title="Edit vendor group: {$vendorName}">
   Detail
</a>
HTML;
        })
        ->rawColumns(['action'])
        ->make(true);
}



    // public function edit($hashedId)
    // {
    //     $vendorgroup = Vendorgroup::get()->first(function ($u) use ($hashedId) {
    //         $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
    //         return $expectedHash === $hashedId;
    //     });
    //     if (!$vendorgroup) {
    //         abort(404, 'Vendor group not found.');
    //     }
    //     return view('pages.Vendorgroup.edit', [
    //         'vendorgroup' => $vendorgroup,
    //         'hashedId' => $hashedId,
    //     ]);
    // }
    public function edit($hashedId)
{
    $uuid = UuidHashHelper::decodeUuid($hashedId);
    if (!$uuid) {
        abort(404, 'Invalid ID.');
    }
    // Cari langsung di DB tanpa load semua data
    $vendorgroup = Vendorgroup::findOrFail($uuid);

    return view('pages.Vendorgroup.edit', [
        'vendorgroup' => $vendorgroup,
        'hashedId' => $hashedId,
    ]);
}
    public function update(Request $request, $hashedId)
    {
        // $vendorgroup = Vendorgroup::get()->first(function ($u) use ($hashedId) {
        //     $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
        //     return $expectedHash === $hashedId;
        // });
    $uuid = UuidHashHelper::decodeUuid($hashedId);

          $vendorgroup = Vendorgroup::findOrFail($uuid);
        if (!$vendorgroup) {
            return redirect()->route('pages.Vendorgroup')->with('error', 'ID tidak valid.');
        }
        $validatedData = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendorgroups', 'name')->ignore($vendorgroup->id),
                new NoXSSInput()
            ],
            'description' => ['nullable']
        ], [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be must be a sentence.',


        ]);

        DB::beginTransaction();
        $vendorgroup->update([
            'name' => $validatedData['name'] ?? '',
            'description' => $validatedData['description'] ?? '',
        ]);
        DB::commit();
        return redirect()->route('pages.Vendorgroup')->with('success', 'Vendorgroup updated successfully.');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                new NoXSSInput(),
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value))); // hilangkan spasi ganda
                    $exists = VendorGroup::all()
                        ->map(function ($item) {
                        return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                    })
                        ->contains($normalized);

                    if ($exists) {
                        $fail('The ' . $attribute . ' is too similar to an existing name.');
                    }
                }
            ],
           
            'description' => ['nullable', 'string', 'max:255', new NoXSSInput()],
        ]);

        try {
            DB::beginTransaction();

            // Ambil kode terakhir, cast ke angka, lalu naikkan 1
            $lastCode = Vendorgroup::lockForUpdate()
                ->max(DB::raw('CAST(code AS UNSIGNED)'));

            $nextNumber = ($lastCode ?? 0) + 1;
            $nextCode = str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // Jadi 00001, 00002, dll

            Vendorgroup::create([
                'name' => strtoupper($validated['name']),

                'code' => $nextCode,
                'description' => $validated['description'] ?? '',
            ]);

            DB::commit();

            return redirect()->route('pages.Vendorgroup')
                ->with('success', "Vendorgroup created successfully with code: $nextCode");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'failed to save data: ' . $e->getMessage()])
                ->withInput();
        }

    }
    public function indeximportvendorgroup()
    {
        $files = Storage::disk('public')->files('template vendor group');
        return view('pages.Importvendorgroup.Importvendorgroup', compact('files'));
    }
    public function downloadvendorgroup($filename)
    {
        $path = 'template vendor group/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        abort(404);
    }
    //  public function importvendorgroup(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|mimes:xlsx,csv,xls'
//         ]);

    //         Excel::import(new Vendorgroupimport, $request->file('file'));

    //         return back()->with('success', 'Import vendor group success!');
//     }

    //     public function importvendorgroup(Request $request)
// {
//     // // Validasi bahwa file ada dan berformat Excel
//     // $request->validate([
//     //     'file' => 'required|mimes:xlsx,csv,xls'
//     // ]);

    //     // try {
//     //     // Jalankan proses import (bisa throw ValidationException dari model())
//     //     Excel::import(new VendorgroupImport, $request->file('file'));

    //     //     // Jika sukses
//     //     return back()->with('success', 'Import success');

    //     // } catch (\Illuminate\Validation\ValidationException $e) {
//     //     // Jika ada kesalahan validasi (dari model()), tampilkan ke view
//     //     return back()->withErrors($e->errors());

    //     // } catch (\Exception $e) {
//     //     // Tangani error umum lainnya (misalnya format Excel rusak)
//     //     return back()->withErrors(['error' => $e->getMessage()]);
//     // }
//     $request->validate([
//         'file' => 'required|mimes:xlsx,csv,xls'
//     ]);

    //     $import = new VendorgroupImport();

    //     Excel::import($import, $request->file('file'));

    //     // Jika ada kesalahan validasi
//     if ($import->failures()->isNotEmpty()) {
//         return back()->withFailures($import->failures());
//     }

    //     return back()->with('success', 'Import success');
// }
//  public function importvendorgroup(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|mimes:xlsx,csv,xls'
//         ]);

    //         Excel::import(new Vendorgroupimport, $request->file('file'));

    //         return back()->with('success', 'import group vendor success!');
//     }
    public function importvendorgroup(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        $import = new Vendorgroupimport;
        $import->import($request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->with('failures', $import->failures());
        }

        return back()->with('success', 'Import group vendor success!');
    }


}
