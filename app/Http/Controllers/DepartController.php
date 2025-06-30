<?php

namespace App\Http\Controllers;

use App\Models\Depart;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Helpers\UuidHashHelper;
class DepartController extends Controller
{
      public function index()
    {
        return view('pages.Depart.Depart');
    }
   public function getDeparts()
{
    $query = Depart::query()->select(['id', 'name', 'code']);
    return datatables()->eloquent($query)
        ->addColumn('action', function ($depart) {
            $hashed = UuidHashHelper::encodeUuid($depart->id);
            $route = route('Depart.edit', $hashed);
            return <<<HTML
<a href="{$route}" class="mx-3" title="Edit Departments">
    <i class="fas fa-user-edit text-secondary"></i>
</a>
HTML;
        })
        ->rawColumns(['action'])
        ->make(true);
}

    public function edit($hashedId)
{
    $uuid = UuidHashHelper::decodeUuid($hashedId);

    if (!$uuid) {
        abort(404, 'Invalid ID.');
    }

    // Cari langsung di DB tanpa load semua data
    $depart = Depart::findOrFail($uuid);

    return view('pages.Depart.edit', [
        'depart' => $depart,
        'hashedId' => $hashedId,
    ]);
}
 
//     public function create()
//     {
//         $managers = User::with('Employee')->get();
//         return view('pages.Department.create',compact('managers'));
//     }

//     public function store(Request $request)
//     {
//         // dd($request->all());

//         $validatedData = $request->validate([
//             'department_name' => ['required', 'string','max:255', 'unique:departments_tables,department_name',
//                 new NoXSSInput()],
//             'manager_id' => ['required','max:255', 
//                 new NoXSSInput()],
            
//         ], [
//            'department_name.required' => 'Department name is required.',
// 'department_name.string' => 'Department name must be a string.',
// 'department_name.max' => 'Department name may not be greater than 255 characters.',
// 'department_name.unique' => 'Department name must be unique or already exists.',
// 'manager_id.required' => 'Manager is required.',
// 'manager_id.max' => 'Manager may not be greater than 255 characters.',
// 'manager_id.string' => 'Manager must be a string.',
//         ]);
//         try {
//             DB::beginTransaction();
//             $department = Departments::create([
//                 'department_name' => $validatedData['department_name'], 
//                 'manager_id' => $validatedData['manager_id'], 
//             ]);
//             DB::commit();
//             return redirect()->route('pages.Department')->with('success', 'Department created Succesfully!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()
//                 ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
//                 ->withInput();
//         }
//     }
//     public function update(Request $request, $hashedId)
//     {
//         $department = Departments::with('user.Employee')->get()->first(function ($u) use ($hashedId) {
//             $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
//             return $expectedHash === $hashedId;
//         });
//         if (!$department) {
//             return redirect()->route('pages.Department')->with('error', 'ID tidak valid.');
//         }
//         $validatedData = $request->validate([
//             'department_name' => ['required', 'string', 'max:255',Rule::unique('departments_tables')->ignore($department->id),
//             new NoXSSInput()],
//             'manager_id' => ['required', 'string', 'max:255',
//             new NoXSSInput()],

//         ], [
//             'department_name.required' => 'name wajib diisi.',
//             'manager_id.required' => 'Manager wajib diisi.',
//             'department_name.string' => 'name hanya boleh berupa teks.',
            
//         ]);

//         $departmentData = [
//             'department_name' => $validatedData['department_name'],
//             'manager_id' => $validatedData['manager_id'],
            
//         ];
//         DB::beginTransaction();
//         $department->update($departmentData);
//         DB::commit();

//         return redirect()->route('pages.Department')->with('success', 'Department Berhasil Diupdate.');
//     }
}
