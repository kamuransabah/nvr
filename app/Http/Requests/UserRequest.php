<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aksiyona göre doğru izin
        $perm = match (true) {
            $this->isStore()   => 'user.add',
            $this->isUpdate()  => 'user.edit',
            $this->isArchive() => 'user.delete',
            $this->isRestore() => 'user.restore',
            default            => 'user.view',
        };

        if (! $this->user()?->can($perm)) {
            return false;
        }

        // Hedef kullanıcı (route param {id})
        $targetId = $this->route('id');
        $target   = $targetId ? User::withTrashed()->find($targetId) : null;

        // Kendini arşivleyemez
        if ($this->isArchive() && $target && $target->id === $this->user()->id) {
            return false;
        }

        // En az 1 admin kalsın
        if ($this->isArchive() && $target && $target->hasRole('Admin')) {
            $remainingAdmins = User::role('Admin')
                ->whereNull('deleted_at')
                ->where('id','!=',$target->id)
                ->count();
            if ($remainingAdmins < 1) return false;
        }

        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        $rules = [
            'name'     => ['required','string','max:255'],
            'isim'     => ['required','string','max:255'],
            'soyisim'  => ['required','string','max:255'],
            'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($id)],
            'telefon'  => ['nullable','string','max:20'],
            'profil_resmi' => ['nullable','image'],
            'durum'    => ['required','in:0,1'],
            'roles'    => ['nullable','array'],
        ];

        // store'da şifre zorunlu, update'de opsiyonel
        $rules['password'] = $this->isStore()
            ? ['required','confirmed', Password::defaults()]
            : ['nullable','confirmed', Password::defaults()];

        return $rules;
    }

    /* ---------- Route tespiti (hatasız) ---------- */

    protected function routeName(): string
    {
        return (string) ($this->route()?->getName() ?? '');
    }

    protected function endsWith(string $suffix): bool
    {
        // admin_prefix 'crm.' gibi geliyor; isimler 'crm.user.store'
        // Bu yüzden son ek ile kıyaslamak en sağlamı
        return Str::endsWith($this->routeName(), $suffix);
    }

    protected function isStore(): bool
    {
        return $this->isMethod('post') && $this->endsWith('user.store');
    }

    protected function isUpdate(): bool
    {
        return in_array($this->method(), ['PUT','PATCH'], true) && $this->endsWith('user.update');
    }

    protected function isArchive(): bool
    {
        return $this->isMethod('delete') && $this->endsWith('user.delete');
    }

    protected function isRestore(): bool
    {
        return $this->isMethod('patch') && $this->endsWith('user.restore');
    }
}
