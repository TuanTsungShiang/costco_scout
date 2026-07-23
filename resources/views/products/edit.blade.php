@extends('layouts.app')
@section('title', '編輯：' . $product->name)

@section('content')
<div class="head">
  <h3>編輯商品</h3>
  <a class="btn" href="{{ route('products.show', $product) }}">← 返回</a>
</div>

<div class="panel" style="max-width:720px">
  <div class="panel-head"><h4>{{ $product->brand }} {{ $product->name }}</h4></div>
  <div class="panel-body">
    <form method="POST" action="{{ route('products.update', $product) }}">
      @csrf @method('PUT')
      <div class="fields">
        <div class="f">
          <label for="brand">品牌</label>
          <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" required>
        </div>
        <div class="f">
          <label for="name">商品名稱</label>
          <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="f">
          <label for="gtin">GTIN / 條碼</label>
          <input type="text" id="gtin" name="gtin" value="{{ old('gtin', $product->gtin) }}" class="mono">
        </div>
        <div class="f">
          <label for="comparison_mode">比較模式</label>
          <select id="comparison_mode" name="comparison_mode" required>
            @foreach($modes as $mode)
              <option value="{{ $mode->value }}" {{ old('comparison_mode', $product->comparison_mode->value) === $mode->value ? 'selected' : '' }}>
                {{ $mode->label() }} ({{ $mode->value }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="f">
          <label for="package_count">件數</label>
          <input type="number" id="package_count" name="package_count" value="{{ old('package_count', $product->package_count) }}" min="1" required>
        </div>
        <div class="f">
          <label for="content_per_package">每件內容量</label>
          <input type="number" id="content_per_package" name="content_per_package" value="{{ old('content_per_package', $product->content_per_package) }}" min="1" required>
        </div>
        <div class="f">
          <label for="content_unit">內容單位</label>
          <input type="text" id="content_unit" name="content_unit" value="{{ old('content_unit', $product->content_unit) }}" maxlength="10" required>
        </div>
        <div class="f">
          <label for="comparison_quantity">比較基準量</label>
          <input type="number" id="comparison_quantity" name="comparison_quantity" value="{{ old('comparison_quantity', $product->comparison_quantity) }}" min="1" required>
        </div>
        <div class="f">
          <label for="comparison_unit">比較單位</label>
          <input type="text" id="comparison_unit" name="comparison_unit" value="{{ old('comparison_unit', $product->comparison_unit) }}" maxlength="10" required>
        </div>
        <div class="f wide">
          <label for="notes">備註</label>
          <textarea id="notes" name="notes">{{ old('notes', $product->notes) }}</textarea>
        </div>
      </div>
      <div class="actions">
        <a class="btn" href="{{ route('products.show', $product) }}">取消</a>
        <button type="submit" class="btn primary">儲存更新</button>
      </div>
    </form>
  </div>
</div>
@endsection
