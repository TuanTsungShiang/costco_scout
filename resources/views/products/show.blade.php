@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="head">
  <div>
    <h3>{{ $product->brand }} {{ $product->name }}</h3>
    <p style="color:var(--chrome-fg-muted);font-size:.85rem;margin-top:4px">
      {{ $product->comparison_mode->label() }} ·
      {{ $product->package_count }}×{{ $product->content_per_package }}{{ $product->content_unit }} ·
      每{{ $product->comparison_quantity }}{{ $product->comparison_unit }} 比較
      @if($product->gtin)· <span class="mono" style="font-size:.78rem">{{ $product->gtin }}</span>@endif
    </p>
  </div>
  <div style="display:flex;gap:8px">
    <a class="btn sm" href="{{ route('products.edit', $product) }}">編輯</a>
    <a class="btn sm blue" href="{{ route('analyses.create', $product) }}">＋ 新增分析</a>
  </div>
</div>

{{-- Price observations per offer --}}
<div class="head" style="margin-top:24px"><h3 style="font-size:.95rem">通路價格</h3></div>
<div class="panel mb-3">
  <div class="panel-body p-0">
    @if($product->productOffers->isEmpty())
      <div class="p-3" style="color:var(--panel-muted);font-size:.88rem">尚無通路紀錄。</div>
    @else
      <table class="tbl">
        <thead>
          <tr>
            <th>通路</th>
            <th>商品編號</th>
            <th class="num">最新有效價（TWD）</th>
            <th class="num">觀測時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($product->productOffers as $offer)
            @php $obs = $offer->priceObservations->first() @endphp
            <tr>
              <td><strong>{{ $offer->retailer->name }}</strong></td>
              <td class="mono" style="font-size:.78rem">{{ $offer->external_product_id ?? '—' }}</td>
              <td class="num mono">
                @if($obs)
                  {{ number_format($obs->amount_minor) }}
                @else
                  <span style="color:var(--panel-muted)">無資料</span>
                @endif
              </td>
              <td class="num" style="font-size:.78rem;color:var(--panel-muted)">
                {{ $obs ? $obs->created_at->format('Y/m/d') : '—' }}
              </td>
              <td style="text-align:right">
                <a class="btn sm" href="{{ route('observations.create', $offer) }}">記錄價格</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>

{{-- Analyses --}}
<div class="head"><h3 style="font-size:.95rem">近期分析</h3></div>
<div class="panel">
  <div class="panel-body p-0">
    @if($product->resaleAnalyses->isEmpty())
      <div class="p-3" style="color:var(--panel-muted);font-size:.88rem">
        尚無分析。<a href="{{ route('analyses.create', $product) }}">立即新增</a>
      </div>
    @else
      <table class="tbl">
        <thead>
          <tr>
            <th>銷售通路</th>
            <th class="num">預期售價</th>
            <th class="num">淨利</th>
            <th class="num">ROI</th>
            <th>市場信心</th>
            <th>判定</th>
            <th>時間</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($product->resaleAnalyses as $a)
          <tr>
            <td>{{ $a->salesChannel->name }}</td>
            <td class="num mono">{{ number_format($a->expected_sale_amount_minor) }}</td>
            <td class="num mono" style="color:{{ $a->estimated_net_profit_minor >= 0 ? 'var(--green)' : 'var(--red)' }}">
              {{ number_format($a->estimated_net_profit_minor) }}
            </td>
            <td class="num mono">{{ number_format($a->roiPercent(), 1) }}%</td>
            <td style="font-size:.78rem">{{ $a->market_data_status->label() }}</td>
            <td><span class="pill {{ strtolower(str_replace('_','',$a->decision->value)) }}">{{ $a->decision->label() }}</span></td>
            <td style="font-size:.78rem;color:var(--panel-muted)">{{ $a->analyzed_at->format('m/d') }}</td>
            <td style="text-align:right">
              <a class="btn sm" href="{{ route('analyses.show', $a) }}">詳情</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection
