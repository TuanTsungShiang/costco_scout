@extends('layouts.app')
@section('title', '總覽')

@section('content')
@php
  $featured   = $latestAnalyses->first();
  $featuredObs = $featured?->purchasePriceObservation;
@endphp

{{-- ── 最新判定 ── --}}
<div class="head">
  <div>
    <h3>今日判定</h3>
    <p>最新分析。單位價已正規化為每比較單元。</p>
  </div>
  @if($featured)
    @php $d = $featured->decision @endphp
    <span class="pill {{ strtolower(str_replace(['_ONE_UNIT','_'],['','-'],$d->value)) }}">{{ $d->label() }}</span>
  @endif
</div>

@if($featured && $featuredObs)
<div class="faceoff">
  {{-- 左：貨架牌 + verdict bar --}}
  <div>
    <article class="tag checked">
      <div class="tag-head">
        <span class="tag-item mono">{{ $featured->canonicalProduct->brand }}</span>
        <span class="src store">賣場</span>
      </div>
      <h4 class="tag-name">{{ $featured->canonicalProduct->name }}</h4>
      <p class="tag-sub">
        {{ $featured->canonicalProduct->package_count }}×{{ $featured->canonicalProduct->content_per_package }}{{ $featured->canonicalProduct->content_unit }}
        @if($featured->canonicalProduct->canComputeUnitPrice())
          · 每{{ $featured->canonicalProduct->comparison_quantity }}{{ $featured->canonicalProduct->comparison_unit }} 比較
        @endif
      </p>
      <div class="tag-price">
        <span class="cur">$</span>
        <span class="int">{{ number_format($featuredObs->amount_minor) }}</span>
        <span class="cent">00</span>
      </div>
      @if($featured->canonicalProduct->canComputeUnitPrice())
      @php
        $unitPrice = $featured->canonicalProduct->totalContentAmount() > 0
          ? round($featuredObs->amount_minor * $featured->canonicalProduct->comparison_quantity / $featured->canonicalProduct->totalContentAmount(), 2)
          : null;
      @endphp
      @if($unitPrice)
      <div class="tag-unit">
        <span>單位價</span>
        <span>${{ number_format($unitPrice, 2) }} / {{ $featured->canonicalProduct->comparison_unit }}</span>
      </div>
      @endif
      @endif
      <p class="tag-foot">{{ $featuredObs->created_at->format('Y-m-d') }} · {{ $featuredObs->source_type->value }}</p>
    </article>

    <div class="verdict-bar">
      @php
        $profit = $featured->estimated_net_profit_minor;
        $profitable = $profit >= 0;
      @endphp
      <span class="verdict {{ $profitable ? 'costco' : 'hold' }}">
        <small>判定</small>
        <strong>{{ $featured->decision->label() }}</strong>
      </span>
      <div>
        <p class="save">{{ $profitable ? '+' : '' }}NT${{ number_format($profit) }}</p>
        <p>
          預期售價 NT${{ number_format($featured->expected_sale_amount_minor) }}（{{ $featured->salesChannel->name }}）。
          ROI {{ number_format($featured->roiPercent(), 1) }}%，市場信心：{{ $featured->market_data_status->label() }}。
        </p>
      </div>
    </div>
  </div>

  {{-- 右：近期分析列表 --}}
  <div>
    <div class="ch-list">
      @foreach($latestAnalyses->take(6) as $a)
      @php $obs2 = $a->purchasePriceObservation; $isFirst = $loop->first; @endphp
      <a href="{{ route('analyses.show', $a) }}" class="ch-row ch-blue {{ $isFirst ? 'best' : '' }}" style="text-decoration:none">
        @if($isFirst)<span class="ch-rank">最新</span>@endif
        <span class="ch-thumb">
          {{ mb_substr($a->salesChannel->name,0,4) }}
        </span>
        <div>
          <div class="ch-name">
            <strong>{{ $a->canonicalProduct->name }}</strong>
            <span class="ch-badge">{{ $a->salesChannel->name }}</span>
          </div>
          <p class="ch-meta">
            <span>進價 {{ $obs2 ? number_format($obs2->amount_minor) : '—' }}</span>
            <span>售 {{ number_format($a->expected_sale_amount_minor) }}</span>
            <span>{{ $a->market_data_status->label() }}</span>
          </p>
        </div>
        <div class="ch-money">
          @php $pos = $a->estimated_net_profit_minor >= 0 @endphp
          <b class="{{ $pos ? 'r-row' : '' }}" style="display:block;font-family:var(--f-display);font-weight:900;font-size:1.1rem;font-variant-numeric:tabular-nums;color:{{ $pos ? 'var(--green)' : 'var(--red)' }}">
            {{ $pos ? '+' : '' }}{{ number_format($a->estimated_net_profit_minor) }}
          </b>
          <small style="display:block;margin-top:3px">
            <span class="pill {{ strtolower(str_replace(['_ONE_UNIT','_'],['','-'],$a->decision->value)) }}">{{ $a->decision->label() }}</span>
          </small>
        </div>
      </a>
      @endforeach
      @if($latestAnalyses->isEmpty())
        <div class="panel-body" style="color:var(--panel-muted);font-size:.88rem;padding:16px">
          尚無分析。<a href="{{ route('price-input') }}">從價格輸入開始</a>。
        </div>
      @endif
    </div>
  </div>
