@extends('layouts.app')
@section('title', $analysis->canonicalProduct->name . ' — 分析詳情')

@section('content')
@php
  $a       = $analysis;
  $obs     = $a->purchasePriceObservation;
  $profit  = $a->estimated_net_profit_minor;
  $pos     = $profit >= 0;
  $product = $a->canonicalProduct;
@endphp

<div class="head">
  <div>
    <h3>分析詳情</h3>
    <p>實付價明細、套利試算與資料狀態。</p>
  </div>
  <span class="pill {{ strtolower(str_replace('_ONE_UNIT','',str_replace('_','-',$a->decision->value))) }}">
    {{ $a->decision->label() }}
  </span>
</div>

<div class="analysis">

  {{-- ── 左：收據 ── --}}
  <div>
    <div class="receipt checked">
      <div class="receipt-top">
        <strong>套利試算單</strong>
        <span>{{ $product->brand }} {{ $product->name }} · {{ $a->salesChannel->name }} · {{ $a->analyzed_at->format('Y-m-d H:i') }}</span>
      </div>

      <p class="r-sec">買進成本</p>
      <div class="r-row">
        <span>Costco 標價</span>
        <b>NT${{ number_format($obs?->amount_minor ?? 0) }}</b>
      </div>
      @if($a->estimated_membership_reward_minor > 0)
      <div class="r-row">
        <span>會員回饋（Executive）</span>
        <b class="neg">−NT${{ number_format($a->estimated_membership_reward_minor) }}</b>
      </div>
      @endif
      @if($product->canComputeUnitPrice() && $product->totalContentAmount() > 0)
      @php $unitCost = round(($obs?->amount_minor ?? 0) * $product->comparison_quantity / $product->totalContentAmount(), 2) @endphp
      <div class="r-row">
        <span>單位成本</span>
        <b>NT${{ number_format($unitCost, 2) }} / {{ $product->comparison_unit }}</b>
      </div>
      @endif

      <p class="r-sec">銷售費用（{{ $a->salesChannel->name }}）</p>
      <div class="r-row">
        <span>平台手續費</span>
        <b>NT${{ number_format($a->estimated_platform_fee_minor) }}</b>
      </div>
      <div class="r-row">
        <span>金流手續費</span>
        <b>NT${{ number_format($a->estimated_payment_fee_minor) }}</b>
      </div>
      @if($a->estimated_promotion_fee_minor > 0)
      <div class="r-row">
        <span>推廣費</span>
        <b>NT${{ number_format($a->estimated_promotion_fee_minor) }}</b>
      </div>
      @endif
      <div class="r-row">
        <span>運費</span>
        <b>NT${{ number_format($a->estimated_shipping_minor) }}</b>
      </div>
      <div class="r-row">
        <span>包材</span>
        <b>NT${{ number_format($a->estimated_packaging_minor) }}</b>
      </div>
      @if($a->estimated_return_loss_minor > 0)
      <div class="r-row">
        <span>退貨損失估算</span>
        <b>NT${{ number_format($a->estimated_return_loss_minor) }}</b>
      </div>
      @endif

      <div class="r-row sub">
        <span>預期售價</span>
        <b>NT${{ number_format($a->expected_sale_amount_minor) }}</b>
      </div>

      <div class="r-row total">
        <span>預估淨利</span>
        <b class="{{ $pos ? 'pos' : 'neg' }}">
          {{ $pos ? '+' : '−' }}NT${{ number_format(abs($profit)) }}
        </b>
      </div>
      <div class="r-row">
        <span>ROI</span>
        <b>{{ number_format($a->roiPercent(), 2) }}%</b>
      </div>
      <div class="r-row">
        <span>毛利率</span>
        <b>{{ number_format($a->profitMarginPercent(), 2) }}%</b>
      </div>
      <div class="r-row">
        <span>損益平衡售價</span>
        <b>NT${{ number_format($a->break_even_amount_minor) }}</b>
      </div>

      <div class="r-future" aria-hidden="true">
        <p class="r-sec">
          <span>實際實驗結果</span>
          <em>{{ $a->experiments->isNotEmpty() ? '已有 ' . $a->experiments->count() . ' 筆' : '尚無紀錄' }}</em>
        </p>
        @if($a->experiments->isNotEmpty())
        @php $lastExp = $a->experiments->last() @endphp
        <div class="r-row"><span>上架件數</span><b>{{ $lastExp->quantity_listed }}</b></div>
        <div class="r-row"><span>已售件數</span><b>{{ $lastExp->quantity_sold }}</b></div>
        <div class="r-row"><span>實際淨利</span>
          <b>{{ $lastExp->actual_net_profit_minor !== null ? 'NT$'.number_format($lastExp->actual_net_profit_minor) : '—' }}</b>
        </div>
        @else
        <div class="r-row"><span>實際售價</span><b>—</b></div>
        <div class="r-row"><span>實際平台費</span><b>—</b></div>
        <div class="r-row"><span>實際淨利</span><b>—</b></div>
        @endif
      </div>

      <p class="receipt-foot">市場信心：{{ $a->market_data_status->label() }} · 分析時間 {{ $a->analyzed_at->format('Y-m-d') }}</p>
    </div>

    <div style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;align-items:center">
      <span class="verdict {{ in_array($a->decision->value, ['RESTOCK','SCALE']) ? 'online' : ($a->decision->value === 'PASS' ? 'hold' : 'costco') }}" style="flex:0 0 auto">
        <small>判定</small>
        <strong>{{ $a->decision->label() }}</strong>
      </span>
      <a class="btn blue" href="{{ route('experiments.create', $a) }}" style="flex:1;min-width:150px;justify-content:center">
        + 建立試賣實驗
      </a>
      <a class="btn" href="{{ route('products.show', $a->canonical_product_id) }}">← 商品</a>
    </div>
  </div>

  {{-- ── 右：單位價比較 + 資料狀態 ── --}}
  <div>
    <div class="panel">
      <div class="panel-head">
        <h4>通路報價對照</h4>
        @if($product->canComputeUnitPrice())
          <span>正規化後 · 每{{ $product->comparison_quantity }}{{ $product->comparison_unit }}</span>
        @else
          <span>套組商品，不比較單位價</span>
        @endif
      </div>
      <div class="panel-body">
        <div class="ch-list">
          {{-- Costco 進貨價（最低標記） --}}
          @if($obs)
          @php
            $costcoUnitPrice = $product->canComputeUnitPrice() && $product->totalContentAmount() > 0
              ? round($obs->amount_minor * $product->comparison_quantity / $product->totalContentAmount(), 2)
              : null;
          @endphp
          <div class="ch-row ch-blue best">
            <span class="ch-rank">進貨</span>
            <span class="ch-thumb">COSTCO<br>賣場</span>
            <div>
              <div class="ch-name">
                <strong>{{ $obs->productOffer->retailer->name ?? 'Costco' }}</strong>
                <span class="ch-badge official">進貨來源</span>
              </div>
              <p class="ch-meta">
                <span>標價 {{ number_format($obs->amount_minor) }}</span>
                <span>{{ $obs->source_type->value }}</span>
              </p>
            </div>
            <div class="ch-money">
              <b>${{ number_format($obs->amount_minor) }}</b>
              @if($costcoUnitPrice)<small>${{ number_format($costcoUnitPrice, 2) }} / {{ $product->comparison_unit }}</small>@endif
            </div>
          </div>
          @endif

          {{-- 目標銷售通路 --}}
          <div class="ch-row ch-shopee">
            <span class="ch-thumb">{{ mb_substr($a->salesChannel->name, 0, 4) }}</span>
            <div>
              <div class="ch-name">
                <strong>{{ $a->salesChannel->name }}</strong>
                <span class="ch-badge">目標售出</span>
              </div>
              <p class="ch-meta">
                <span>預期售 {{ number_format($a->expected_sale_amount_minor) }}</span>
                <span>平台費 {{ number_format($a->salesChannel->platform_fee_basis_points/100, 1) }}%</span>
              </p>
            </div>
            <div class="ch-money">
              <b>${{ number_format($a->expected_sale_amount_minor) }}</b>
              <small>淨利 {{ $pos ? '+' : '' }}{{ number_format($profit) }}</small>
            </div>
          </div>
        </div>

        <p class="note">損益平衡售價 NT${{ number_format($a->break_even_amount_minor) }}。
          售價需高於此數字才不虧本。
          @if(!$product->canComputeUnitPrice()) 套組商品不計算單位價。@endif
        </p>
      </div>
    </div>

    <div class="panel" style="margin-top:14px">
      <div class="panel-head">
        <h4>資料狀態</h4>
        <span>來源與信心度</span>
      </div>
      <div class="panel-body" style="padding:0">
        <table class="tbl">
          <tbody>
            <tr>
              <td>進貨價觀測</td>
              <td class="num mono">{{ $obs ? $obs->created_at->diffForHumans() . ' · ' . $obs->source_type->value : '無資料' }}</td>
            </tr>
            <tr>
              <td>市場信心</td>
              <td class="num mono">{{ $a->market_data_status->label() }}</td>
            </tr>
            <tr>
              <td>試賣實驗</td>
              <td class="num mono">{{ $a->experiments->count() }} 筆</td>
            </tr>
            <tr>
              <td>商品比較模式</td>
              <td class="num mono">{{ $product->comparison_mode->label() }}</td>
            </tr>
            <tr>
              <td>分析時間</td>
              <td class="num mono">{{ $a->analyzed_at->format('Y-m-d H:i') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- 實驗列表 --}}
    @if($a->experiments->isNotEmpty())
    <div class="panel" style="margin-top:14px">
      <div class="panel-head">
        <h4>試賣實驗</h4>
        <a class="btn sm" href="{{ route('experiments.create', $a) }}">+ 新增</a>
      </div>
      <div class="panel-body" style="padding:0">
        <table class="tbl">
          <thead>
            <tr>
              <th>進貨</th>
              <th>已售</th>
              <th class="num">進貨總價</th>
              <th class="num">實際淨利</th>
              <th>狀態</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($a->experiments as $exp)
            <tr>
              <td class="mono">{{ $exp->quantity_purchased }} 件</td>
              <td class="mono">{{ $exp->quantity_sold }} / {{ $exp->quantity_listed }}</td>
              <td class="num mono">{{ $exp->purchase_total_minor ? number_format($exp->purchase_total_minor) : '—' }}</td>
              <td class="num mono {{ ($exp->actual_net_profit_minor ?? 0) >= 0 ? 'c-green' : 'c-red' }}">
                {{ $exp->actual_net_profit_minor !== null ? number_format($exp->actual_net_profit_minor) : '—' }}
              </td>
              <td><span class="pill test">{{ $exp->status->label() }}</span></td>
              <td style="text-align:right"><a class="btn sm" href="{{ route('experiments.edit', $exp) }}">更新</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>

</div>
@endsection
