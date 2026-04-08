@extends('layouts.app')
@section('title', ($query ? 'Hasil: ' . $query : 'Cari Drama') . ' — DramaCina')
@section('content')

<div style="margin-bottom:var(--gap-xl);">
    <h1 class="section-title" style="margin-bottom:var(--gap-lg);">
        @if($query) Hasil pencarian: "{{ $query }}" @else Cari Drama @endif
    </h1>

    {{-- Search Form --}}
    <form action="{{ route('search') }}" method="GET" style="display:flex;gap:var(--gap-sm);max-width:600px;margin-bottom:var(--gap-xl);">
        <div style="flex:1;position:relative;">
            <input type="text" name="q" value="{{ $query }}" placeholder="Cari drama..." class="form-input" style="padding-left:2.5rem;">
            <svg style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </div>
        <select name="provider" class="form-input" style="max-width:140px;">
            <option value="all" {{ $provider === 'all' ? 'selected' : '' }}>Semua Provider</option>
            @foreach(['dramabox'=>'DramaBox','reelshort'=>'ReelShort','shortmax'=>'ShortMax','netshort'=>'NetShort','melolo'=>'Melolo','freereels'=>'FreeReels','dramanova'=>'DramaNova'] as $key => $label)
                <option value="{{ $key }}" {{ $provider === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    @if(empty($query))
        <div class="watchlist-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <p>Masukkan kata kunci untuk mencari drama.</p>
        </div>
    @elseif(empty($results))
        <div class="watchlist-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <h3>Tidak Ditemukan</h3>
            <p>Tidak ada hasil untuk "{{ $query }}".</p>
        </div>
    @else
        @php
            $providerConfig = [
                'dramabox'  => ['label' => 'DramaBox',  'route' => 'dramabox.detail',  'idKey' => 'bookId',       'titleKey' => ['bookName','title','name'],    'thumbKey' => ['cover','thumbnail','coverUrl'],   'badge' => 'badge-dramabox'],
                'reelshort' => ['label' => 'ReelShort', 'route' => 'reelshort.detail', 'idKey' => 'bookId',       'titleKey' => ['bookName','title','name'],    'thumbKey' => ['cover','thumbnail','image'],       'badge' => 'badge-reelshort'],
                'shortmax'  => ['label' => 'ShortMax',  'route' => 'shortmax.detail',  'idKey' => 'shortPlayId',  'titleKey' => ['shortPlayName','title','name'],'thumbKey' => ['cover','thumbnail','coverUrl'],   'badge' => 'badge-shortmax'],
                'netshort'  => ['label' => 'NetShort',  'route' => 'netshort.detail',  'idKey' => 'shortPlayId',  'titleKey' => ['shortPlayName','title','name'],'thumbKey' => ['cover','thumbnail'],              'badge' => 'badge-netshort'],
                'melolo'    => ['label' => 'Melolo',    'route' => 'melolo.detail',    'idKey' => 'bookId',       'titleKey' => ['title','name','bookName'],    'thumbKey' => ['cover','thumbnail','image'],       'badge' => 'badge-melolo'],
                'freereels' => ['label' => 'FreeReels', 'route' => 'freereels.detail', 'idKey' => 'key',          'titleKey' => ['title','name','bookName'],    'thumbKey' => ['cover','thumbnail','image','poster'],'badge' => 'badge-freereels'],
                'dramanova' => ['label' => 'DramaNova', 'route' => 'dramanova.detail', 'idKey' => 'dramaId',      'titleKey' => ['dramaTitle','title','name'],  'thumbKey' => ['cover','thumbnail','image'],       'badge' => 'badge-dramanova'],
            ];
        @endphp

        @foreach($results as $pKey => $pData)
            @php
                $cfg = $providerConfig[$pKey] ?? null;
                if (!$cfg) continue;
                $pItems = data_get($pData, 'data', data_get($pData, 'result', data_get($pData, 'list', [])));
                $pItems = is_array($pItems) ? $pItems : [];
            @endphp
            @if(count($pItems) > 0)
            <section class="section">
                <div class="section-header">
                    <h2 class="section-title">{{ $cfg['label'] }}</h2>
                    <span style="font-size:0.8rem;color:var(--text-muted);">{{ count($pItems) }} hasil</span>
                </div>
                <div class="drama-grid">
                    @foreach($pItems as $item)
                        @php
                            $id = null;
                            foreach ([$cfg['idKey'], 'id', '_id', 'bookId'] as $k) {
                                if (data_get($item, $k)) { $id = data_get($item, $k); break; }
                            }
                            $title = null;
                            foreach ($cfg['titleKey'] as $k) {
                                if (data_get($item, $k)) { $title = data_get($item, $k); break; }
                            }
                            $thumb = null;
                            foreach ($cfg['thumbKey'] as $k) {
                                if (data_get($item, $k)) { $thumb = data_get($item, $k); break; }
                            }
                        @endphp
                        @if($id)
                        <a href="{{ route($cfg['route'], ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                            <div class="drama-card-poster">
                                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy">@endif
                                <div class="overlay"></div>
                                <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                                <span class="provider-badge {{ $cfg['badge'] }}">{{ strtoupper($pKey) }}</span>
                            </div>
                            <div class="drama-card-info">
                                <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif
        @endforeach
    @endif
</div>

@endsection