</div>
@else
<div class="panel" style="padding:32px;text-align:center;color:var(--panel-muted)">
  <p style="font-size:1rem;font-weight:700;margin-bottom:8px">還沒有任何分析</p>
  <a class="btn primary" href="{{ route('price-input') }}">
    <svg width="15" height="15" aria-hidden="true"><use href="#i-plus"></use></svg>
    開始輸入價格
  </a>
</div>
@endif

{{-- ── 資料摘要 ── --}}
<div class="head">
  <div><h3>資料摘要</h3></div>
</div>

<div class="strip" style="margin-bottom:var(--gap)">
  <div>
    <small>已建立商品</small>
    <strong>{{ $stats['total_products'] }}</strong>
    <em>已建檔</em>
  </div>
  <div class="mk-blue">
    <small>分析紀錄</small>
    <strong>{{ $stats['total_analyses'] }}</strong>
    <em>試算次數</em>
  </div>
  <div>
    <small>進行中實驗</small>
    <strong>{{ $stats['active_experiments'] }}</strong>
    <em>待售出</em>
  </div>
  <div class="mk-green">
    <small>獲利分析</small>
    <strong>{{ $stats['profitable_count'] }}</strong>
    <em>淨利 &gt; 0</em>
  </div>
</div>

{{-- ── 商品列表 + 完整度 ── --}}
<div class="head">
  <div>
    <h3>商品列表</h3>
    <p>依最近分析排序。</p>
  </div>
  <a class="btn" href="{{ route('products.index') }}">查看全部</a>
</div>

<div class="cols">
  <div class="panel">
    <div class="panel-body" style="padding:0;overflow-x:auto">
      <table class="tbl">
        <thead>
          <tr>
            <th>商品</th>
            <th class="num col-sm">進貨價</th>
            <th class="num col-sm">預期售價</th>
            <th class="num">預估淨利</th>
            <th>判定</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestAnalyses as $a)
          @php $obs3 = $a->purchasePriceObservation @endphp
          <tr onclick="location.href='{{ route('analyses.show', $a) }}'" style="cursor:pointer">
            <td class="name">
              {{ $a->canonicalProduct->name }}
              <small>{{ $a->canonicalProduct->brand }} · {{ $a->salesChannel->name }}</small>
            </td>
            <td class="num mono col-sm">{{ $obs3 ? number_format($obs3->amount_minor) : '—' }}</td>
            <td class="num mono col-sm">{{ number_format($a->expected_sale_amount_minor) }}</td>
            <td class="num mono {{ $a->estimated_net_profit_minor >= 0 ? 'win' : 'lose' }}">
              {{ $a->estimated_net_profit_minor >= 0 ? '+' : '' }}{{ number_format($a->estimated_net_profit_minor) }}
            </td>
            <td><span class="pill {{ strtolower(str_replace(['_ONE_UNIT','_'],['','-'],$a->decision->value)) }}">{{ $a->decision->label() }}</span></td>
          </tr>
          @empty
          <tr><td colspan="5" style="color:var(--panel-muted);text-align:center;padding:24px">尚無資料</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h4>資料完整度</h4>
    </div>
    <div class="panel-body">
      @php
        $total   = max($stats['total_products'], 1);
        $mapPct  = $stats['total_analyses'] > 0 ? min(100, round($stats['profitable_count'] / $stats['total_analyses'] * 100)) : 0;
        $expPct  = $stats['total_analyses'] > 0 ? min(100, round(($stats['total_analyses'] - $stats['active_experiments']) / $stats['total_analyses'] * 100)) : 0;
      @endphp
      <div class="bar-row">
        <div class="bar-top"><span>已有分析的商品</span><b class="num">{{ $stats['total_analyses'] }}</b></div>
        <div class="bar"><i style="width:{{ min(100, $stats['total_analyses'] / max($stats['total_products'],1) * 100) }}%"></i></div>
      </div>
      <div class="bar-row">
        <div class="bar-top"><span>獲利率（淨利 &gt; 0）</span><b class="num">{{ $mapPct }}%</b></div>
        <div class="bar"><i class="{{ $mapPct > 60 ? 'ok' : ($mapPct > 30 ? 'warn' : '') }}" style="width:{{ $mapPct }}%"></i></div>
      </div>
      <div class="bar-row">
        <div class="bar-top"><span>進行中實驗</span><b class="num">{{ $stats['active_experiments'] }}</b></div>
        <div class="bar"><i class="warn" style="width:{{ min(100, $stats['active_experiments'] * 10) }}%"></i></div>
      </div>
      <p class="note">電商售價隨促銷活動變動頻繁，建議每次進 Costco 前重新確認市場報價。</p>
    </div>
  </div>
</div>
@endsection
