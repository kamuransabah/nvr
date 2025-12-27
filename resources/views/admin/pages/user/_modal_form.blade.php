<div class="modal-header">
    <h2 class="modal-title">{{ $title ?? 'Form' }}</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="userForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(($method ?? 'POST') !== 'POST') @method($method) @endif

    <div class="modal-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>

        <div class="mb-3">
            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('{{ userAvatar($profil->profil_resmi ?? null) }}')">
                <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ userAvatar($user->profil_resmi ?? null) }})"></div>
                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Resmi Değiştir">
                    <i class="ki-outline ki-pencil fs-7"></i>
                    <input type="file" name="profil_resmi" />
                    <input type="hidden" name="avatar_remove" />
                </label>
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="İptal">
                    <i class="ki-outline ki-cross fs-2"></i>
                </span>
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Resmi Sil">
                    <i class="ki-outline ki-cross fs-2"></i>
                </span>
            </div>
            <div class="form-text">Geçerli dosya türleri: png, jpg, jpeg.</div>
        </div>

        <div class="mb-3">
            <label class="form-label required">Rumuz</label>
            <input type="text" name="name" class="form-control form-control-solid"
                   value="{{ old('name', $user->name ?? '') }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">İsim</label>
                <input type="text" name="isim" class="form-control form-control-solid"
                       value="{{ old('isim', $user->isim ?? '') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label required">Soyisim</label>
                <input type="text" name="soyisim" class="form-control form-control-solid"
                       value="{{ old('soyisim', $user->soyisim ?? '') }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">E-posta</label>
                <input type="email" name="email" class="form-control form-control-solid"
                       value="{{ old('email', $user->email ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Telefon</label>
                <input type="text" name="telefon" class="form-control form-control-solid"
                       value="{{ old('telefon', $user->telefon ?? '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Şifre
                @isset($user) <small class="text-muted">(boş bırakırsanız değişmez)</small> @endisset
            </label>
            <input type="password" name="password" class="form-control form-control-solid" @empty($user) required @endempty>
        </div>

        <div class="mb-3">
            <label class="form-label">Şifre (Tekrar)</label>
            <input type="password" name="password_confirmation" class="form-control form-control-solid" @empty($user) required @endempty>
        </div>

        <div class="mb-3">
            <label class="form-label">Roller</label>
            <select name="roles[]" class="form-select form-select-solid" data-control="select2" multiple>
                @foreach(($roles ?? []) as $name => $label)
                    <option value="{{ $name }}" @selected(collect(old('roles', $userRoles ?? []))->contains($name))>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="modal-footer d-flex justify-content-between">
        <div class="d-flex flex-start align-items-center">
            <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                <input class="form-check-input" type="checkbox" value="1" name="durum" id="durum" {{ isset($user) && $user->durum == 1 ? 'checked' : '' }}>
            </div>
            <label for="" class="inline-label ms-2">Durum</label>
        </div>
        <div>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
            <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Güncelle' : 'Kaydet' }}</button>
        </div>
    </div>
</form>
