@extends('layouts.app')
@section('title', '建立實驗')

@section('content')
<div class="head">
  <div>
    <h3>建立試賣實驗</h3>
    <p style="color:var(--chrome-fg-muted);font-size:.85rem;margin-top:4px">
      {{ $analysis->canonicalProduct->name }} · {{ $analysis->salesChannel->name }}
    </p>
  </div>
  <a class="btn" href="{{ route('analyses.show', $analysis) }}">← 返回分析</a>
</div>

<div class="panel" style="max-width:560px">
  <div class="panel-head">
    <h4>實驗參數</h4>
    <span>預估淨利 {{ number_format($analysis->estimated_net_profit_minor) }} TWD · ROI {{ number_format($analysis->roiPercent(), 1) }}%</span>
  </div>
  <div class="panel-body">
    <form method="POST" action="{{ route('experiments.store', $analysis) }}">
      @csrf
      <div class="fields">
        <div class="f">
          <label for="quantity_purchased">進貨件數</label>
          <input type="number" id="quantity_purchased" name="quantity_purchased" value="{{ old('quantity_purchased', 1) }}" min="1" required class="mono">
        </div>
        <div class="f">
          <label for="quantity_listed">上架件數</label>
          <input type="number" id="quantity_listed" name="quantity_listed" value="{{ old('quantity_listed', 1) }}" min="1" required class="mono">
        </div>
        <div class="f wide">
          <label for="purchase_total_minor">實際進貨總價（TWD，整數）</label>
          <input type="number" id="purchase_total_minor" name="purchase_total_minor" value="{{ old('purchase_total_minor') }}" min="1" required class="mono">
          <span class="f-hint">含運費、稅費等全部實際支出</span>
        </div>
        <div class="f wide">
          <label for="notes">備註</label>
          <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
        </div>
      </div>
      <div class="actions">
        <a class="btn" href="{{ route('analyses.show', $analysis) }}">取消</a>
        <button type="submit" class="btn primary">建立實驗</button>
      </div>
    </form>
  </div>
</div>
@endsection
