<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use App\Rules\NoXSSInput;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;
class CountryController extends Controller
{
    public function index()
    {
        return view('pages.Country.Country');
    }

    public function getCountrys()
    {
        $countrys = Country::select(['id', 'country_name'])
            ->get()
            ->map(function ($country) {
                $country->id_hashed = substr(hash('sha256', $country->id . env('APP_KEY')), 0, 8);
                $country->action = '
                    <a href="' . route('Country.edit', $country->id_hashed) . '" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit country"title="Edit country: ' . e($country->country_name) . '">
                        <i class="fas fa-user-edit text-secondary"></i>
                    </a>';
                return $country;
            });
        return DataTables::of($countrys)

            ->rawColumns(['action'])
            ->make(true);
    }
    public function edit($hashedId)
    {
        $country = Country::get()->first(function ($u) use ($hashedId) {
            $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
            return $expectedHash === $hashedId;
        });

        if (!$country) {
            abort(404, 'country not found.');
        }

        return view('pages.Country.edit', [
            'country' => $country,
            'hashedId' => $hashedId,

        ]);
    }

    public function create()
    {

        return view('pages.Country.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'country_name' => [
                'required',
                'string',
                'max:255',
                'unique:country,country_name',
                new NoXSSInput()
            ],

        ], [
            'country_name.required' => 'country name must be filled.',
            'country_name.string' => 'country name must be a text.',
            'country_name.max' => 'country name max input 255 character.',
            'country_name.unique' => 'country name already taken.',

        ]);
        try {
            DB::beginTransaction();
            $country = Country::create([
                'country_name' => $validatedData['country_name'],
            ]);
            DB::commit();
            return redirect()->route('pages.Country')->with('success', 'Country created Succesfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to store data: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function update(Request $request, $hashedId)
    {
        $country = Country::get()->first(function ($u) use ($hashedId) {
            $expectedHash = substr(hash('sha256', $u->id . env('APP_KEY')), 0, 8);
            return $expectedHash === $hashedId;
        });
        if (!$country) {
            return redirect()->route('pages.Country')->with('error', 'ID tidak valid.');
        }
        $validatedData = $request->validate([
            'country_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('country')->ignore($country->id),
                new NoXSSInput()
            ],

        ], [
            'country_name.required' => 'country name must be filled.',
            'country_name.string' => 'country name must be a text.',
            'country_name.max' => 'country name max input 255 character.',

        ]);

        $countryData = [
            'country_name' => $validatedData['country_name'],

        ];
        DB::beginTransaction();
        $country->update($countryData);
        DB::commit();

        return redirect()->route('pages.Country')->with('success', 'Country updated Successfully.');
    }
}


