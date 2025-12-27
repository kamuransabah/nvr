<div class="separator mb-10"></div>
<form method="POST" action="{{ $action }}"  class="form">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="d-flex flex-column me-n7 pe-7">

        <div class="fv-row mb-10">
            <label class="fs-5 fw-bold form-label mb-2">
                <span class="required">Rol Adı</span>
            </label>
            <input class="form-control form-control-solid" placeholder="Bir rol ismi girin" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" />
        </div>
        <div class="fv-row">
            <label class="fs-5 fw-bold form-label mb-2">Rol İzinleri</label>
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <tbody class="text-gray-600 fw-semibold">
                @foreach($permissionGroups as $group => $permissions)
                    <tr>
                        <td class="text-gray-800">{{ $group }}</td>
                        <td>
                            <div class="d-flex flex-wrap">
                                @foreach($permissions as $permission)
                                    @php
                                        $parts = explode('.', $permission);
                                        $label = end($parts);
                                        $entity = ucfirst(str_replace('_', ' ', $parts[0]));
                                        $labelMap = [
                                            'view' => 'Görüntüle',
                                            'add' => 'Ekle',
                                            'edit' => 'Düzenle',
                                            'delete' => 'Sil',
                                            'update' => 'Güncelle',
                                            'create' => 'Oluştur',
                                            'reply' => 'Yanıtla',
                                            'export' => 'Dışa Aktar'
                                        ];
                                        $translated = $labelMap[$label] ?? ucfirst($label);
                                    @endphp
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20 mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission }}"
                                            {{ in_array($permission, $rolePermissions ?? []) ? 'checked' : '' }} />
                                        <span class="form-check-label">{{ $translated }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center pt-15">
        <a href="{{ route(config('system.admin_prefix').'.roles.index') }}" class="btn btn-light me-3">İptal</a>
        <button type="submit" class="btn btn-primary">
            <span class="indicator-label">Kaydet</span>
            <span class="indicator-progress">Lütfen bekleyin...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>
</form>
