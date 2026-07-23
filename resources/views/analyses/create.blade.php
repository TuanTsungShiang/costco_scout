@extends('layouts.app')
@section('title', '新增分析')

@section('content')
<div class="head">
  <div>
    <h3>套利試算</h3>
    <p style="color:var(--chrome-fg-muted);font-size:.85rem;margin-top:4px">{{ $product->brand }} {{ $product->name }}</p>
  </div>
  <a class="btn" href="{{ route('products.show', $product) }}">← 返回</a>
</div>

<div class="panel" style="max-width:680px">
  <div class="panel-head"><h4>設定分析參數</h4></div>
  <div class="panel-body">
    <form method="POST" action="{{ route('analyses.store', $product) }}">
      @csrf
      <div class="fields">
        <div class="f wide">
          <label for="price_observation_id">進貨價格觀測（Costco）</label>
          <select id="price_observation_id" name="price_observation_id" required>
            <option value="">選擇進貨價...</option>
            @foreach($offers as $offer)
              @php $obs = $observationsByOffer[$offer->id] ?? null @endphp
              @if($obs)
                <option value="{{ $obs->id }}" {{ old('price_observation_id') == $obs->id ? 'selected' : '' }}>
                  {{ $offer->retailer->name }} — NT${{ number_format($obs->amount_minor) }}
                  ({{ $obs->created_at->format('Y/m/d') }})
                </option>
              @endif
            @endforeach
          </select>
          @if($observationsByOffer->filter()->isEmpty())
            <span class="f-hint" style="color:var(--red)">無有效進貨價，請先記錄 Costco 價格。</span>
          @endif
        </div>
        <div class="f wide">
          <label for="sales_channel_id">銷售通路</label>
          <select id="sales_channel_id" name="sales_channel_id" required>
            <option value="">選擇通路...</option>
            @foreach($channels as $ch)
              <option value="{{ $ch->id }}" {{ old('sales_channel_id') == $ch->id ? 'selected' : '' }}>
                {{ $ch->name }}
                （平台費 {{ number_format($ch->platform_fee_basis_points/100, 1) }}%
                + 金流 {{ number_format($ch->payment_fee_basis_points/100, 1) }}%）
              </option>
            @endforeach
          </select>
        </div>
        <div class="f">
          <label for="expected_sale_amount">預期售價（TWD，整數）</label>
          <input type="number" id="expected_sale_amount" name="expected_sale_amount" value="{{ old('expected_sale_amount') }}" min="1" required class="mono">
          <span class="f-hint">輸入 1200 = NT$1,200</span>
        </div>
        <div class="f">
          <label for="market_data_status">市場資料信心</label>
          <select id="market_data_status" name="market_data_status" required>
            @foreach($marketStates as $s)
              <option value="{{ $s->value }}" {{ old('market_data_status', 'UNVERIFIED') === $s->value ? 'selected' : '' }}>
                {{ $s->label() }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="note mt-3">
        <strong>信心等級說明：</strong>「未驗證」最高只能獲得「觀察」決定；「人工比價」最高「買一件試賣」；需要「自有銷售紀錄」才能達到「補貨」或「擴大」。
      </div>
      <div class="actions">
        <a class="btn" href="{{ route('products.show', $product) }}">取消</a>
        <button type="submit" class="btn primary">執行試算</button>
      </div>
    </form>
  </div>
</div>
@endsection
