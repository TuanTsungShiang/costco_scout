@extends('layouts.app')
@section('title', '更新實驗')

@section('content')
@php $a = $experiment->resaleAnalysis @endphp
<div class="head">
  <div>
    <h3>更新實驗紀錄</h3>
    <p style="color:var(--chrome-fg-muted);font-size:.85rem;margin-top:4px">
      {{ $a->canonicalProduct->name }} · {{ $a->salesChannel->name }}
    </p>
  </div>
  <a class="btn" href="{{ route('analyses.show', $a) }}">← 返回分析</a>
</div>

<div class="panel" style="max-width:680px">
  <div class="panel-head">
    <h4>實驗結果</h4>
    <span>進貨 {{ $experiment->quantity_purchased }} 件，上架 {{ $experiment->quantity_listed }} 件</span>
  </div>
  <div class="panel-body">
    <form method="POST" action="{{ route('experiments.update', $experiment) }}">
      @csrf @method('PATCH')
      <div class="fields">
        <div class="f">
          <label for="status">狀態</label>
          <select id="status" name="status" required>
            @foreach(\App\Enums\ExperimentStatus::cases() as $s)
              <option value="{{ $s->value }}" {{ old('status', $experiment->status->value) === $s->value ? 'selected' : '' }}>
                {{ $s->label() }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="f">
          <label for="quantity_sold">已售件數</label>
          <input type="number" id="quantity_sold" name="quantity_sold" value="{{ old('quantity_sold', $experiment->quantity_sold) }}" min="0" required class="mono">
        </div>
        <div class="f">
          <label for="actual_average_sale_amount_minor">平均售價（TWD 整數）</label>
          <input type="number" id="actual_average_sale_amount_minor" name="actual_average_sale_amount_minor"
            value="{{ old('actual_average_sale_amount_minor', $experiment->actual_average_sale_amount_minor) }}" min="0" class="mono">
        </div>
        <div class="f">
          <label for="actual_platform_fee_minor">實際平台費</label>
          <input type="number" id="actual_platform_fee_minor" name="actual_platform_fee_minor"
            value="{{ old('actual_platform_fee_minor', $experiment->actual_platform_fee_minor) }}" class="mono">
        </div>
        <div class="f">
          <label for="actual_payment_fee_minor">實際金流費</label>
          <input type="number" id="actual_payment_fee_minor" name="actual_payment_fee_minor"
            value="{{ old('actual_payment_fee_minor', $experiment->actual_payment_fee_minor) }}" class="mono">
        </div>
        <div class="f">
          <label for="actual_shipping_minor">實際運費</label>
          <input type="number" id="actual_shipping_minor" name="actual_shipping_minor"
            value="{{ old('actual_shipping_minor', $experiment->actual_shipping_minor) }}" class="mono">
        </div>
        <div class="f">
          <label for="actual_packaging_minor">實際包材費</label>
          <input type="number" id="actual_packaging_minor" name="actual_packaging_minor"
            value="{{ old('actual_packaging_minor', $experiment->actual_packaging_minor) }}" class="mono">
        </div>
        <div class="f">
          <label for="actual_other_cost_minor">其他費用</label>
          <input type="number" id="actual_other_cost_minor" name="actual_other_cost_minor"
            value="{{ old('actual_other_cost_minor', $experiment->actual_other_cost_minor) }}" class="mono">
        </div>
        <div class="f wide">
          <label for="notes">備註</label>
          <textarea id="notes" name="notes">{{ old('notes', $experiment->notes) }}</textarea>
        </div>
      </div>
      <div class="actions">
        <a class="btn" href="{{ route('analyses.show', $a) }}">取消</a>
        <button type="submit" class="btn primary">儲存紀錄</button>
      </div>
    </form>
  </div>
</div>
@endsection
