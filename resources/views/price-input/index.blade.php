@extends('layouts.app')
@section('title', '價格輸入')

@section('content')
<div class="head">
  <div>
    <h3>價格輸入</h3>
    <p>三種來源各自獨立記錄。賣場價與線上價是兩筆不同觀測，不會互相覆蓋。</p>
  </div>
</div>

{{-- ── 三模式 Segmented Control ── --}}
<div class="seg" role="group" aria-label="價格來源">
  <button type="button" aria-pressed="true" data-src="ocr" id="btn-ocr">
    <svg width="17" height="17" aria-hidden="true"><use href="#i-camera"></use></svg>拍價格牌
  </button>
  <button type="button" aria-pressed="false" data-src="costco" id="btn-costco">
    <svg width="17" height="17" aria-hidden="true"><use href="#i-link"></use></svg>Costco 線上
  </button>
  <button type="button" aria-pressed="false" data-src="ec" id="btn-ec">
    <svg width="17" height="17" aria-hidden="true"><use href="#i-cart"></use></svg>電商通路
  </button>
</div>

{{-- ── 拍照辨識區 ── --}}
<div id="ocr-panel">

  {{-- Step 1: 選圖 / 拍照 --}}
  <div class="capture" id="capture-zone">
    <div class="capture-icon">
      <svg width="32" height="32" aria-hidden="true"><use href="#i-camera"></use></svg>
    </div>
    <h3>拍一張賣場價格牌</h3>
    <p>自動辨識標籤上的價格與品號。辨識完成後所有欄位都可手動修正，確認前不會建立紀錄。</p>

    <input type="file" id="imageInput" accept="image/*" capture="environment" style="display:none">

    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:18px">
      <button class="btn primary" id="cameraBtn" type="button">
        <svg width="16" height="16" aria-hidden="true"><use href="#i-camera"></use></svg>
        開啟相機
      </button>
      <button class="btn" id="uploadBtn" type="button">
        <svg width="16" height="16" aria-hidden="true"><use href="#i-link"></use></svg>
        選擇圖片
      </button>
    </div>
  </div>

  {{-- Step 2: 預覽 + 辨識進度 --}}
  <div id="preview-section" style="display:none;max-width:680px;margin:var(--gap) auto 0">
    <div style="position:relative;display:inline-block;width:100%">
      <img id="imgPreview" style="width:100%;border:2px solid var(--rule);display:block" alt="價格牌預覽">

      {{-- 辨識進度 overlay --}}
      <div id="ocrOverlay" style="position:absolute;inset:0;background:rgba(0,93,170,.88);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;color:#fff">
        <svg width="40" height="40" style="animation:spin 1.2s linear infinite" aria-hidden="true">
          <circle cx="20" cy="20" r="16" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="4"/>
          <path d="M20 4 A16 16 0 0 1 36 20" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
        </svg>
        <div style="font-family:var(--f-display);font-weight:800;font-size:1rem" id="ocrStatus">傳送圖片給 Claude…</div>
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:10px;align-items:center">
      <button class="btn sm" id="retakeBtn" type="button">重拍</button>
      <div id="ocrResult" style="flex:1;padding:8px 12px;background:var(--panel);border:1px solid var(--rule-soft);font-family:var(--f-mono);font-size:.72rem;color:var(--panel-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">等待辨識…</div>
    </div>

    {{-- 辨識失敗提示 --}}
    <div id="ocrError" style="display:none;margin-top:8px;padding:10px 14px;background:color-mix(in srgb,var(--red) 12%,transparent);border-left:3px solid var(--red);font-size:.83rem;color:var(--red)"></div>
  </div>

</div>{{-- /ocr-panel --}}

{{-- ── 手動輸入 CTA（非 OCR 模式） ── --}}
<div class="capture" id="manual-panel" style="display:none">
  <div class="capture-icon" style="background:var(--blue)">
    <svg width="32" height="32" aria-hidden="true" id="manualCapIcon"><use href="#i-link"></use></svg>
  </div>
  <h3 id="manualCapTitle">記錄 Costco 線上商品價格</h3>
  <p id="manualCapDesc">線上價與賣場價會分開記錄。兩者是不同觀測，不會互相覆蓋。</p>

  {{-- 商品網址（選填，會存到通路報價供日後追蹤） --}}
  <div class="url-row" id="urlRow" style="margin-top:16px">
    <label class="sr" for="manualUrl">商品網址</label>
    <input id="manualUrl" type="url" inputmode="url"
           placeholder="貼上商品頁網址（選填，例：costco.com.tw/...）">
  </div>

  <button class="btn primary" id="manualRunBtn" type="button" style="margin-top:14px">開始記錄</button>

  {{-- 擷取狀態/錯誤訊息 --}}
  <div id="scrapeMsg" style="display:none;max-width:640px;margin:12px auto 0;padding:9px 13px;font-size:.84rem;border-radius:var(--r);text-align:left"></div>
