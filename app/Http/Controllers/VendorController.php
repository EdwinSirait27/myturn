<?php

namespace App\Http\Controllers;
use App\Models\Banks;
use App\Models\Stores;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\Vendorgroup;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class VendorController extends Controller
{
//     public function create()
// {
//    $types = [
//     'Regular Vendor',
//     'Consignment',
//     'Consignment Open Price',
//     'General Allocation',
// ];
//          $vendors = Vendorgroup::select('id', 'name','code')->get();
//          $countrys = Country::select('id', 'country_name')->get();
//          $stores = Stores::select('id', 'name')->get();
//          $banks = Banks::select('id', 'name')->get();
//     return view('pages.Vendor.create', compact('vendors','countrys','stores','banks','types')); 
// }
public function create($hashedId)
{
    $vendorgroup = Vendorgroup::get()->first(function ($u) use ($hashedId) {
        return substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8) === $hashedId;
    });

    if (!$vendorgroup) {
        abort(404, 'Vendor group not found.');
    }

    $types = [
        'Regular Vendor',
        'Consignment',
        'Consignment Open Price',
        'General Allocation',
    ];

    $countrys = Country::select('id', 'country_name')->get();
    $stores = Stores::select('id', 'name')->get();
    $banks = Banks::select('id', 'name')->get();

    return view('pages.Vendor.create', compact(
        'vendorgroup', 'types', 'countrys', 'stores', 'banks', 'hashedId'
    ));
}

public function logs(Request $request)
{
    // dd(Activity::where('log_name', 'vendorgroup')->latest()->first());
    if ($request->ajax()) {
        $data = Activity::where('log_name', 'vendor')
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
   public function detail($hashedId)
{
    $vendorgroup = Vendorgroup::get()->first(function ($u) use ($hashedId) {
        return substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8) === $hashedId;
    });

    if (!$vendorgroup) {
        abort(404, 'Vendor group not found.');
    }

    return view('pages.Vendorgroup.detail', [
        'vendorgroup' => $vendorgroup,
        'hashedId' => $hashedId,
    ]);
}
// public function store(Request $request)
// {
        
//     $validated = $request->validate([
//         'vendor_group_id' => ['nullable', 'exists:vendorgroups,id', new NoXSSInput()],
//         // 'bank_id' => ['nullable', 'exists:banks_tables,id', new NoXSSInput()],
//         // 'country_id' => ['nullable', 'exists:country,id', new NoXSSInput()],
//         // 'store_id' => ['required', 'exists:stores_tables,id', new NoXSSInput()],
//     //      'type' => [
//     //     'required', 'exists:vendor,type',
//     //     new NoXSSInput(),
//     // ],
//       'type' => [
//         'required',
//         'in:Regular Vendor,Consignment,Consignment Open Price,General Allocation',
//         new NoXSSInput(),
//     ],
//     //  'code' => ['nullable', new NoXSSInput()],
//        'name' => [
//                 'required',
//                 'string',
//                 'max:255',
//                 new NoXSSInput(),
//                 function ($attribute, $value, $fail) {
//                     $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value))); // hilangkan spasi ganda
        
//                     $exists = Vendor::all()
//                         ->map(function ($item) {
//                             return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
//                         })
//                         ->contains($normalized);

//                     if ($exists) {
//                         $fail('The ' . $attribute . ' is too similar to an existing name.');
//                     }
//                 }
//             ],
//     //         'address' => ['required','string','max:255', new NoXSSInput()],
//     //         'city' => [
//     //             'required',
//     //             'string',
//     //             'max:255',
//     //             new NoXSSInput()
//     //         ],
//     //         'email' => ['required', 'string', 'max:255',],
//     //         'phonenumber' => ['required', 'numeric', 'digits_between:10,13',new NoXSSInput()],
//     //  'vendorpkp' => [
//     //     'required',
//     //     Rule::in($pkps),
//     //     new NoXSSInput(),
//     // ],
//     //  'consignment' => [
//     //     'required',
//     //     Rule::in($consignments),
//     //     new NoXSSInput(),
//     // ],
//     //         'salesname' => ['nullable','string','max:255', new NoXSSInput()],
//     //         'salescp' => ['nullable','string','numeric', 'digits_between:10,13', new NoXSSInput()],
//     //         'npwpname' => ['nullable','string','max:255', new NoXSSInput()],
//     //         'npwpaddress' => ['nullable','string','max:255', new NoXSSInput()],
//     //         'npwpnumber' => ['nullable','string','max:255', new NoXSSInput()],
//     //         'description' => ['nullable','string','max:255', new NoXSSInput()],
//     ]);
//     try {
//         DB::beginTransaction();

//         $vendor = Vendor::create([
//             'vendor_group_id' => ($validated['vendor_group_id']),
//             // 'country_id' => ($validated['country_id']),
//             // 'bank_id' => ($validated['bank_id']),
//             // // 'store_id' => ($validated['store_id']),
//             'name' => ($validated['name']),
//             'type' => ($validated['type']),
//             // 'code' => ($validated['code']),
//             // 'name' => strtoupper($validated['name']),
//             // 'address' => ($validated['address']),
//             // 'city' => ($validated['city']),
//             // 'email' => ($validated['email']),
//             // 'phonenumber' => ($validated['phonenumber']),
//             // 'consignment' => ($validated['consignment']),
//             // 'vendorpkp' => ($validated['vendorpkp']),
//             // 'salesname' => ($validated['salesname']),
//             // 'salescp' => ($validated['salescp']),
//             // 'npwpaddress' => ($validated['npwpaddress']),
//             // 'npwpnumber' => strtoupper($validated['npwpnumber']),
//             // 'description' => strtoupper($validated['description']),
//         ]);
//         DB::commit();
//         // Ambil kembali vendorgroup berdasarkan ID
//         $vendorgroup = Vendorgroup::findOrFail($validated['vendor_group_id']);

//         // Hitung hashedId-nya
//         $hashedId = substr(hash('sha256', $vendorgroup->id . env('APP_KEY')), 0, 8);

//         // Redirect ke halaman detail
//         return redirect()->route('Vendorgroup.detail', $hashedId)
//                          ->with('success', "Vendor Created Successfully.");
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return back()->withErrors(['error' => 'failed to save data: ' . $e->getMessage()])
//                      ->withInput();
//     }
// }

public function store(Request $request)
{
    $types = [
        'Regular Vendor',
        'Consignment',
        'Consignment Open Price',
        'General Allocation',
    ];

    $validated = $request->validate([
        'vendor_group_id' => ['required', 'exists:vendorgroups,id', new NoXSSInput()],
        'type' => ['required', Rule::in($types), new NoXSSInput()],
        'name' => [
            'required', 'string', 'max:255', new NoXSSInput(),
            function ($attribute, $value, $fail) {
                $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value)));
                $exists = Vendor::all()->map(function ($item) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                })->contains($normalized);

                if ($exists) {
                    $fail("The $attribute is too similar to an existing name.");
                }
            }
        ],
    ]);

    try {
        DB::beginTransaction();

        $vendor = Vendor::create([
            'vendor_group_id' => $validated['vendor_group_id'],
            'name' => $validated['name'],
            'type' => $validated['type'],
        ]);

        DB::commit();

        $vendorgroup = Vendorgroup::findOrFail($validated['vendor_group_id']);
        $hashedId = substr(hash('sha256', $vendorgroup->id . env('APP_KEY')), 0, 8);

        return redirect()->route('Vendorgroup.detail', $hashedId)
                         ->with('success', 'Vendor created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Failed to save data: ' . $e->getMessage()])
                     ->withInput();
    }
}




    public function getVendors($hashedId)
{
    $vendorgroup = Vendorgroup::get()->first(function ($u) use ($hashedId) {
        return substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8) === $hashedId;
    });

    if (!$vendorgroup) {
        abort(404, 'Vendor group not found.');
    }

   $vendors = $vendorgroup->vendors()->with('banks','country','group')->select(['id', 'type','code','address','name','city','country_id','email','phonenumber','consignment','vendorpkp','salesname','salescp','npwpname','npwpnumber','npwpaddress','bank_id','description']); // pilih kolom eksplisit
    return DataTables::of($vendors)
  ->addColumn('action', function ($row) {
    return '<a href="' . route('Vendor.edit', $row->id_hashed) . '" class="btn btn-sm btn-primary">Edit</a>';
})
        ->rawColumns(['action'])
        ->make(true);
}    
 public function edit($hashedId)
    {
        $vendor = Vendor::with('banks')->get()->first(function ($u) use ($hashedId) {
            $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
            return $expectedHash === $hashedId;
        });
        if (!$vendor) {
            abort(404, 'Vemdor not found.');
        }
        $banks = Banks::get();
        $countrys = Country::get();
        return view('pages.Vendor.edit', [
            'vendor' => $vendor,
            'banks' => $banks,
            'countrys' => $countrys,
            'hashedId' => $hashedId,
        ]);
    }
     public function update(Request $request, $hashedId)
    {
        $vendor = Vendor::with('banks','country','store','group')->get()->first(function ($u) use ($hashedId) {
            $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
            return $expectedHash === $hashedId;
        });
        if (!$vendor) {
            return redirect()->route('pages.Vendor')->with('error', 'ID tidak valid.');
        }
        $types = DB::table('vendors')->distinct()->pluck('type')->toArray();
        $consignments = DB::table('vendors')->distinct()->pluck('consignment')->toArray();
        
        $validatedData = $request->validate([
             'type' => [
        'required',
        Rule::in($types),
        new NoXSSInput(),
    ],
            'code' => ['nullable', new NoXSSInput()],
            'address' => ['required','string','max:255', new NoXSSInput()],
            'city' => [
                'required',
                'string',
                'max:255',
                new NoXSSInput()
            ],
            'country_id' => ['required', 'exists:country,id', new NoXSSInput()],
            'email' => ['required', 'string', 'max:255',],
            'phonenumber' => ['required', 'numeric', 'digits_between:10,13',new NoXSSInput()],
     'consignment' => [
        'required',
        Rule::in($consignments),
        new NoXSSInput(),
    ],

            // 'bpjs_kes' => ['required', 'string', 'max:255',Rule::unique('employees_tables', 'bpjs_kes')->ignore($user->Employee->id), new NoXSSInput()],
            'bpjs_ket' => ['required', 'string', 'max:255'],
            // 'bpjs_ket' => ['required', 'string', 'max:255',Rule::unique('employees_tables', 'bpjs_ket')->ignore($user->Employee->id), new NoXSSInput()],
            // Rule::unique('employees_tables', 'email')->ignore($user->Employee->id), new NoXSSInput()],
            'emergency_contact_name' => ['required', 'string', 'max:255', new NoXSSInput()],
            'marriage' => ['required', 'string', 'max:255', new NoXSSInput()],
            'notes' => ['nullable', 'string', 'max:255', new NoXSSInput()],
            'child' => ['required', 'string', 'max:255', new NoXSSInput()],
            'gender' => ['required', 'string', 'max:255', new NoXSSInput()],
            'status_employee' => ['required', 'string', 'max:255', new NoXSSInput()],
            // 'nik' => ['required', 'max:20', Rule::unique('employees_tables', 'nik')->ignore($user->Employee->id), new NoXSSInput()],
            'bank_account_number' => ['required', 'max:20', new NoXSSInput()],
            'last_education' => ['required', 'string', 'max:255', new NoXSSInput()],
            'religion' => ['required', 'string', new NoXSSInput()],
            // 'daily_allowance' => ['nullable','string',
            // new NoXSSInput()],
            'place_of_birth' => ['required', 'string', 'max:255', new NoXSSInput()],
            'biological_mother_name' => ['required', 'string', 'max:255', new NoXSSInput()],
            'current_address' => ['required', 'string', 'max:255', new NoXSSInput()],
            'id_card_address' => ['required', 'string', 'max:255', new NoXSSInput()],
            'institution' => ['required', 'string', 'max:255', new NoXSSInput()],
            'npwp' => ['required', 'string', 'max:50'],
            'position_id' => ['required', 'exists:position_tables,id', new NoXSSInput()],
            'store_id' => ['required', 'exists:stores_tables,id', new NoXSSInput()],
            'company_id' => ['required', 'exists:company_tables,id', new NoXSSInput()],
            'department_id' => ['required', 'exists:departments_tables,id', new NoXSSInput()],
            'banks_id' => ['required', 'exists:banks_tables,id', new NoXSSInput()],
        ], [
            'join_date.required' => 'The join date is required.',
            'join_date.date_format' => 'The join date must be in the format YYYY-MM-DD.',
            // 'daily_allowance.numeric' => 'Net salary must be a number.',

            'date_of_birth.required' => 'The date of birth is required.',
            'date_of_birth.date_format' => 'The date of birth must be in the format YYYY-MM-DD.',

            'employee_name.required' => 'The employee name is required.',
            'employee_name.max' => 'The employee name may not be greater than 255 characters.',

            'bpjs_kes.required' => 'The BPJS Kesehatan field is required.',
            'bpjs_kes.max' => 'The BPJS Kesehatan may not be greater than 255 characters.',

            'bpjs_ket.required' => 'The BPJS Ketenagakerjaan field is required.',
            'bpjs_ket.max' => 'The BPJS Ketenagakerjaan may not be greater than 255 characters.',

            'email.required' => 'The email is required.',
            'email.max' => 'The email may not be greater than 255 characters.',

            'emergency_contact_name.required' => 'The emergency contact name is required.',
            'marriage.required' => 'The marriage status is required.',
            'notes.max' => 'The notes may not be greater than 255 characters.',
            'child.required' => 'The child information is required.',
            'gender.required' => 'The gender is required.',

            'telp_number.required' => 'The phone number is required.',
            'telp_number.numeric' => 'The phone number must be numeric.',
            'telp_number.max' => 'The phone number may not be greater than 13 digits.',

            'status_employee.required' => 'The employee status is required.',
            'nik.required' => 'The NIK is required.',
            'nik.max' => 'The NIK may not be greater than 20 characters.',
            'bank_account_number.required' => 'The bank account number is required.',
            'bank_account_number.max' => 'The bank account number may not be greater than 20 characters.',


            'last_education.required' => 'The last education field is required.',
            'last_education.max' => 'The last education may not be greater than 255 characters.',

            'religion.required' => 'The religion field is required.',

            'place_of_birth.required' => 'The place of birth is required.',
            'biological_mother_name.required' => 'The biological mother\'s name is required.',
            'current_address.required' => 'The current address is required.',
            'id_card_address.required' => 'The ID card address is required.',
            'institution.required' => 'The institution is required.',
            'npwp.required' => 'The NPWP is required.',
            'npwp.max' => 'The NPWP may not be greater than 50 characters.',

            'position_id.exists' => 'The selected position is invalid.',
            'store_id.exists' => 'The selected store is invalid.',
            'company_id.exists' => 'The selected company is invalid.',
            'department_id.exists' => 'The selected department is invalid.',
            'position_id.required' => 'The Position is required.',
            'store_id.required' => 'The Store is required.',
            'company_id.required' => 'The Company is required.',
            'department_id.required' => 'The Department is required.',
            'banks_id.exists' => 'The selected banks is invalid.',
            'banks_id.required' => 'The banks is required.',
        ]);

        DB::beginTransaction();
        $user->Employee->update([
            'employee_name' => $validatedData['employee_name'] ?? '',
            'nik' => $validatedData['nik'] ?? '',
            'bank_account_number' => $validatedData['bank_account_number'] ?? '',
            'position_id' => $validatedData['position_id'] ?? '',
            'company_id' => $validatedData['company_id'] ?? '',
            'store_id' => $validatedData['store_id'] ?? '',
            'department_id' => $validatedData['department_id'] ?? '',
            'banks_id' => $validatedData['banks_id'] ?? '',
            'status_employee' => $validatedData['status_employee'] ?? '',
            // 'daily_allowance' => Crypt::encrypt($validatedData['daily_allowance'])?? 0,

            'join_date' => $validatedData['join_date'] ?? '',
            'marriage' => $validatedData['marriage'] ?? '',
            'child' => $validatedData['child'] ?? '',
            'telp_number' => $validatedData['telp_number'] ?? '',
            'gender' => $validatedData['gender'] ?? '',
            'date_of_birth' => $validatedData['date_of_birth'] ?? '',

            'bpjs_kes' => $validatedData['bpjs_kes'] ?? '',
            'bpjs_ket' => $validatedData['bpjs_ket'] ?? '',
            'email' => $validatedData['email'] ?? '',
            'emergency_contact_name' => $validatedData['emergency_contact_name'] ?? '',

            'notes' => $validatedData['notes'] ?? '',
            'status' => $validatedData['status'] ?? 'Pending',
            'religion' => $validatedData['religion'] ?? '',
            'last_education' => $validatedData['last_education'] ?? '',
            // disini masi error
            'place_of_birth' => $validatedData['place_of_birth'] ?? '',
            'biological_mother_name' => $validatedData['biological_mother_name'] ?? '',
            'current_address' => $validatedData['current_address'] ?? '',
            'id_card_address' => $validatedData['id_card_address'] ?? '',
            'institution' => $validatedData['institution'] ?? '',
            'npwp' => $validatedData['npwp'] ?? '',
        ]);
        DB::commit();
        return redirect()->route('pages.Employee')->with('success', 'Employee Berhasil Diupdate.');
    }
}
