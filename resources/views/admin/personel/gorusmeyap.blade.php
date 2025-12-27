@php use Carbon\Carbon; @endphp
@extends(theme_view('admin', 'layouts.main'))

@section('title', 'Data Görüşme Listesi')
@section('css')
    <link rel="stylesheet" href="{{ theme_asset('admin', 'plugins/custom/datatables/datatables.bundle.css') }}" />
@endsection

@section('toolbar')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center me-3 flex-wrap lh-1">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-gray-900 fw-bold my-1 fs-3">Data Görüşmeler</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">Data Görüşme Listesi</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                <!--begin::Menu wrapper-->
                <div class="me-4">
                    <a href="{{ route(config('system.personel_prefix').'.data.index') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-list me-2"></i>Data Listesi</a>
                </div>
                <!--end::Dropdown wrapper-->

            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="bg-danger border rounded shadow-sm flex-center mb-3">
                <div id="stopwatch" class="bg-primary-o-20 display-1 text-white font-weight-bold text-center p-10">00:00:00</div>
            </div>
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->
                <div class="card-header h-auto py-4">
                    <div class="card-title">
                        <h3 class="card-label">Data Bilgileri</h3>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-4">
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Ürün:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder"><?=$data_bilgileri->urun_adi?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">İsim:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder"><?=$data_bilgileri->isim?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Şehir:</label>
                        <div class="col-8">
                            <span
                                class="form-control-plaintext font-weight-bolder"><?=empty($data_bilgileri->sehir) || is_null($data_bilgileri->sehir) ? '-' : $data_bilgileri->sehir?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">E-Posta:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder"><?=$data_bilgileri->eposta?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Telefon:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder"><?=$data_bilgileri->telefon?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Kaynak:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder">
                                <?=empty($data_bilgileri->kaynak) || is_null($data_bilgileri->dataKaynak)
                                    ? 'CRM'
                                    : $data_bilgileri->dataKaynak->value;?>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Atama Tarihi:</label>
                        <div class="col-8">
                            <span
                                class="form-control-plaintext font-weight-bolder"><?=Carbon::parse($data_bilgileri->atama_tarihi)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm')?></span>
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Başvuru Tarihi:</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder"><?=Carbon::parse($data_bilgileri->tarih)->locale('tr_TR')->isoFormat('D MMMM YYYY HH:mm')?></span>
                        </div>
                    </div>


                </div>
                <!--end::Body-->
                <!--begin::Footer-->
                <div class="card-footer">
                    <a href="#" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#smsgonder"><i class="fa-solid fa-comment-sms fs-3 me-2"></i>SMS Gönder</a>
                    <a href="#" class="btn btn-info font-weight-bold" data-toggle="modal" data-target="#epostagonder"><i class="fa-regular fa-envelope fs-3 me-2"></i>E-Posta Gönder</a>
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Card-->
            @if(!empty($data_bilgileri->personel_notu))
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Notlar</h3>
                    </div>
                    <div class="card-body">
                        {{ $data_bilgileri->personel_notu }}
                    </div>
                </div>
            @endif


        </div>
        <div class="col-xl-8">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            GÖRÜŞME SONUCU
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <form action="{{ route(config('system.personel_prefix').'.data.cevapsiz', $data_bilgileri->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Bu kişiyi cevapsız olarak işaretlemek istiyor musunuz?')">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm d-block mt-3 w-100"><i class="fa-solid fa-phone-slash me-2"></i>Cevapsız</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="h-200px">
                                <div class="bg-light-success d-flex flex-center rounded w-150px h-150px">
                                    <i class="far fa-smile text-success fs-5x"></i>
                                </div>
                                <div class="w-150px h-150px">
                                    <a href="#" class="btn btn-success btn-sm d-block mt-3" data-open-gorusme="5" id="linkKayit">Satış</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-200px">
                                <div class="bg-light-danger d-flex flex-center rounded w-150px h-150px">
                                    <i class="far fa-frown text-danger fs-5x"></i>
                                </div>
                                <div class="w-150px h-150px">
                                    <a href="#" class="btn btn-danger btn-sm d-block mt-3" data-open-gorusme="3" id="linkOlumsuz">Olumsuz</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-200px">
                                <div class="bg-light-primary d-flex flex-center rounded w-150px h-150px">
                                    <i class="far fa-calendar-alt text-primary fs-5x"></i>
                                </div>
                                <div class="w-150px h-150px">
                                    <a href="#" class="btn btn-primary btn-sm d-block mt-3" data-open-gorusme="6" id="linkRandevu">Randevu</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-200px">
                                <div class="bg-light-info d-flex flex-center rounded w-150px h-150px">
                                    <i class="fa-solid fa-clipboard-check text-info fs-5x"></i>
                                </div>
                                <div class="w-150px h-150px">
                                    <a href="#" class="btn btn-info btn-sm d-block mt-3" data-open-gorusme="2" id="linkTeklif">Teklif</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- start:form -->
            <div id="islemCard" class="card shadow-sm mt-5 {{ old('sonuc') ? '' : 'd-none' }}">
                <div class="card-header">
                    <h3 class="card-title" id="cardTitle">
                        {{ old('sonuc') ? '' : 'İşlem' }}
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route(config('system.personel_prefix').'.data.index') }}" class="btn btn-sm btn-light-danger">
                            Görüşmeyi İptal Et
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="post"
                          action="{{ route(config('system.personel_prefix').'.data.gorusmeyap.store', $data_bilgileri->id) }}"
                          id="gorusmeForm" class="mt-5" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="urun_id" value="{{ $data_bilgileri->urun_id }}">
                        <input type="hidden" name="sonuc" id="sonuc" value="{{ old('sonuc') }}">

                        <div id="satisGroup" class="mb-4 d-none">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Ürün</label>
                                    <select name="satis_urun_id" class="form-select" data-placeholder="Ürün seçiniz">
                                        <option value="">Seçiniz…</option>
                                        @foreach($urunler as $u)
                                            <option value="{{ $u->id }}"
                                                @selected(old('satis_urun_id', $data_bilgileri->urun_id) == $u->id)>
                                                {{ $u->urun_adi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Satış Tutarı</label>
                                    <input type="number" step="0.01" min="0" name="satis_tutari"
                                           class="form-control" value="{{ old('satis_tutari') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sigorta Şirketi</label>
                                    <select name="sirket_id" class="form-select" data-placeholder="Şirket seçiniz">
                                        <option value="">Seçiniz…</option>
                                        @foreach($sigortaSirketleri as $s)
                                            <option value="{{ $s->id }}" @selected(old('sirket_id') == $s->id)>{{ $s->ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Poliçe PDF (zorunlu)</label>
                                    <input type="file" name="police_dosya" accept="application/pdf" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Poliçe Başlangıç</label>
                                    <input type="date" name="police_baslangic" class="form-control" value="{{ old('police_baslangic') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Poliçe Bitiş</label>
                                    <input type="date" name="police_bitis" class="form-control" value="{{ old('police_bitis') }}">
                                </div>
                            </div>
                        </div>


                        <div id="randevuGroup" class="mb-4 d-none">
                            <label class="form-label">Randevu Tarihi</label>
                            <input type="datetime-local" name="randevu_tarihi" class="form-control" value="{{ old('randevu_tarihi') }}">
                        </div>

                        <div id="olumsuzGroup" class="mb-4 d-none">
                            <label class="form-label">Olumsuz Sebebi</label>
                            <select class="form-select" name="olumsuz_id">
                                <option value="">Seçiniz…</option>
                                @foreach($olumsuzNedenler as $item)
                                    <option value="{{ $item->id }}" @selected(old('olumsuz_id')==$item->id)>{{ $item->isim }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="teklifGroup" class="mb-4 d-none">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Ürün</label>
                                    <select name="teklif_urun_id" class="form-select" data-control="select2" data-placeholder="Ürün seçiniz">
                                        <option value="">Seçiniz…</option>
                                        @foreach($urunler as $u)
                                            <option value="{{ $u->id }}"
                                                @selected(old('teklif_urun_id', $data_bilgileri->urun_id) == $u->id)>
                                                {{ $u->urun_adi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teklif Tutarı</label>
                                    <input type="number" step="0.01" min="0" name="teklif_tutari"
                                           class="form-control" value="{{ old('teklif_tutari') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teklif PDF (zorunlu)</label>
                                    <input type="file" name="dosya" accept="application/pdf" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Son Tarih</label>
                                    <input type="date" name="son_tarih" class="form-control" value="{{ old('son_tarih') }}">
                                </div>
                            </div>
                        </div>


                        <div class="mb-4">
                            <label class="form-label">Personel Notu (opsiyonel)</label>
                            <textarea class="form-control" name="personel_notu" rows="3">{{ old('personel_notu') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>

            </div>
            <!-- end:form -->

            <!-- geçmiş görüşmeler -->
            <div class="card shadow-sm mt-6">
                <div class="card-header">
                    <h3 class="card-title">Geçmiş Görüşmeler</h3>
                </div>

                <div class="card-body p-0">
                    @forelse($gecmis as $g)
                        @php
                            // Tür & rozet
                            $tur = $g->kayit ? 'Kayıt' : ($g->randevu_id ? 'Randevu' : ($g->olumsuz_id ? 'Olumsuz' : 'Görüşme'));
                            $badgeClass = match($tur){
                                'Kayıt'   => 'badge-light-success',
                                'Randevu' => 'badge-light-info',
                                'Olumsuz' => 'badge-light-warning',
                                default   => 'badge-light-secondary'
                            };

                            // Ürün adı (varsa map ile)
                            $urunAdi = isset($urunMap) ? ($urunMap[$g->urun_id] ?? null) : null;

                            // Olumsuz isim (ilişki yoksa map'ten)
                            $olumsuzIsim = isset($olumsuzMap) && $g->olumsuz_id ? ($olumsuzMap[$g->olumsuz_id] ?? null) : ($g->olumsuzNedeni->value ?? null);

                            // Randevu tarihi
                            $randevuTarih = $g->randevu?->randevu_tarihi ? \Carbon\Carbon::parse($g->randevu->randevu_tarihi)->format('d.m.Y H:i') : null;
                        @endphp

                        <div class="border-bottom px-5 py-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-7 text-muted">
                                        {{ $g->created_at?->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                                <div class="fs-7 text-gray-700">
                                    <i class="bi bi-person-badge me-1"></i> {{ $g->personel->name ?? '—' }}
                                </div>
                            </div>

                            <!-- Orta satır: Ürün + (varsa) Olumsuz Sebep + (varsa) Randevu Tarihi -->
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                                <span class="badge {{ $badgeClass }}">{{ $tur }}</span>

                                @if($olumsuzIsim)
                                    <span class="badge badge-light-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i> {{ $olumsuzIsim }}
                                    </span>
                                @endif

                                @if($randevuTarih)
                                    <span class="badge badge-light-info">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $randevuTarih }}
                                    </span>
                                @endif
                            </div>

                            <!-- Not: tam genişlik -->
                            @if(!empty($g->personel_notu))
                                <div class="mt-3">
                                    <div class="p-3 bg-light rounded">
                                        <div class="fw-semibold text-gray-700">Personel Notu</div>
                                        <div class="text-gray-800 mt-1" style="white-space: pre-wrap;">{{ $g->personel_notu }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-muted">Henüz görüşme kaydı yok.</div>
                    @endforelse
                </div>

                @if($gecmis->hasPages())
                    <div class="card-footer">
                        {{ $gecmis->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>

            <!-- end:geçmiş görüşmeler -->

        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('common/js/jquery.stopwatch.js') }}"></script>
    <script>
        (function($){
            $('#stopwatch').stopwatch({format: '{Minutes} and {s.}'}).stopwatch('start');
        })(jQuery);
    </script>

    <script>
        (function(){
            const form = document.getElementById('gorusmeForm');
            const islemCard = document.getElementById('islemCard');
            const cardTitle = document.getElementById('cardTitle');
            const sonucInput = document.getElementById('sonuc');

            const randevuGroup = document.getElementById('randevuGroup');
            const olumsuzGroup = document.getElementById('olumsuzGroup');
            const teklifGroup  = document.getElementById('teklifGroup');
            const satisGroup   = document.getElementById('satisGroup');

            const titles = {
                '2':'Teklif Oluştur',
                '3':'Olumsuz Bildirimi',
                '5':'Satış Oluştur',
                '6':'Randevu Oluştur'
            };

            function req(name,on){
                const el = form.querySelector(`[name="${name}"]`);
                if(!el) return;
                on ? el.setAttribute('required','required') : el.removeAttribute('required');
            }

            function prepareForm(val){
                sonucInput.value = val;

                const isTeklif  = (val==='2');
                const isOlumsuz = (val==='3');
                const isSatis   = (val==='5');
                const isRandevu = (val==='6');

                teklifGroup.classList.toggle('d-none', !isTeklif);
                olumsuzGroup.classList.toggle('d-none', !isOlumsuz);
                satisGroup.classList.toggle('d-none', !isSatis);
                randevuGroup.classList.toggle('d-none', !isRandevu);

                // Teklif gereksinimleri
                req('teklif_urun_id', isTeklif);
                req('teklif_tutari', isTeklif);
                req('dosya',         isTeklif);
                req('son_tarih',     isTeklif);

                // Satış gereksinimleri
                req('satis_urun_id',   isSatis);
                req('satis_tutari',    isSatis);
                req('sirket_id',       isSatis);
                req('police_dosya',    isSatis);
                req('police_baslangic',isSatis);
                req('police_bitis',    isSatis);

                // Olumsuz / Randevu
                req('olumsuz_id',      isOlumsuz);
                req('randevu_tarihi',  isRandevu);

                cardTitle.textContent = titles[val] || 'İşlem';
                islemCard.classList.remove('d-none');
                islemCard.scrollIntoView({behavior:'smooth', block:'start'});
            }

            document.addEventListener('click', function(e){
                const a = e.target.closest('[data-open-gorusme]');
                if(!a) return;
                e.preventDefault();
                prepareForm(a.getAttribute('data-open-gorusme')); // 2|3|5|6
            });

            // geri dönüşte old() ile açma
            (function initFromOld(){ const v = "{{ old('sonuc') }}"; if(v) prepareForm(v); })();
        })();
    </script>



@endsection
