<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }


    public function index()
    {
        $roles = Role::pluck('name', 'id');
        return view(theme_view('admin', 'pages.user.index'), compact('roles'));
    }

    public function getData(Request $request)
    {

        $result = User::select('users.id', 'users.name', 'users.isim', 'users.soyisim', 'users.telefon', 'users.email', 'users.profil_resmi', 'users.durum', 'users.created_at')
            ->with('roles');

        if($request->has('arama') && !empty($request->arama)) {
            $arama = $request->arama;

            if(preg_match('/^[0-9\s\-\+]+$/', $arama)) {
                // Rakam, boşluk, - veya + içeriyorsa telefon
                $result->where('users.telefon', 'like', '%' . $arama . '%');
            } elseif(filter_var($arama, FILTER_VALIDATE_EMAIL)) {
                // Geçerli bir e-posta ise
                $result->where('users.email', 'like', '%' . $arama . '%');
            } else {
                // İsim veya soyisim alanlarında arama
                $result->where(function($query) use ($arama) {
                    $query->where('users.isim', 'like', '%' . $arama . '%')->orWhere('users.soyisim', 'like', '%' . $arama . '%');
                });
            }
        }

        if ($request->filled('yetki')) {
            $yetki = $request->yetki; // örn: "Admin" | "personel" | "none" | "all"

            if ($yetki === 'all') {
                // geç
            } elseif ($yetki === 'none') {
                $result->doesntHave('roles');
            } else {
                // rol adı ile
                $result->whereHas('roles', function ($q) use ($yetki) {
                    $q->where('name', $yetki);
                });
                // Eğer select'ten rol ID gönderiyorsan bir üst satırı yoruma alıp şunu aç:
                // $result->whereHas('roles', fn($q) => $q->where('id', $yetki));
            }
        }

        // Duruma göre filtreleme
        if ($request->filled('durum')) {
            $result->where('users.durum', $request->durum);
        }

        // Tarih aralığına göre filtreleme
        if ($request->has('tarih_filtre') && !empty($request->tarih_filtre)) {
            $tarihAraligi = explode(' - ', $request->tarih_filtre);

            $baslangicTarihi = Carbon::parse($tarihAraligi[0])->startOfDay(); // 00:00:00
            $bitisTarihi = Carbon::parse($tarihAraligi[1])->endOfDay(); // 23:59:59

            $result->whereBetween('users.created_at', [$baslangicTarihi, $bitisTarihi]);
        }

        return DataTables::of($result)
            ->addColumn('id', function ($data) {
                return '<button type="button" disabled class="btn btn-sm btn-block btn-secondary">'.$data->id.'</button>';
            })
            ->addColumn(
                'isim', function ($data) {
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol  symbol-40px symbol-circle ">
                            <img alt="'.$data->isim.' '.$data->soyisim.'" src="'.userAvatar($data->profil_resmi, 'user').'">
                        </div>
                        <div class="ms-4">
                            <span class="fs-6 fw-bold text-gray-900 text-hover-primary mb-2">'.$data->isim.' '.$data->soyisim.'</span>
                            <div class="fw-semibold fs-7 text-muted">'.$data->email.'</div>
                        </div>
                    </div>';
            })
            ->addColumn('yetki', function ($data) {
                if ($data->roles->isNotEmpty()) {
                    return $data->roles->map(function ($role) {
                        return '<span class="badge badge-lg badge-light-'.status()->get($role->name, 'class','yetki').'">'.$role->name.'</span>';
                    })->implode(' ');
                }
                return '<span class="badge badge-light-secondary">Yetki yok</span>';
            })
            ->addColumn('tarih', function ($data) {
                return Carbon::parse($data->created_at)->format('d.m.Y');
            })
            ->addColumn('durum', function ($data) {
                return '<span class="badge badge-light-'.status()->get($data->durum, 'class').'">'.status()->get($data->durum, 'text').'</span>';
            })
            ->addColumn('islem', function ($row) {
                // Düzenle butonu
                $buttons = '
                    <button type="button" class="btn btn-sm btn-icon btn-light js-edit"
                            data-id="'.$row->id.'" data-bs-toggle="tooltip" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                ';

                // Arşivle / Geri Yükle butonları
                if ($row->deleted_at) {
                    // Geri yükle
                    $buttons .= '
                        <button type="button" class="btn btn-sm btn-icon btn-primary js-restore"
                                data-id="'.$row->id.'" data-bs-toggle="tooltip" title="Geri Yükle">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    ';
                } else {
                    // Arşivle (kendini gizlemek istersen burada kontrol yapabilirsin)
                    if (auth()->id() !== $row->id) {
                        $buttons .= '
                <button type="button" class="btn btn-sm btn-icon btn-warning js-archive"
                        data-id="'.$row->id.'" data-bs-toggle="tooltip" title="Arşivle">
                    <i class="fa-solid fa-box-archive"></i>
                </button>
            ';
                    }
                }

                return $buttons;
            })
            ->rawColumns(['id', 'isim', 'yetki', 'tarih', 'durum', 'islem'])
            ->make(true);
    }

    /**
     * Yeni kullanıcı formu
     * GET crm/user/add
     */
    public function add()
    {
        $roles = Role::orderBy('name')->pluck('name', 'name');
        return view(theme_view('admin', 'pages.user.add'), compact('roles'));
    }

    /**
     * Kaydet
     * POST crm/user/store
     */
    public function store(UserRequest $request, imageUploadService $imageUploadService)
    {
        $data = $request->validated();

        $user = new User();
        $user->isim     = $data['isim'];
        $user->soyisim  = $data['soyisim'];
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->telefon  = $data['telefon'] ?? null;
        $user->durum    = $data['durum'];
        $user->password = Hash::make($data['password']);


        if ($request->hasFile('profil_resmi')) {
            $imagePaths = $imageUploadService->upload($request->file('profil_resmi'), 'user');
            $user->profil_resmi = $imagePaths['image'];
        }

        $user->save();
        $user->syncRoles($data['roles'] ?? []);

        return response()->json(['ok'=>true,'message'=>'Kullanıcı oluşturuldu.']);
    }

    /**
     * Düzenleme formu
     * GET crm/user/edit/{id}
     */
    public function edit($id)
    {
        // Arşivli kullanıcı da düzenlenebilsin istiyorsan withTrashed()
        $user      = User::withTrashed()->findOrFail($id);
        $roles     = Role::orderBy('name')->pluck('name', 'name');
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('crm.user.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Güncelle
     * PUT crm/user/update/{id}
     */
    public function update(UserRequest $request, $id, imageUploadService $imageUploadService)
    {
        $data = $request->validated();
        $user = User::withTrashed()->findOrFail($id);

        $user->isim     = $data['isim'];
        $user->soyisim  = $data['soyisim'];
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->telefon  = $data['telefon'] ?? null;
        $user->durum    = $data['durum'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Profil resmi silinsin mi kontrolü
        if ($request->filled('remove_avatar') && $request->remove_avatar == 1) {
            // Dosya varsa sil
            if ($user->profil_resmi && Storage::disk('public')->exists('upload/user/' . $user->profil_resmi)) {
                Storage::disk('public')->delete('upload/user/' . $user->profil_resmi);
            }

            $user->profil_resmi = null;
        }

        if ($request->hasFile('profil_resmi')) {
            $imagePaths = $imageUploadService->upload($request->file('profil_resmi'), 'user');
            $user->profil_resmi = $imagePaths['image'];
        }

        $user->save();
        $user->syncRoles($data['roles'] ?? []);

        return response()->json(['ok'=>true,'message'=>'Kullanıcı güncellendi.']);
    }

    /**
     * Arşivle (soft delete)
     * DELETE crm/user/delete/{id}
     */
    public function delete(UserRequest $request, $id)
    {
        $target = User::findOrFail($id);

        // Kendini arşivleyemez
        if (auth()->id() === $target->id) {
            return redirect()->back()->with('alert', [
                'library' => 'sweetalert',
                'type'    => 'error',
                'message' => 'Kendi hesabınızı arşivleyemezsiniz.'
            ]);
        }

        // En az bir aktif Admin kalmalı
        if ($target->hasRole('Admin') && $this->isLastActiveAdmin($target)) {
            return redirect()->back()->with('alert', [
                'library' => 'sweetalert',
                'type'    => 'error',
                'message' => 'Sistemde en az bir aktif Admin kalmalıdır. Bu kullanıcı arşivlenemez.'
            ]);
        }

        $target->delete(); // Soft delete

        return redirect()->route(config('system.admin_prefix') . '.user.index')->with('alert', [
            'library' => 'sweetalert',
            'type'    => 'success',
            'message' => 'Kullanıcı arşivlendi.'
        ]);
    }

    /**
     * Geri Yükle
     * PATCH crm/user/restore/{id}
     */
    public function restore(UserRequest $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Not: email unique index ile çakışma ihtimali yok; soft delete geri yüklemede aynı email kalır.
        // Eğer farklı bir iş kuralın varsa burada kontrol edebilirsin.

        $user->restore();

        return redirect()->route(config('system.admin_prefix') . '.user.index')->with('alert', [
            'library' => 'sweetalert',
            'type'    => 'success',
            'message' => 'Kullanıcı geri yüklendi.'
        ]);
    }

    /**
     * Bu kullanıcı arşivlenirse aktif Admin kalıyor mu?
     */
    private function isLastActiveAdmin(User $target): bool
    {
        $remainingAdmins = User::role('Admin')
            ->whereNull('deleted_at')
            ->where('id', '!=', $target->id)
            ->count();

        return $remainingAdmins < 1;
    }

    public function addModal()
    {
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name','name');
        return view(theme_view('admin', 'pages.user._modal_form'), [
            'formAction' => route(config('system.admin_prefix').'.user.store'),
            'method'     => 'POST',
            'title'      => 'Yeni Kullanıcı',
            'roles'      => $roles,
            'user'       => null,
            'userRoles'  => [],
        ]);
    }

    public function editModal($id)
    {
        $user      = \App\Models\User::withTrashed()->findOrFail($id);
        $roles     = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name','name');
        $userRoles = $user->roles->pluck('name')->toArray();

        return view(theme_view('admin', 'pages.user._modal_form'), [
            'formAction' => route(config('system.admin_prefix').'.user.update', $user->id),
            'method'     => 'PUT',
            'title'      => 'Kullanıcıyı Düzenle',
            'roles'      => $roles,
            'user'       => $user,
            'userRoles'  => $userRoles,
        ]);
    }

}