</div>

{{-- ── 確認表單 ── --}}
<div class="result panel" id="result"
     data-has-old="{{ old('amount_minor') ? '1' : '' }}"
     data-ocr-url="{{ route('ocr.recognize') }}"
     data-scrape-url="{{ route('price.scrape') }}"
     data-csrf="{{ csrf_token() }}"
     style="display:none;margin-top:var(--gap)">
  <div class="panel-head">
    <h4>確認並記錄</h4>
    <span class="pill hold" id="resultStatus">待確認</span>
  </div>
  <div class="panel-body">
    <form method="POST" action="{{ route('price-input.store') }}" id="priceForm">
      @csrf
      <input type="hidden" name="source_mode" id="source_mode" value="ocr">
      <input type="hidden" name="source_url" id="source_url" value="">

      <div class="fields">
        <div class="f">
          <label for="f-retailer">通路 / 賣場</label>
          <select id="f-retailer" name="retailer_id" required>
            <option value="">選擇通路...</option>
            @foreach($retailers as $r)
              <option value="{{ $r->id }}" {{ old('retailer_id') == $r->id ? 'selected' : '' }}>
                {{ $r->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="f">
          <label for="f-product">商品</label>
          <select id="f-product" name="canonical_product_id" required>
            <option value="">選擇或搜尋商品...</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}"
                data-brand="{{ $p->brand }}"
                data-name="{{ $p->name }}"
                {{ (old('canonical_product_id') == $p->id || request('new_product_id') == $p->id) ? 'selected' : '' }}>
                {{ $p->brand }} {{ $p->name }}
              </option>
            @endforeach
          </select>
          <span class="f-hint">找不到？<a href="{{ route('products.create') }}" id="createProductLink">建立新商品</a></span>
        </div>
        <div class="f">
          <label for="f-price">標價（TWD 整數）</label>
          <input id="f-price" name="amount_minor" class="mono" type="number"
                 inputmode="numeric" placeholder="例：769" min="1"
                 value="{{ old('amount_minor') }}" required>
          <span class="f-hint">輸入 769 代表 NT$769。TWD 不含小數。</span>
        </div>
        <div class="f">
          <label for="f-currency">貨幣</label>
          <select id="f-currency" name="currency_code">
            <option value="TWD" selected>TWD 新台幣</option>
            <option value="USD">USD 美元</option>
            <option value="JPY">JPY 日圓</option>
          </select>
        </div>

        {{-- Claude 辨識到的商品名稱（供核對） --}}
        <div class="f wide" id="ocrNameField" style="display:none">
          <label>辨識到的品號 <span class="f-hint">（供核對，不送出）</span></label>
          <input type="text" id="ocrNameDisplay" readonly style="font-family:var(--f-mono);font-size:.82rem;color:var(--panel-muted)" placeholder="辨識結果">
        </div>

        <div class="f wide">
          <label for="f-notes">備註（折扣說明、有效期等）</label>
          <textarea id="f-notes" name="notes" rows="2"
            placeholder="例：7/22 促銷價，原價 899">{{ old('notes') }}</textarea>
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="button" id="discard">捨棄</button>
        <button class="btn blue" type="submit">確認記錄並前往試算</button>
      </div>
    </form>
  </div>
</div>

<div class="note" style="margin-top:24px">
  <strong>第一次使用？</strong>
  先到<a href="{{ route('products.create') }}">商品管理</a>建立商品基本資料（品牌、規格、比較模式），
  再回來記錄價格，最後進行套利試算。
</div>

<style>
@keyframes spin{to{transform:rotate(360deg)}}
</style>

@endsection

@push('scripts')
<script>
(function(){
'use strict';

/* ── DOM refs ── */
var segBtns      = document.querySelectorAll('.seg [data-src]');
var ocrPanel     = document.getElementById('ocr-panel');
var manualPanel  = document.getElementById('manual-panel');
var imageInput   = document.getElementById('imageInput');
var cameraBtn    = document.getElementById('cameraBtn');
var uploadBtn    = document.getElementById('uploadBtn');
var retakeBtn    = document.getElementById('retakeBtn');
var imgPreview   = document.getElementById('imgPreview');
var previewSec   = document.getElementById('preview-section');
var ocrOverlay   = document.getElementById('ocrOverlay');
var ocrStatus    = document.getElementById('ocrStatus');
var ocrResultEl  = document.getElementById('ocrResult');
var ocrErrorEl   = document.getElementById('ocrError');
var result       = document.getElementById('result');
var srcInput     = document.getElementById('source_mode');
var discardBtn   = document.getElementById('discard');
var priceField   = document.getElementById('f-price');
var ocrNameField = document.getElementById('ocrNameField');
var ocrNameDisp  = document.getElementById('ocrNameDisplay');
var manualRunBtn = document.getElementById('manualRunBtn');
var manualUrl    = document.getElementById('manualUrl');
var sourceUrlInp = document.getElementById('source_url');
var retailerSel  = document.getElementById('f-retailer');
var scrapeMsg    = document.getElementById('scrapeMsg');
var productSel   = document.getElementById('f-product');

/* ── Mode copy ── */
var COPY = {
  ocr:    { icon: '#i-camera', title: '拍一張賣場價格牌',        desc: '自動辨識標籤價格。辨識後所有欄位可手動修正。' },
  costco: { icon: '#i-link',   title: '記錄 Costco 線上商品價格', desc: '線上價與賣場價分開記錄，不會互相覆蓋。' },
  ec:     { icon: '#i-cart',   title: '記錄電商通路價格',          desc: '支援蝦皮、momo、PChome、Yahoo、露天。' }
};

/* ── Segmented control ── */
segBtns.forEach(function(b){
  b.addEventListener('click', function(){
    segBtns.forEach(function(x){ x.setAttribute('aria-pressed', String(x === b)); });
    var mode = b.dataset.src;
    srcInput.value = mode;
    hideResult();
    resetPreview();
    if(mode === 'ocr'){
      ocrPanel.style.display = '';
      manualPanel.style.display = 'none';
      if(sourceUrlInp) sourceUrlInp.value = '';
    } else {
      ocrPanel.style.display = 'none';
      manualPanel.style.display = '';
      var c = COPY[mode];
      manualPanel.querySelector('h3').textContent = c.title;
      manualPanel.querySelector('p').textContent  = c.desc;
      manualPanel.querySelector('svg use').setAttribute('href', c.icon);
    }
  });
});

/* 依模式自動選通路：costco → Costco；ec 不強制（有多家電商） */
function autoSelectRetailer(mode){
  if(!retailerSel) return;
  if(mode === 'costco'){
    for(var i = 0; i < retailerSel.options.length; i++){
      if(/costco|好市多/i.test(retailerSel.options[i].textContent)){
        retailerSel.selectedIndex = i;
        return;
      }
    }
  }
}

/* ── Camera / Upload ── */
if(cameraBtn) cameraBtn.addEventListener('click', function(){
  imageInput.setAttribute('capture','environment');
  imageInput.click();
});
if(uploadBtn) uploadBtn.addEventListener('click', function(){
  imageInput.removeAttribute('capture');
  imageInput.click();
});
if(retakeBtn)    retakeBtn.addEventListener('click', function(){ resetPreview(); hideResult(); });
if(manualRunBtn) manualRunBtn.addEventListener('click', function(){
  var url = manualUrl ? manualUrl.value.trim() : '';
  if(sourceUrlInp) sourceUrlInp.value = url;
  autoSelectRetailer(srcInput.value);

  // 有網址 → 先擷取價格；沒網址 → 直接手動記錄
  if(url){
    scrapePrice(url);
  } else {
    hideScrapeMsg();
    showResult();
  }
});

/* 呼叫後端擷取商品頁價格 */
function scrapePrice(url){
  var SCRAPE_URL = result.dataset.scrapeUrl;
  var CSRF_TOK   = result.dataset.csrf;

  manualRunBtn.disabled = true;
  manualRunBtn.textContent = '擷取中…';
  setScrapeMsg('info', '正在讀取商品頁價格…');

  fetch(SCRAPE_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOK,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ url: url }),
  })
  .then(function(r){ return r.json().then(function(d){ return { ok: r.ok, data: d }; }); })
  .then(function(res){
    manualRunBtn.disabled = false;
    manualRunBtn.textContent = '開始記錄';
    var d = res.data;

    if(res.ok && d.price_twd){
      // 成功：填價格、顯示名稱參考
      priceField.value = d.price_twd;
      priceField.style.outline = '3px solid var(--green)';
      setTimeout(function(){ priceField.style.outline = ''; }, 2500);

      var summary = ['NT$' + d.price_twd];
      if(d.item_number) summary.unshift('#' + d.item_number);
      if(d.name)        summary.push(d.name);
      setScrapeMsg('ok', '✓ 擷取成功：' + summary.join('　'));

      if(d.name || d.item_number){
        ocrNameDisp.value = [d.item_number ? '#'+d.item_number : null, d.name].filter(Boolean).join('  ');
        ocrNameField.style.display = '';
      }
      // 帶入「建立新商品」連結（名稱、品號、線上網址）
      updateCreateLink(d);
    } else {
      // 失敗：仍展開表單讓使用者手動輸入
      var msg = (d && d.error) ? d.error : '無法擷取價格，請手動輸入。';
      setScrapeMsg('warn', msg);
      if(d && d.name){
        ocrNameDisp.value = [d.item_number ? '#'+d.item_number : null, d.name].filter(Boolean).join('  ');
        ocrNameField.style.display = '';
      }
      // 即使沒抓到價格，名稱/品號/網址仍帶入建立商品連結
      if(d) updateCreateLink({ name: d.name, item_number: d.item_number, source_url: sourceUrlInp ? sourceUrlInp.value : '' });
    }
    showResult();
  })
  .catch(function(err){
    manualRunBtn.disabled = false;
    manualRunBtn.textContent = '開始記錄';
    setScrapeMsg('warn', '擷取失敗：' + err.message + '（請手動輸入價格）');
    showResult();
  });
}

