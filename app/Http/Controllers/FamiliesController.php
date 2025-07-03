<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Subcat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Helpers\UuidHashHelper;

class FamiliesController extends Controller
{
    public function index()
    {
        return view('pages.Families.Families');
    }
   public function getFamilies()
{
    $query = Family::query()->select(['id', 'subcategories_id','name', 'code'])->with('subcat');
    return datatables()->eloquent($query)
        ->addColumn('action', function ($familes) {
            $hashed = UuidHashHelper::encodeUuid($familes->id);
            $route = route('Families.edit', $hashed);
            return <<<HTML
<a href="{$route}" class="mx-3" title="Edit Families">
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
    $families = Family::findOrFail($uuid);

    return view('pages.Families.edit', [
        'families' => $families,
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
                    $exists = Family::all()
                        ->map(function ($item) {
                        return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                    })
                        ->contains($normalized);

                    if ($exists) {
                        $fail('The ' . $attribute . ' is too similar to an existing name.');
                    }
                }
            ],
              'subcategories_id' => ['required','exists:subcategories,id', 
                new NoXSSInput()],
         
            
        ], [
         
        ]);
        try {
        DB::beginTransaction();

          Family::create([
                'name' => strtoupper($validated['name']),

                   'subcategories_id' => $validated['subcategories_id'], 
            
            ]);

            DB::commit();
            return redirect()->route('pages.Families')->with('success', 'Families created Succesfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }
   public function create()
    {
    $subcats = Subcat::select('id', 'name')->get();

        return view('pages.Families.create',compact('subcats'));
    }

}
