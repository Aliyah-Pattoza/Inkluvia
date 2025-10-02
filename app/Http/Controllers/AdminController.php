<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // User Management
    public function manajemenPengguna(Request $request)
    {
        $query = User::with('lembaga');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by lembaga_id
        if ($request->filled('lembaga_id')) {
            $query->where('lembaga_id', $request->lembaga_id);
        }

        // Filter by status (assuming we have a status field)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(10)->withQueryString();
        $lembagas = Lembaga::all();

        return view('admin.manajemen-pengguna', compact('users', 'lembagas'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,user',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'lembaga_id' => 'nullable|exists:lembagas,id'
        ]);

        User::create([
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'lembaga_id' => $request->lembaga_id,
            'email_verified_at' => now()
        ]);

        return redirect()->route('admin.manajemen-pengguna')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'lembaga_id' => 'nullable|exists:lembagas,id',
            'password' => 'nullable|min:8'
        ]);

        $data = [
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'role' => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'lembaga_id' => $request->lembaga_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.manajemen-pengguna')->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.manajemen-pengguna')->with('success', 'Pengguna berhasil dihapus!');
    }

    // Lembaga Management
    public function manajemenLembaga(Request $request)
    {
        $query = Lembaga::withCount('users');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $lembagas = $query->paginate(10)->withQueryString();

        return view('admin.manajemen-lembaga', compact('lembagas'));
    }

    public function createLembaga()
    {
        return view('admin.lembaga.create');
    }

    public function storeLembaga(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'type' => 'required|in:Individu,Sekolah,Lembaga',
            'alamat' => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        $lembaga = Lembaga::create($request->all());

        return redirect()->route('admin.manajemen-lembaga')->with('success', 'Lembaga berhasil ditambahkan!');
    }

    public function showLembaga(Lembaga $lembaga)
    {
        $lembaga->load(['users' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('admin.lembaga.show', compact('lembaga'));
    }

    public function editLembaga(Lembaga $lembaga)
    {
        $lembaga->loadCount('users');
        return view('admin.lembaga.edit', compact('lembaga'));
    }

    public function updateLembaga(Request $request, Lembaga $lembaga)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'type' => 'required|in:Individu,Sekolah,Lembaga',
            'alamat' => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        $lembaga->update($request->all());

        return redirect()->route('admin.manajemen-lembaga')->with('success', 'Lembaga berhasil diperbarui!');
    }

    public function destroyLembaga(Lembaga $lembaga)
    {
        // Check if lembaga has users
        if ($lembaga->users()->count() > 0) {
            return redirect()->back()->with('error', 'Lembaga tidak dapat dihapus karena masih memiliki pengguna!');
        }

        $lembaga->delete();
        return redirect()->route('admin.manajemen-lembaga')->with('success', 'Lembaga berhasil dihapus!');
    }
}