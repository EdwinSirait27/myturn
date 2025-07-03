<?php

namespace App\Http\Controllers;

use App\Models\Subcat;
use App\Models\Cat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Helpers\UuidHashHelper;


class SubcatController extends Controller
{
       public function index()
    {
        return view('pages.Subcat.Subcat');
    }
   public function getSubcats()
{
    $query = Subcat::query()->select(['id', 'categories_id','name', 'code'])->with('categories');
    return datatables()->eloquent($query)
        ->addColumn('action', function ($subcat) {
            $hashed = UuidHashHelper::encodeUuid($subcat->id);
            $route = route('Subcat.edit', $hashed);
            return <<<HTML
<a href="{$route}" class="mx-3" title="Edit Sub Categories">
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
    $subcat = Subcat::findOrFail($uuid);

    return view('pages.Subcat.edit', [
        'subcat' => $subcat,
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
                    $exists = Subcat::all()
                        ->map(function ($item) {
                        return strtolower(trim(preg_replace('/\s+/', ' ', $item->name)));
                    })
                        ->contains($normalized);

                    if ($exists) {
                        $fail('The ' . $attribute . ' is too similar to an existing name.');
                    }
                }
            ],
              'categories_id' => ['required','exists:categories,id', 
                new NoXSSInput()],
         
            
        ], [
         
        ]);
        try {
        DB::beginTransaction();

          Subcat::create([
                'name' => strtoupper($validated['name']),

                   'categories_id' => $validated['categories_id'], 
            
            ]);

            DB::commit();
            return redirect()->route('pages.Subcat')->with('success', 'Sub Categories created Succesfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }
   public function create()
    {
    $cats = Cat::select('id', 'name')->get();

        return view('pages.Subcat.create',compact('cats'));
    }

}
