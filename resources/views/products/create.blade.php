@extends('layouts.app')
@section('title', '新增商品')

@section('content')
<div class="head">
  <h3>新增商品</h3>
  <a class="btn" href="{{ route('products.index') }}">← 返回列表</a>
</div>

@if($defaults['return_to'] === 'price-input')
<div class="note" style="margin-bottom:16px">
  從價格輸入帶入的資料已預填。請確認規格後填上品牌與名稱，建立後會自動跳回。
</div>
@endif

<div class="panel" style="max-width:720px">
  <div class="panel-head"><h4>商品基本資料</h4></div>
  <div class="panel-body">
    <form method="POST" action="{{ route('products.store') }}">
      @csrf
      {{-- 從 price-input 來時保留 return_to --}}
      <input type="hidden" name="return_to" value="{{ $defaults['return_to'] }}">

      <div class="fields">
        <div class="f">
          <label for="brand">品牌</label>
          <input type="text" id="brand" name="brand"
                 value="{{ old('brand', $defaults['brand']) }}"
                 placeholder="例：雀巢、Kirkland" required>
        </div>
        <div class="f">
          <label for="name">商品名稱</label>
          <input type="text" id="name" name="name"
                 value="{{ old('name', $defaults['name']) }}"
                 placeholder="例：EXCELLA 無糖黑咖啡" required>
        </div>
        <div class="f">
          <label for="gtin">GTIN / 條碼 <span class="f-hint">（選填）</span></label>
          <input type="text" id="gtin" name="gtin"
                 value="{{ old('gtin') }}" class="mono">
        </div>
        <div class="f">
          <label for="comparison_mode">比較模式</label>
          <select id="comparison_mode" name="comparison_mode" required>
            <option value="">選擇...</option>
            @foreach($modes as $mode)
              <option value="{{ $mode->value }}"
                {{ (old('comparison_mode', $defaults['comparison_mode'])) === $mode->value ? 'selected' : '' }}>
                {{ $mode->label() }} ({{ $mode->value }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="f">
          <label for="package_count">件數（包裝數量）</label>
          <input type="number" id="package_count" name="package_count"
                 value="{{ old('package_count', $defaults['package_count']) }}"
                 min="1" required>
          <span class="f-hint">例：12瓶裝填 12</span>
        </div>
        <div class="f">
          <label for="content_per_package">每件內容量</label>
          <input type="number" id="content_per_package" name="content_per_package"
                 value="{{ old('content_per_package', $defaults['content_per_package']) }}"
                 min="1" required>
        </div>
        <div class="f">
          <label for="content_unit">內容單位 <span class="f-hint">（G / ML / SHEET / COUNT）</span></label>
          <input type="text" id="content_unit" name="content_unit"
                 value="{{ old('content_unit', $defaults['content_unit']) }}"
                 maxlength="10" placeholder="ML" required>
        </div>
        <div class="f">
          <label for="comparison_quantity">比較基準量</label>
          <input type="number" id="comparison_quantity" name="comparison_quantity"
                 value="{{ old('comparison_quantity', $defaults['comparison_quantity']) }}"
                 min="1" required>
          <span class="f-hint">例：每 100g 比價 → 填 100</span>
        </div>
        <div class="f">
          <label for="comparison_unit">比較單位</label>
          <input type="text" id="comparison_unit" name="comparison_unit"
                 value="{{ old('comparison_unit', $defaults['comparison_unit']) }}"
                 maxlength="10" placeholder="ml" required>
        </div>
        <div class="f wide">
          <label for="notes">備註 <span class="f-hint">（品號、來源等）</span></label>
          <textarea id="notes" name="notes">{{ old('notes', $defaults['notes']) }}</textarea>
        </div>
      </div>
      <div class="actions">
        @if($defaults['return_to'] === 'price-input')
          <a class="btn" href="{{ route('price-input') }}">← 回價格輸入</a>
        @else
          <a class="btn" href="{{ route('products.index') }}">取消</a>
        @endif
        <button type="submit" class="btn primary">建立商品</button>
      </div>
    </form>
  </div>
</div>
@endsection
