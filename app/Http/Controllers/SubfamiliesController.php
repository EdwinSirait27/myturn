<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Subcat;
use App\Models\Subfamilies;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Helpers\UuidHashHelper;
class SubfamiliesController extends Controller
{
     public function index()
    {
        return view('pages.Subfamilies.Subfamilies');
    }
   public function getSubFamilies()
{
    $query = Subfamilies::query()->select(['id', 'families_id','name', 'code'])->with('families');
    return datatables()->eloquent($query)
        ->addColumn('action', function ($familes) {
            $hashed = UuidHashHelper::encodeUuid($familes->id);
            $route = route('Subfamilies.edit', $hashed);
            return <<<HTML
<a href="{$route}" class="mx-3" title="Edit Sub Families">
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
    $subfamilies = Subfamilies::findOrFail($uuid);

    return view('pages.Subfamilies.edit', [
        'subfamilies' => $subfamilies,
        'hashedId' => $hashedId,
    ]);
}
 public function store(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
             'name' => [
                'required',
                'string',
                'max:255',
                new NoXSSInput(),
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $value))); // hilangkan spasi ganda
                    $exists = Subfamilies::all()
                        ->map(function ($item) {
                        return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                    })
                        ->contains($normalized);

                    if ($exists) {
                        $fail('The ' . $attribute . ' is too similar to an existing name.');
                    }
                }
            ],
              'families_id' => ['required','exists:family,id', 
                new NoXSSInput()],
         
            
        ], [
         
        ]);
        try {
        DB::beginTransaction();

          Subfamilies::create([
                'name' => strtoupper($validated['name']),

                   'families_id' => $validated['families_id'], 
            
            ]);

            DB::commit();
            return redirect()->route('pages.Subfamilies')->with('success', 'Sub families created Succesfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }
   public function create()
    {
    $families = Family::select('id', 'name')->get();

        return view('pages.Subfamilies.create',compact('families'));
    }

}