function setScrapeMsg(type, text){
  if(!scrapeMsg) return;
  var styles = {
    info: { bg: 'var(--panel)',    fg: 'var(--panel-muted)', bd: 'var(--rule-soft)' },
    ok:   { bg: 'color-mix(in srgb,var(--green) 14%,transparent)', fg: 'var(--green)', bd: 'var(--green)' },
    warn: { bg: 'color-mix(in srgb,var(--red) 12%,transparent)',   fg: 'var(--red)',   bd: 'var(--red)' }
  };
  var s = styles[type] || styles.info;
  scrapeMsg.textContent = text;
  scrapeMsg.style.background  = s.bg;
  scrapeMsg.style.color       = s.fg;
  scrapeMsg.style.borderLeft  = '3px solid ' + s.bd;
  scrapeMsg.style.display     = '';
}
function hideScrapeMsg(){ if(scrapeMsg) scrapeMsg.style.display = 'none'; }
if(discardBtn)   discardBtn.addEventListener('click', function(){ hideResult(); resetPreview(); });

/* ── Image selected → Claude Vision pipeline ── */
imageInput.addEventListener('change', function(e){
  var file = e.target.files[0];
  if(!file) return;
  var reader = new FileReader();
  reader.onload = function(ev){
    var raw = ev.target.result;
    imgPreview.src = raw;
    previewSec.style.display = '';
    document.getElementById('capture-zone').style.display = 'none';
    ocrOverlay.style.display = 'flex';
    ocrStatus.textContent = '壓縮圖片…';
    ocrResultEl.textContent = '等待辨識…';
    ocrErrorEl.style.display = 'none';
    resizeImage(raw, 1920, function(resized){
      ocrStatus.textContent = '傳送給 Claude Vision…';
      runClaudeOcr(resized);
    });
  };
  reader.readAsDataURL(file);
});

