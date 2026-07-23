@extends('layouts.app')
@section('title', '記錄價格')

@section('content')
<div class="head">
  <div>
    <h3>記錄價格觀測</h3>
    <p style="color:var(--chrome-fg-muted);font-size:.85rem;margin-top:4px">
      {{ $offer->retailer->name }} · {{ $offer->canonicalProduct->name }}
    </p>
  </div>
  <a class="btn" href="{{ route('products.show', $offer->canonical_product_id) }}">← 返回商品</a>
</div>

<div class="panel" style="max-width:560px">
  <div class="panel-head"><h4>新增觀測價格</h4><span>所有金額單位：{{ $offer->retailer->stores->first()?->currency_code ?? 'TWD' }} 分（整數）</span></div>
  <div class="panel-body">
    <div class="note mb-3">
      TWD 不含小數：輸入 <strong class="mono">769</strong> 代表 NT$769。若有小數（USD），輸入 <strong class="mono">1999</strong> 代表 $19.99。
    </div>
    <form method="POST" action="{{ route('observations.store', $offer) }}">
      @csrf
      <div class="fields">
        <div class="f">
          <label for="amount_minor">金額（整數，最小單位）</label>
          <input type="number" id="amount_minor" name="amount_minor" value="{{ old('amount_minor') }}" min="1" required class="mono">
        </div>
        <div class="f">
          <label for="currency_code">貨幣</label>
          <select id="currency_code" name="currency_code">
            <option value="TWD" selected>TWD 新台幣</option>
            <option value="USD">USD 美元</option>
            <option value="JPY">JPY 日圓</option>
          </select>
        </div>
        <div class="f">
          <label for="fx_rate_to_base">兌 TWD 匯率 <span class="f-hint">（TWD 留空）</span></label>
          <input type="number" id="fx_rate_to_base" name="fx_rate_to_base" value="{{ old('fx_rate_to_base') }}" step="0.0000000001" class="mono">
        </div>
        <div class="f">
          <label for="source_type">來源</label>
          <select id="source_type" name="source_type" required>
            <option value="MANUAL" {{ old('source_type','MANUAL') === 'MANUAL' ? 'selected' : '' }}>人工輸入</option>
            <option value="PRICE_TAG_OCR" {{ old('source_type') === 'PRICE_TAG_OCR' ? 'selected' : '' }}>價格牌 OCR</option>
            <option value="SCRAPE" {{ old('source_type') === 'SCRAPE' ? 'selected' : '' }}>網頁爬取</option>
            <option value="API" {{ old('source_type') === 'API' ? 'selected' : '' }}>API</option>
          </select>
        </div>
        <div class="f wide">
          <label for="notes">備註</label>
          <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
        </div>
      </div>
      <div class="actions">
        <a class="btn" href="{{ route('products.show', $offer->canonical_product_id) }}">取消</a>
        <button type="submit" class="btn primary">儲存價格</button>
      </div>
    </form>
  </div>
</div>
@endsection
