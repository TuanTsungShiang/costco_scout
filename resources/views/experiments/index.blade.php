@extends('layouts.app')
@section('title', '追蹤紀錄')

@section('content')
<div class="head">
  <h3>試賣追蹤紀錄</h3>
</div>

<div class="panel">
  <div class="panel-body p-0">
    @if($experiments->isEmpty())
      <div class="p-4" style="color:var(--panel-muted);font-size:.88rem">尚無實驗紀錄。</div>
    @else
      <table class="tbl">
        <thead>
          <tr>
            <th>商品</th>
            <th>通路</th>
            <th class="num">進貨</th>
            <th class="num">上架</th>
            <th class="num">已售</th>
            <th class="num">進貨總價</th>
            <th class="num">實際淨利</th>
            <th>狀態</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($experiments as $exp)
          @php $a = $exp->resaleAnalysis @endphp
          <tr>
            <td class="name">
              {{ $a->canonicalProduct->name }}
              <small>{{ $a->canonicalProduct->brand }}</small>
            </td>
            <td>{{ $a->salesChannel->name }}</td>
            <td class="num mono">{{ $exp->quantity_purchased }}</td>
            <td class="num mono">{{ $exp->quantity_listed }}</td>
            <td class="num mono">{{ $exp->quantity_sold }}</td>
            <td class="num mono">{{ $exp->purchase_total_minor ? number_format($exp->purchase_total_minor) : '—' }}</td>
            <td class="num mono <?= ($exp->actual_net_profit_minor ?? 0) >= 0 ? 'c-green' : 'c-red' ?>">
              {{ $exp->actual_net_profit_minor !== null ? number_format($exp->actual_net_profit_minor) : '—' }}
            </td>
            <td><span class="pill test">{{ $exp->status->label() }}</span></td>
            <td style="text-align:right"><a class="btn sm" href="{{ route('experiments.edit', $exp) }}">更新</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-3">{{ $experiments->links() }}</div>
    @endif
  </div>
</div>
@endsection
