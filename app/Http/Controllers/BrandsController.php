<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brands::whereNotNull('brand_name')->pluck('brand_name', 'id');
        return view('pages.Brands.Brands',compact('brands'));
    }
    public function getBrands(Request $request)
{
    $query = Brands::select(['id', 'brand_code', 'brand_name','description'])
                ->orderBy('brand_code', 'asc'); // Added sorting by brand_code in ascending order

    if ($request->filled('brand_name')) {
        $query->where('brand_name', $request->brand_name);
    }

    $brand = $query->get()
        ->map(function ($brand) {
            $brand->id_hashed = substr(hash('sha256', $brand->id . env('APP_KEY')), 0, 8);
            $brand->action = '
        <a href="' . route('Brands.edit', $brand->id_hashed) . '" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit brands">
            <i class="fas fa-user-edit text-secondary"></i>
        </a>';
            return $brand;
        });
    return DataTables::of($brand)
        ->rawColumns(['action'])
        ->make(true);
}
    public function edit($hashedId)
    {
        $brand = Brands::get()->first(function ($u) use ($hashedId) {
            $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
            return $expectedHash === $hashedId;
        });

        if (!$brand) {
            abort(404, 'Uom not found.');
        }


        return view('pages.Brands.edit', [
            'hashedId' => $hashedId,
            'brand' => $brand,
        ]);
    }

    public function create()
    {

        return view('pages.Brands.create');
    }
   
//     public function store(Request $request)
// {
//     $validatedData = $request->validate([
//         // Hapus validasi uom_code karena akan digenerate
//         'brand_code' => [
//                         'nullable',
//                         'string',
//                         'max:255',
//                         'unique:brands_tables,brand_code',
//                         new NoXSSInput()
//                     ],
//                     'brand_name' => [
//                         'required',
//                         'string',
//                         'max:255',
//                         'unique:brands_tables,brand_name',
//                         new NoXSSInput()
//                     ],
        
//                     'description' => [
//                         'required',
//                         new NoXSSInput()
//                     ],
        
//     ], [
//                'brand_code.string' => 'brand_code hanya boleh berupa teks.',
//             'brand_code.max' => 'brand_code maksimal terdiri dari 255 karakter.',
//             'brand_code.unique' => 'brand_code harus unique.',
//             'brand_name.required' => 'brand_name wajib diisi.',
//             'brand_name.string' => 'brand_name hanya boleh berupa teks.',
//             'brand_name.max' => 'brand_name maksimal terdiri dari 255 karakter.',
//             'brand_name.unique' => 'brand_name harus unique.',
//             'description.required' => 'description harus terisi.',
//     ]);

//         try {
//         DB::beginTransaction();

//         // Cari kode terakhir
//         $lastBrand = DB::table('brands_tables') // Pastikan ini nama tabel yang benar
//             ->select('brand_code')
//             ->orderBy('brand_code', 'desc')
//             ->first();

//         if ($lastBrand && preg_match('/BR(\d+)/', $lastBrand->brand_code, $matches)) {
//             $lastNumber = (int) $matches[1];
//             $nextNumber = $lastNumber + 1;
//         } else {
//             $nextNumber = 1;
//         }

//         // Generate uom_code baru
//         $newCode = 'BR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

//         // Simpan ke database
//         $brand = Brands::create([
//             'brand_code' => $newCode,
//             'brand_name' => $validatedData['brand_name'],
//             'description' => $validatedData['description'],
//         ]);

//         DB::commit();
//         return redirect()->route('pages.Brands')->with('success', 'Brands created successfully!');
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()
//             ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
//             ->withInput();
//     }
// }
public function store(Request $request)
    {
        

        $validated = $request->validate([
             'brand_name' => [
                'required',
                'string',
                'max:255',
                new NoXSSInput(),
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value))); // hilangkan spasi ganda
                    $exists = Brands::all()
                        ->map(function ($item) {
                        return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                    })
                        ->contains($normalized);

                    if ($exists) {
                        $fail('The ' . $attribute . ' is too similar to an existing name.');
                    }
                }
            ],
             'description' => ['nullable', 'string','max:255', new NoXSSInput()],
              
        ], [
         
        ]);
        try {
        DB::beginTransaction();

            $lastCode = Brands::whereNotNull('brand_code')
        ->lockForUpdate()
        ->orderBy('brand_code', 'desc')
        ->value('brand_code');

    if ($lastCode) {
        $number = (int) substr($lastCode, 2); // Hilangkan 'BR'
        $nextNumber = $number + 1;
    } else {
        $nextNumber = 1;
    }
    $nextCode = 'BR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT); // BR00001, BR00002, dst.
    Brands::create([
        'brand_name'  => strtoupper($validated['brand_name']),
        'description'  =>($validated['description']),
        'brand_code'  => $nextCode,
    ]);
            DB::commit();
            return redirect()->route('pages.Brands')->with('success', 'Brands created Succesfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Fatal Error: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    
    public function update(Request $request, $hashedId)
{
   $brands = Brands::cursor()->first(function ($u) use ($hashedId) {
    $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
    return $expectedHash === $hashedId;
});

        if (!$brands) {
            return redirect()->route('pages.Brands')->with('error', 'ID tidak valid.');
        }

    $validated = $request->validate([
        'brand_name' => [
            'required',
            'string',
            'max:255',
            new NoXSSInput(),
            function ($attribute, $value, $fail) use ($brands) {
                $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value)));

                $exists = Brands::where('id', '!=', $brands->id)
                    ->get()
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

        $brands->update([
            'brand_name' => strtoupper($validated['brand_name']),
            'description' => $validated['description'],
            // brand_code tidak diubah saat update
        ]);

        DB::commit();
        return redirect()->route('pages.Brands')->with('success', 'Brands updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()])
            ->withInput();
    }
}

    
    
    // public function update(Request $request, $hashedId)
    // {
    //     $brands = Brands::get()->first(function ($u) use ($hashedId) {
    //         $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
    //         return $expectedHash === $hashedId;
    //     });
    //     if (!$brands) {
    //         return redirect()->route('pages.Brands')->with('error', 'ID tidak valid.');
    //     }
    //     $validatedData = $request->validate([
    //         'brand_name' => [
    //             'required',
    //             'string',
    //             'max:255',
    //             Rule::unique('brands_tables')->ignore($brands->id),
    //             new NoXSSInput()
    //         ],
    //         'description' => [
    //             'required',
    //             new NoXSSInput()
    //         ],
    //     ], [
    //         'brand_name.required' => 'brand name wajib diisi.',
    //         'brand_name.string' => 'brand name hanya boleh berupa teks.',
    //         'brand_name.max' => 'brand name maksimal terdiri dari 255 karakter.',
    //         'brand_name.unique' => 'brand name harus unique.',
    //         'description.required' => 'description harus terisi.',
    //     ]);
    //     $brandsData = [
    //         'brand_name' => $validatedData['brand_name'],
    //         'description' => $validatedData['description'],
    //     ];
    //     DB::beginTransaction();
    //     $brands->update($brandsData);
    //     DB::commit();
    //     return redirect()->route('pages.Brands')->with('success', 'Brands Berhasil Diupdate.');
    // }
}
