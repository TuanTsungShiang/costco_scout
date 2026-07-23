@extends('layouts.app')
@section('title', '商品管理')

@section('content')
<div class="head">
  <h3>商品管理</h3>
  <a class="btn-add" href="{{ route('products.create') }}">＋ 新增商品</a>
</div>

<div class="panel">
  <div class="panel-body p-0">
    @if($products->isEmpty())
      <div class="p-4" style="color:var(--panel-muted);font-size:.88rem;">尚未建立任何商品。</div>
    @else
      <table class="tbl">
        <thead>
          <tr>
            <th>品牌 / 商品名稱</th>
            <th>比較模式</th>
            <th class="num">規格</th>
            <th class="num">分析次數</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $p)
          <tr>
            <td class="name">
              {{ $p->name }}
              <small>{{ $p->brand }}</small>
            </td>
            <td>
              <span class="pill test">{{ $p->comparison_mode->label() }}</span>
            </td>
            <td class="num mono" style="font-size:.8rem">
              {{ $p->package_count }}×{{ $p->content_per_package }}{{ $p->content_unit }}
            </td>
            <td class="num">{{ $p->resale_analyses_count }}</td>
            <td style="text-align:right">
              <a class="btn sm" href="{{ route('products.show', $p) }}">查看</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-3">{{ $products->links() }}</div>
    @endif
  </div>
</div>
@endsection