/* 縮放到 maxW 寬，白底 JPEG */
function resizeImage(dataUrl, maxW, cb){
  var img = new Image();
  img.onload = function(){
    var w = img.width, h = img.height;
    if(w > maxW){ h = Math.round(h * maxW / w); w = maxW; }
    var c = document.createElement('canvas');
    c.width = w; c.height = h;
    var ctx = c.getContext('2d');
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, w, h);
    ctx.drawImage(img, 0, 0, w, h);
    cb(c.toDataURL('image/jpeg', 0.88));
  };
  img.src = dataUrl;
}

/* 呼叫 Laravel → OcrController → Claude API */
function runClaudeOcr(dataUrl){
  var OCR_URL  = result.dataset.ocrUrl;
  var CSRF_TOK = result.dataset.csrf;

  fetch(OCR_URL, {
    method: 'POST',
    headers: {
      'Content-Type':  'application/json',
      'X-CSRF-TOKEN':  CSRF_TOK,
      'Accept':        'application/json',
    },
    body: JSON.stringify({ image: dataUrl }),
  })
  .then(function(r){ return r.json().then(function(d){ return { ok: r.ok, data: d }; }); })
  .then(function(res){
    ocrOverlay.style.display = 'none';

    if(!res.ok || res.data.error){
      var msg = res.data.error || '未知錯誤';
      if(res.data.raw_response){
        msg += '\n\n完整回應：\n' + res.data.raw_response;
      }
      ocrErrorEl.textContent = '辨識失敗：' + msg;
      ocrErrorEl.style.whiteSpace = 'pre-wrap';
      ocrErrorEl.style.wordBreak  = 'break-all';
      ocrErrorEl.style.maxHeight  = '240px';
      ocrErrorEl.style.overflowY  = 'auto';
      ocrErrorEl.style.display = '';
      showResult();
      return;
    }

    var d = res.data;

    // 顯示摘要（debug 用）
    var parts = [];
    if(d.item_number)  parts.push('#' + d.item_number);
    if(d.brand)        parts.push(d.brand);
    if(d.name)         parts.push(d.name);
    if(d.price_twd)    parts.push('NT$' + d.price_twd);
    ocrResultEl.textContent = parts.join('　') || '辨識完成（無資料）';

    // 填入價格
    if(d.price_twd){
      priceField.value = d.price_twd;
      priceField.style.outline = '3px solid var(--green)';
      setTimeout(function(){ priceField.style.outline = ''; }, 2500);
    }

    // 顯示品號參考
    var refLabel = [d.item_number ? '#'+d.item_number : null, d.brand, d.name].filter(Boolean).join('  ');
    if(refLabel){
      ocrNameDisp.value = refLabel;
      ocrNameField.style.display = '';
    }

    // 更新「建立新商品」連結，帶入 Claude 直接解析的規格
    updateCreateLink(d);
    showResult();
  })
  .catch(function(err){
    ocrOverlay.style.display = 'none';
    ocrErrorEl.textContent = '網路錯誤：' + err.message;
    ocrErrorEl.style.display = '';
    showResult();
  });
}

