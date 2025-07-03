<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserprofileController extends Controller
{
//     public function index()
//     {
//         $user = Auth::user();
//           $roles = Role::pluck('name', 'name')->all();
//        $selectedRole = old('role', $user->roles->pluck('name')->toArray());


//         return view('pages.feature-profile', compact('user','roles','selectedRole'));
//     }
//     public function updatePassword(Request $request)
// {
//     $request->validate([
//         'password' => ['nullable', 'string', 'min:8'],
//         'role' => ['required', 'array'],
// 'role.*' => ['string', 'exists:roles,name'],
//     ]);

//     $user = Auth::user();

//     // Update password hanya jika new_password ada isinya
//     if ($request->filled('password')) {
//         $user->password = Hash::make($request->password);
//     }
//    $user->syncRoles($validatedData['role']); // langsung array of role names


//     // Update name jika diisi (harus ada karena required)
    
//     if ($user->save()) {
//         Log::info("Data berhasil diperbarui untuk user ID {$user->id}");
//         return back()->with('status', 'Success');
//     } else {
//         Log::error("Gagal menyimpan data untuk user ID {$user->id}");
//         return back()->withErrors(['update' => 'Gagal menyimpan perubahan']);
//     }
// }
public function index()
{
    $user = Auth::user();
 $roles = $user->roles->pluck('name', 'name'); // hanya role milik user, untuk dropdown
$selectedRole = old('role', $roles->keys()->toArray()); // selected dari role user

    return view('pages.feature-profile', compact('user', 'roles', 'selectedRole'));
}


// public function updatePassword(Request $request)
// {
//     $validatedData = $request->validate([
//         'password' => ['nullable', 'string', 'min:8'],
//        'role' => ['required', 'string', 'exists:roles,name'],
//         'role.*' => ['string', 'exists:roles,name'],
//     ]);

//     $user = Auth::user();

//     // Update password jika diisi
//     if (!empty($validatedData['password'])) {
//         $user->password = Hash::make($validatedData['password']);
//     }
//  $selectedRole = $validatedData['role'];

//     // Kalau sudah punya role itu, hapus dulu agar bisa ditambahkan ulang dan muncul pertama
//     if ($user->hasRole($selectedRole)) {
//         $user->removeRole($selectedRole);
//     }

//     // Tambahkan ulang agar menjadi urutan pertama
//     $user->assignRole($selectedRole);

//     // Tidak menghapus role lain
//     // (Role lama tetap, hanya $selectedRole dipindah ke posisi akhir → muncul pertama saat ->getRoleNames()->first())

//     // Set session active role manual juga
//     session(['active_role' => $selectedRole]);

//     if ($user->save()) {
//         Log::info("Data berhasil diperbarui untuk user ID {$user->id}");
//         return back()->with('status', 'Success');
//     } else {
//         Log::error("Gagal menyimpan data untuk user ID {$user->id}");
//         return back()->withErrors(['update' => 'Update Failed']);
//     }
// }
public function updatePassword(Request $request)
{
    $validatedData = $request->validate([
        'password' => ['nullable', 'string', 'min:8'],
        'role' => ['required', 'string', 'exists:roles,name'], // hanya string karena single role
    ]);

    $user = Auth::user();
    $selectedRole = $validatedData['role'];

    // Update password jika diisi
    if (!empty($validatedData['password'])) {
        $user->password = Hash::make($validatedData['password']);
    }

    // Jika belum punya role itu, tambahkan
    if (!$user->hasRole($selectedRole)) {
        $user->assignRole($selectedRole);
    }

    // Simpan role yang dipilih sebagai default aktif (permanen)
    $user->active_role = $selectedRole;

    if ($user->save()) {
        // Juga simpan ke session agar langsung aktif
        session(['active_role' => $selectedRole]);

        Log::info("Role aktif diubah menjadi {$selectedRole} untuk user ID {$user->id}");
        return back()->with('status', 'Success');
    } else {
        Log::error("Gagal menyimpan data untuk user ID {$user->id}");
        return back()->withErrors(['update' => 'Update Failed']);
    }
}

}