/* 用 Claude API 回傳的結構化資料更新「建立新商品」連結 */
function updateCreateLink(d){
  var link = document.getElementById('createProductLink');
  if(!link) return;
  var base = link.href.split('?')[0];
  var p = new URLSearchParams();
  p.set('return_to', 'price-input');
  if(d.brand)                 p.set('brand', d.brand);
  if(d.name)                  p.set('name',  d.name);

  // 備註同時帶品號與線上網址
  var notes = [];
  if(d.item_number) notes.push('品號 #' + d.item_number);
  if(d.source_url)  notes.push('線上網址：' + d.source_url);
  if(notes.length)  p.set('notes', notes.join('\n'));

  if(d.comparison_mode)       p.set('comparison_mode',      d.comparison_mode);
  if(d.package_count)         p.set('package_count',        d.package_count);
  if(d.content_per_package)   p.set('content_per_package',  d.content_per_package);
  if(d.content_unit)          p.set('content_unit',         d.content_unit);
  if(d.comparison_quantity)   p.set('comparison_quantity',  d.comparison_quantity || 100);
  if(d.comparison_unit)       p.set('comparison_unit',      d.comparison_unit);
  link.href = base + '?' + p.toString();
  link.removeAttribute('target');
}

/* ── 共用 helpers ── */
function showResult(){
  // .result 在 CSS 預設 display:none，用明確 inline 值覆蓋（設 '' 會 fallback 回隱藏）
  result.style.display = 'block';
  var first = result.querySelector('select:not([disabled])');
  if(first) setTimeout(function(){ first.focus({ preventScroll: true }); }, 60);
}
function hideResult(){ result.style.display = 'none'; }
function resetPreview(){
  previewSec.style.display = 'none';
  document.getElementById('capture-zone').style.display = '';
  ocrOverlay.style.display = 'flex';
  imgPreview.src = '';
  imageInput.value = '';
  priceField.value = '';
  if(ocrNameField)  ocrNameField.style.display = 'none';
  if(ocrNameDisp)   ocrNameDisp.value = '';
  if(ocrErrorEl)    ocrErrorEl.style.display = 'none';
}

if(result.dataset.hasOld) showResult();

// 從「建立新商品」跳回時：帶 new_product_id → 自動選好商品 + 展開表單
(function(){
  var url = new URL(window.location.href);
  var newId = url.searchParams.get('new_product_id');
  if(!newId) return;
  var sel = document.getElementById('f-product');
  if(!sel) return;
  for(var i = 0; i < sel.options.length; i++){
    if(sel.options[i].value === newId){
      sel.options[i].selected = true;
      break;
    }
  }
  showResult();
  // 清掉 URL 的 query param，避免重整後又觸發
  url.searchParams.delete('new_product_id');
  history.replaceState(null, '', url.toString());
})();

})();
</script>
@endpush
