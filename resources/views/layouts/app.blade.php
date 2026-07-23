<!DOCTYPE html>
<html lang="zh-Hant" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="light dark">
<title>@yield('title', '總覽') — Costco 套利偵察</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ============================================================
   TOKENS — 倉儲賣場語彙
   ============================================================ */
:root{
  --red:#e31837;--red-ink:#a80f27;
  --blue:#005daa;--blue-ink:#00447c;
  --yellow:#ffd200;--green:#00703c;
  --floor:#e4e2dd;--floor-2:#d6d3cc;
  --chrome-fg:#0a0a0a;--chrome-fg-muted:#5f5b54;
  --panel:#ffffff;--panel-fg:#0a0a0a;--panel-muted:#6b665e;
  --rule:#0a0a0a;--rule-soft:#c9c5bd;
  --paper:#ffffff;--paper-ink:#0a0a0a;--paper-sub:#57534c;
  --ch-shopee:#ee4d2d;--ch-momo:#e60044;--ch-pchome:#d0021b;
  --ch-yahoo:#6001d2;--ch-ruten:#ff6600;
  --f-display:"Archivo","Noto Sans TC","PingFang TC","Microsoft JhengHei",sans-serif;
  --f-body:"Inter","Noto Sans TC","PingFang TC","Microsoft JhengHei",sans-serif;
  --f-mono:"IBM Plex Mono",ui-monospace,SFMono-Regular,Menlo,monospace;
  --r:2px;--gap:14px;--tap:44px;
}
html[data-theme="dark"]{
  --floor:#141416;--floor-2:#1d1d20;
  --chrome-fg:#f2f0ec;--chrome-fg-muted:#9c968c;
  --panel:#1e1e22;--panel-fg:#f2f0ec;--panel-muted:#9c968c;
  --rule:#4a4a52;--rule-soft:#33333a;
}
*,*::before,*::after{box-sizing:border-box}
body{margin:0;min-height:100dvh;font-family:var(--f-body);font-size:15px;line-height:1.55;color:var(--chrome-fg);background:var(--floor);-webkit-font-smoothing:antialiased}
h1,h2,h3,h4{font-family:var(--f-display);margin:0;letter-spacing:-.015em;line-height:1.12}
p{margin:0}
button,input,select,textarea{font:inherit;color:inherit}
button{cursor:pointer;border-radius:var(--r)}
a{color:inherit}
.num{font-variant-numeric:tabular-nums;text-align:right;font-feature-settings:"tnum" 1}
.mono{font-family:var(--f-mono);font-variant-numeric:tabular-nums}
:where(a,button,input,select,[tabindex]):focus-visible{outline:3px solid var(--blue);outline-offset:2px}
html[data-theme="dark"] :where(a,button,input,select,[tabindex]):focus-visible{outline-color:var(--yellow)}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
@media(prefers-reduced-motion:no-preference){html{scroll-behavior:smooth}}

/* signage */
.signage{position:sticky;top:0;z-index:40;background:var(--blue);color:#fff;border-bottom:4px solid var(--red)}
.signage-top{max-width:1440px;margin:0 auto;display:flex;align-items:center;gap:14px;padding:10px 20px}
.mark{display:flex;align-items:center;gap:10px;text-decoration:none;color:#fff;flex:0 0 auto}
.mark-block{width:40px;height:40px;display:grid;place-items:center;background:var(--red);font-family:var(--f-display);font-weight:900;font-size:1.05rem;border-bottom:5px solid #fff}
.mark-copy strong{display:block;font-family:var(--f-display);font-weight:900;font-size:1rem;line-height:1.1}
.mark-copy span{display:block;font-size:.65rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase;opacity:.72}
.signage-meta{margin-left:auto;display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end}
.mode{display:inline-flex;border:1px solid rgba(255,255,255,.32);border-radius:var(--r);overflow:hidden;height:36px}
.mode a,.mode span{border:0;background:transparent;color:rgba(255,255,255,.72);padding:0 13px;display:inline-flex;align-items:center;font-family:var(--f-display);font-weight:800;font-size:.76rem;letter-spacing:.04em;text-decoration:none;white-space:nowrap}
.mode a.active{background:#fff;color:var(--blue)}
.mode span{opacity:.45;cursor:not-allowed}
.chip{display:inline-flex;align-items:center;gap:7px;height:36px;padding:0 11px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.28);border-radius:var(--r);font-size:.8rem;font-weight:600;white-space:nowrap}
.chip .mono{font-size:.78rem}
.sq{width:36px;height:36px;flex:0 0 auto;display:grid;place-items:center;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.28);color:#fff;border-radius:var(--r)}
.sq:hover{background:rgba(255,255,255,.22)}
.btn-add{height:36px;padding:0 14px;display:inline-flex;align-items:center;gap:7px;background:var(--red);color:#fff;border:0;font-family:var(--f-display);font-weight:800;font-size:.82rem;text-decoration:none;border-radius:var(--r)}
.btn-add:hover{background:var(--red-ink);color:#fff}
.aisles{max-width:1440px;margin:0 auto;display:flex;padding:0 20px;overflow-x:auto;scrollbar-width:none}
.aisles::-webkit-scrollbar{display:none}
.aisle{border:0;background:transparent;color:rgba(255,255,255,.7);font-family:var(--f-display);font-weight:700;font-size:.85rem;padding:11px 16px;display:inline-flex;align-items:center;gap:8px;border-bottom:4px solid transparent;margin-bottom:-4px;white-space:nowrap;border-radius:0;text-decoration:none}
.aisle:hover{color:#fff}
.aisle.active{color:#fff;border-bottom-color:#fff}

/* wrap + head */
.wrap{max-width:1440px;margin:0 auto;padding:22px 20px 40px}
.head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin:26px 0 12px;padding-bottom:8px;border-bottom:2px solid var(--rule)}
.head:first-child{margin-top:0}
.head h3{font-size:1.15rem;font-weight:800;text-transform:uppercase}
.head p{color:var(--chrome-fg-muted);font-size:.85rem;margin-top:3px}

/* panel */
.panel{background:var(--panel);color:var(--panel-fg);border:2px solid var(--rule);border-radius:var(--r)}
.panel-head{padding:12px 16px;border-bottom:2px solid var(--rule);display:flex;align-items:center;justify-content:space-between;gap:12px}
.panel-head h4{font-size:.92rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em;margin:0}
.panel-head span{color:var(--panel-muted);font-size:.78rem}
.panel-body{padding:16px}

/* price tag */
.tag{position:relative;background:var(--paper);color:var(--paper-ink);border:2px solid var(--paper-ink);border-radius:var(--r);padding:14px;display:flex;flex-direction:column;overflow:hidden}
.tag-head{display:flex;align-items:center;justify-content:space-between;gap:10px;border-bottom:1px solid var(--paper-ink);padding-bottom:7px}
.tag-item{font-family:var(--f-mono);font-size:.74rem;font-weight:600;letter-spacing:.05em}
.src{font-family:var(--f-display);font-weight:900;font-size:.66rem;letter-spacing:.14em;text-transform:uppercase;padding:3px 8px}
.src.store{background:var(--blue);color:#fff}
.src.online{background:transparent;color:var(--blue);box-shadow:inset 0 0 0 2px var(--blue)}
.tag-name{font-family:var(--f-display);font-weight:800;font-size:.98rem;line-height:1.25;text-transform:uppercase;margin-top:10px;min-height:2.5em}
.tag-sub{font-size:.76rem;color:var(--paper-sub);margin-top:3px}
.tag-price{margin-top:auto;padding-top:10px;display:flex;align-items:flex-start;justify-content:flex-end;font-family:var(--f-display);font-weight:900;font-variant-numeric:tabular-nums;letter-spacing:-.045em;line-height:.86}
.tag-price .cur{font-size:1.1rem;font-weight:800;margin-top:.28em;margin-right:.06em}
.tag-price .int{font-size:clamp(2.6rem,6.5vw,3.6rem)}
.tag-price .cent{font-size:1.2rem;margin-top:.22em;margin-left:.08em}
.tag-unit{display:flex;justify-content:space-between;gap:10px;font-family:var(--f-mono);font-size:.74rem;border-top:1px solid var(--paper-ink);margin-top:9px;padding-top:7px}
.tag-foot{font-family:var(--f-mono);font-size:.68rem;color:var(--paper-sub);margin-top:5px}
.checked::after{content:"";position:absolute;left:-6%;right:-6%;bottom:19%;height:30px;background:var(--yellow);opacity:.62;mix-blend-mode:multiply;transform:rotate(-3.2deg);pointer-events:none}

/* verdict */
.verdict{display:inline-flex;flex-direction:column;align-items:center;gap:2px;padding:9px 15px;border:3px solid currentColor;border-radius:var(--r);font-family:var(--f-display);font-weight:900;transform:rotate(-2deg)}
.verdict small{font-size:.6rem;letter-spacing:.2em;text-transform:uppercase;opacity:.85}
.verdict strong{font-size:1.4rem;line-height:1}
.verdict.costco{color:var(--blue)}
.verdict.online{color:var(--green)}
.verdict.hold{color:var(--chrome-fg-muted)}
.verdict-bar{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:12px;padding:12px 14px;background:var(--panel);border:2px solid var(--rule)}
.verdict-bar .save{font-family:var(--f-display);font-weight:900;font-size:1.5rem;font-variant-numeric:tabular-nums;letter-spacing:-.035em;color:var(--blue)}
html[data-theme="dark"] .verdict-bar .save{color:#6cb6ff}
.verdict-bar p{font-size:.79rem;color:var(--panel-muted);line-height:1.45}

/* pill */
.pill{display:inline-flex;align-items:center;height:24px;padding:0 9px;border:1.5px solid currentColor;border-radius:var(--r);font-family:var(--f-display);font-weight:800;font-size:.7rem;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap}
.pill.costco{color:var(--blue)}
.pill.online{color:var(--green)}
.pill.hold{color:var(--panel-muted)}
.pill.stale{color:var(--panel-muted);opacity:.7}
.pill.pass{color:#a80f27}
.pill.watch{color:#b07000}
.pill.test{color:var(--blue)}
.pill.restock{color:var(--green)}
.pill.scale{color:var(--green)}
html[data-theme="dark"] .pill.costco{color:#6cb6ff}
html[data-theme="dark"] .pill.online{color:#4ade80}

/* channel rows */
.ch-list{display:grid;gap:9px}
.ch-row{position:relative;display:grid;grid-template-columns:46px minmax(0,1fr) auto;gap:12px;align-items:center;padding:11px 13px 11px 15px;background:var(--panel);border:1px solid var(--rule-soft);border-left:5px solid var(--rule-soft);border-radius:var(--r)}
.ch-row.best{border-color:var(--rule);border-left-color:var(--rule);box-shadow:inset 0 0 0 1px var(--rule)}
/* channel accent colours — avoids inline CSS custom properties that IDE linters flag */
.ch-blue{border-left-color:var(--blue)}
.ch-blue.best{border-color:var(--blue);border-left-color:var(--blue)}
.ch-shopee{border-left-color:var(--ch-shopee)}
.ch-momo{border-left-color:var(--ch-momo)}
.ch-pchome{border-left-color:var(--ch-pchome)}
.ch-yahoo{border-left-color:var(--ch-yahoo)}
.ch-ruten{border-left-color:var(--ch-ruten)}
.ch-thumb{width:46px;height:46px;display:grid;place-items:center;background:var(--floor-2);border:1px solid var(--rule-soft);font-family:var(--f-display);font-weight:900;font-size:.62rem;letter-spacing:.02em;color:var(--panel-muted);text-align:center;line-height:1.1}
html[data-theme="dark"] .ch-thumb{background:#2a2a30}
.ch-name{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.ch-name strong{font-family:var(--f-display);font-weight:800;font-size:.88rem}
.ch-badge{font-size:.64rem;font-weight:700;letter-spacing:.04em;padding:2px 6px;border:1px solid var(--rule-soft);color:var(--panel-muted)}
.ch-badge.official{color:var(--ch,var(--blue));border-color:currentColor}
.ch-meta{font-family:var(--f-mono);font-size:.71rem;color:var(--panel-muted);margin-top:3px;display:flex;gap:10px;flex-wrap:wrap}
.ch-money{text-align:right;white-space:nowrap}
.ch-money b{display:block;font-family:var(--f-display);font-weight:900;font-size:1.28rem;font-variant-numeric:tabular-nums;letter-spacing:-.03em;line-height:1.1}
.ch-money small{display:block;font-family:var(--f-mono);font-size:.68rem;color:var(--panel-muted);margin-top:2px}
.ch-rank{position:absolute;top:-1px;right:-1px;background:var(--green);color:#fff;font-family:var(--f-display);font-weight:900;font-size:.6rem;letter-spacing:.12em;padding:2px 7px}

/* faceoff layout */
.faceoff{display:grid;grid-template-columns:minmax(260px,.72fr) minmax(0,1.28fr);gap:var(--gap);align-items:start}
.cols{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,.65fr);gap:var(--gap);align-items:start}

/* strip stats */
.strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border:2px solid var(--rule);background:var(--panel);color:var(--panel-fg)}
.strip>div{padding:14px 16px;border-left:1px solid var(--rule-soft)}
.strip>div:first-child{border-left:0}
.strip small{display:block;font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--panel-muted)}
.strip strong{display:block;margin-top:5px;font-family:var(--f-display);font-weight:900;font-size:1.7rem;font-variant-numeric:tabular-nums;letter-spacing:-.035em}
.strip em{display:block;font-style:normal;font-size:.75rem;color:var(--panel-muted);margin-top:2px}
.strip .mk-blue strong{color:var(--blue)}
.strip .mk-green strong{color:var(--green)}
html[data-theme="dark"] .strip .mk-blue strong{color:#6cb6ff}
html[data-theme="dark"] .strip .mk-green strong{color:#4ade80}

/* table */
.tbl{width:100%;border-collapse:collapse;font-size:.86rem}
.tbl th{text-align:left;padding:9px 12px;font-family:var(--f-display);font-weight:800;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--panel-muted);border-bottom:2px solid var(--rule);white-space:nowrap}
.tbl th.num,.tbl td.num{text-align:right}
.tbl td{padding:11px 12px;border-bottom:1px solid var(--rule-soft);vertical-align:middle}
.tbl tbody tr:last-child td{border-bottom:0}
.tbl tbody tr:hover{background:var(--floor-2)}
html[data-theme="dark"] .tbl tbody tr:hover{background:#26262b}
.tbl .name{font-weight:600}
.tbl .name small{display:block;font-family:var(--f-mono);font-size:.7rem;color:var(--panel-muted);font-weight:400}
.tbl .win{color:var(--blue);font-weight:700}
.tbl .lose{color:var(--green);font-weight:700}
html[data-theme="dark"] .tbl .win{color:#6cb6ff}
html[data-theme="dark"] .tbl .lose{color:#4ade80}

/* bar chart */
.bar-row{margin-bottom:14px}
.bar-row:last-child{margin-bottom:0}
.bar-top{display:flex;justify-content:space-between;gap:12px;font-size:.8rem;margin-bottom:5px}
.bar-top b{font-variant-numeric:tabular-nums}
.bar{height:12px;background:var(--floor-2);border:1px solid var(--rule)}
html[data-theme="dark"] .bar{background:#2a2a30}
.bar>i{display:block;height:100%;background:var(--blue)}
.bar>i.warn{background:var(--yellow)}
.bar>i.ok{background:var(--green)}

/* note */
.note{margin-top:16px;padding:11px 13px;border-left:5px solid var(--blue);background:var(--floor-2);font-size:.82rem;line-height:1.6}
html[data-theme="dark"] .note{background:#26262b}

/* seg control */
.seg{display:flex;flex-wrap:wrap;gap:0;border:2px solid var(--rule);background:var(--panel);margin-bottom:var(--gap)}
.seg button{flex:1 1 180px;border:0;background:transparent;color:var(--panel-muted);padding:13px 14px;display:flex;align-items:center;justify-content:center;gap:8px;font-family:var(--f-display);font-weight:800;font-size:.84rem;border-right:1px solid var(--rule-soft);border-radius:0}
.seg button:last-child{border-right:0}
.seg button[aria-pressed="true"]{background:var(--blue);color:#fff}

/* capture zone */
.capture{border:3px dashed var(--rule);background:var(--panel);color:var(--panel-fg);padding:40px 22px;text-align:center}
.capture-icon{width:72px;height:72px;margin:0 auto 16px;display:grid;place-items:center;background:var(--red);color:#fff}
.capture h3{font-size:1.3rem;font-weight:900;text-transform:uppercase}
.capture p{max-width:540px;margin:9px auto 18px;color:var(--panel-muted);font-size:.87rem}
.url-row{display:flex;gap:9px;max-width:640px;margin:0 auto;flex-wrap:wrap}
.url-row input{flex:1 1 260px;min-height:var(--tap);padding:9px 11px;border:2px solid var(--rule);border-radius:var(--r);background:var(--panel);color:var(--panel-fg);font-family:var(--f-mono);font-size:.84rem}
.result{display:none;max-width:780px;margin:22px auto 0;text-align:left}
.result.on{display:block}

/* buttons */
.btn{height:var(--tap);padding:0 20px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border:2px solid var(--rule);background:var(--panel);color:var(--panel-fg);font-family:var(--f-display);font-weight:800;font-size:.85rem;letter-spacing:.03em;text-decoration:none;border-radius:var(--r)}
.btn:hover{background:var(--floor-2);color:var(--panel-fg)}
.btn.primary{background:var(--red);border-color:var(--red);color:#fff}
.btn.primary:hover{background:var(--red-ink);border-color:var(--red-ink)}
.btn.blue{background:var(--blue);border-color:var(--blue);color:#fff}
.btn.blue:hover{background:var(--blue-ink);border-color:var(--blue-ink)}
.btn.sm{height:32px;font-size:.78rem;padding:0 12px}

/* form fields */
.fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
.f{display:grid;gap:5px}
.f.wide{grid-column:1/-1}
.f label{font-family:var(--f-display);font-weight:800;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--panel-muted)}
.f input,.f select,.f textarea{width:100%;min-height:var(--tap);padding:9px 11px;border:2px solid var(--rule);border-radius:var(--r);background:var(--panel);color:var(--panel-fg)}
.f textarea{min-height:80px;resize:vertical}
.f input.mono{font-family:var(--f-mono)}
.f-hint{font-family:var(--f-mono);font-size:.7rem;color:var(--panel-muted)}
.actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:16px}

/* receipt */
.receipt{position:relative;background:var(--paper);color:var(--paper-ink);border:2px solid var(--paper-ink);padding:18px;font-family:var(--f-mono);font-size:.82rem}
.receipt-top{text-align:center;border-bottom:1px dashed var(--paper-ink);padding-bottom:11px}
.receipt-top strong{display:block;font-family:var(--f-display);font-weight:900;font-size:1rem;letter-spacing:.1em;text-transform:uppercase}
.receipt-top span{font-size:.7rem;color:var(--paper-sub)}
.r-sec{font-family:var(--f-display);font-weight:900;font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;margin-top:14px;padding-bottom:4px;border-bottom:1px solid var(--paper-ink)}
.r-row{display:flex;justify-content:space-between;gap:14px;padding:5px 0}
.r-row b{font-weight:500;font-variant-numeric:tabular-nums}
.r-row.sub{border-top:1px dashed var(--paper-ink);margin-top:5px;padding-top:8px;font-weight:600}
.r-row.sub b{font-weight:600}
.r-row.total{border-top:3px double var(--paper-ink);margin-top:9px;padding-top:10px;font-family:var(--f-display);font-weight:900;font-size:1.02rem;text-transform:uppercase}
.r-row.total b{font-size:1.45rem;letter-spacing:-.03em}
.r-row .neg{color:var(--red)}
.r-row .pos{color:var(--green)}
.r-future{opacity:.42;margin-top:6px}
.r-future .r-sec{display:flex;justify-content:space-between;align-items:baseline;gap:10px}
.r-future .r-sec em{font-style:normal;font-size:.62rem;letter-spacing:.08em}
.receipt-foot{margin-top:14px;padding-top:10px;border-top:1px dashed var(--paper-ink);font-size:.68rem;color:var(--paper-sub);text-align:center}

/* analysis layout */
.analysis{display:grid;grid-template-columns:minmax(320px,.85fr) minmax(0,1.15fr);gap:var(--gap);align-items:start}

/* track cards */
.track{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:var(--gap)}
.track-card{padding:16px}
.track-card h4{font-size:1rem;font-weight:800;margin-top:10px}
.track-card p{color:var(--panel-muted);font-size:.8rem;margin-top:5px;line-height:1.55}
.track-stats{display:grid;grid-template-columns:1fr 1fr;gap:1px;margin-top:14px;background:var(--rule-soft);border:1px solid var(--rule-soft)}
.track-stats div{background:var(--panel);padding:9px 10px}
.track-stats small{display:block;font-size:.66rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--panel-muted)}
.track-stats b{display:block;margin-top:2px;font-family:var(--f-display);font-weight:800;font-size:1.05rem;font-variant-numeric:tabular-nums}

/* colour utility classes — use these instead of inline style="color:..." + Blade expressions */
.c-green{color:var(--green)}
.c-red{color:var(--red)}
.c-blue{color:var(--blue)}
.c-muted{color:var(--panel-muted)}
html[data-theme="dark"] .c-green{color:#4ade80}
html[data-theme="dark"] .c-blue{color:#6cb6ff}

/* flash */
.flash{padding:10px 16px;border-left:5px solid var(--green);background:#e6f4ec;font-size:.88rem;margin-bottom:16px}
.flash.error{border-left-color:var(--red);background:#fde8eb}

/* mobile dock */
.dock{display:none}

/* responsive */
@media(max-width:1100px){
  .faceoff{grid-template-columns:1fr}
  .strip{grid-template-columns:1fr 1fr}
  .strip>div:nth-child(3){border-left:0}
  .strip>div:nth-child(n+3){border-top:1px solid var(--rule-soft)}
  .cols,.analysis{grid-template-columns:1fr}
  .track{grid-template-columns:1fr 1fr}
}
@media(max-width:760px){
  .wrap{padding:18px 14px 96px}
  .signage-top{padding:9px 14px;gap:10px}
  .aisles{display:none}
  .chip.hide-sm,.btn-add span{display:none}
  .btn-add{padding:0 11px}
  .track{grid-template-columns:1fr}
  .fields{grid-template-columns:1fr}
  .f.wide{grid-column:auto}
  .tbl .col-sm{display:none}
  .ch-row{grid-template-columns:38px minmax(0,1fr) auto;padding-left:12px}
  .ch-thumb{width:38px;height:38px;font-size:.55rem}
  .dock{display:grid;grid-template-columns:repeat(4,1fr);position:fixed;left:0;right:0;bottom:0;z-index:50;background:var(--panel);border-top:2px solid var(--rule);padding-bottom:env(safe-area-inset-bottom)}
  .dock a{border:0;background:transparent;color:var(--panel-muted);display:grid;place-items:center;gap:2px;padding:9px 4px;font-family:var(--f-display);font-weight:700;font-size:.66rem;border-radius:0;text-decoration:none}
  .dock a.active{color:var(--red);box-shadow:inset 0 3px 0 var(--red)}
}
@media(max-width:460px){
  .strip{grid-template-columns:1fr}
  .strip>div{border-left:0;border-top:1px solid var(--rule-soft)}
  .strip>div:first-child{border-top:0}
  .signage-meta .chip{display:none}
  .ch-meta{display:none}
}
@media print{
  .signage,.dock,.actions{display:none}
  body{background:#fff}
}
</style>
</head>
<body>

<svg class="sr" aria-hidden="true"><defs>
  <symbol id="i-grid" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/></symbol>
  <symbol id="i-tag" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" d="M3 4h11l7 8-7 8H3z"/><circle cx="7.5" cy="12" r="1.6" fill="currentColor"/></symbol>
  <symbol id="i-scale" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M12 3v18M4 7h16M7 7l-3 7h6zM17 7l-3 7h6z"/></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" d="M4 12.5 9.5 18 20 6"/></symbol>
  <symbol id="i-camera" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" d="M3 7h4l2-3h6l2 3h4v13H3z"/><circle cx="12" cy="13" r="4" fill="none" stroke="currentColor" stroke-width="2.2"/></symbol>
  <symbol id="i-link" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 1 0-6.4-6.4L11.3 6.3M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 1 0 6.4 6.4l1.7-1.7"/></symbol>
  <symbol id="i-cart" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" d="M2 4h3l2.6 11h10.2L21 7H6"/><circle cx="9" cy="19.5" r="1.6" fill="currentColor"/><circle cx="18" cy="19.5" r="1.6" fill="currentColor"/></symbol>
  <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4.2" fill="none" stroke="currentColor" stroke-width="2.2"/><path stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M19.1 4.9l-1.8 1.8M6.7 17.3l-1.8 1.8"/></symbol>
  <symbol id="i-moon" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5z"/></symbol>
  <symbol id="i-plus" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2.6" stroke-linecap="round" d="M12 5v14M5 12h14"/></symbol>
</defs></svg>

<header class="signage">
  <div class="signage-top">
    <a class="mark" href="{{ route('dashboard') }}">
      <span class="mark-block">CA</span>
      <span class="mark-copy">
        <strong>套利偵察</strong>
        <span>Costco vs 電商</span>
      </span>
    </a>
    <div class="signage-meta">
      <div class="mode" role="group" aria-label="試算模式">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('products.*') || request()->routeIs('price-input') ? 'active' : '' }}">買方比價</a>
        <span title="Phase 2">賣方套利</span>
      </div>
      <span class="chip hide-sm mono">{{ now()->format('m/d H:i') }}</span>
      <button class="sq" id="theme" aria-label="切換明暗模式" type="button">
        <svg width="18" height="18" aria-hidden="true"><use href="#i-moon" id="themeIcon"></use></svg>
      </button>
      <a class="btn-add" href="{{ route('price-input') }}">
        <svg width="15" height="15" aria-hidden="true"><use href="#i-plus"></use></svg>
        <span>新增價格</span>
      </a>
    </div>
  </div>
  <nav class="aisles" aria-label="主要功能">
    <a class="aisle {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
      <svg width="15" height="15" aria-hidden="true"><use href="#i-grid"></use></svg>總覽
    </a>
    <a class="aisle {{ request()->routeIs('price-input') ? 'active' : '' }}" href="{{ route('price-input') }}">
      <svg width="15" height="15" aria-hidden="true"><use href="#i-tag"></use></svg>價格輸入
    </a>
    <a class="aisle {{ request()->routeIs('analyses.*') || request()->routeIs('products.show') ? 'active' : '' }}" href="{{ route('products.index') }}">
      <svg width="15" height="15" aria-hidden="true"><use href="#i-scale"></use></svg>分析詳情
    </a>
    <a class="aisle {{ request()->routeIs('experiments.*') ? 'active' : '' }}" href="{{ route('experiments.index') }}">
      <svg width="15" height="15" aria-hidden="true"><use href="#i-check"></use></svg>追蹤紀錄
    </a>
  </nav>
</header>

<main class="wrap">
  @if(session('success'))
    <div class="flash">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="flash error">
      @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
    </div>
  @endif

  @yield('content')
</main>

<nav class="dock" aria-label="主要功能">
  <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <svg width="20" height="20" aria-hidden="true"><use href="#i-grid"></use></svg>總覽
  </a>
  <a href="{{ route('price-input') }}" class="{{ request()->routeIs('price-input') ? 'active' : '' }}">
    <svg width="20" height="20" aria-hidden="true"><use href="#i-tag"></use></svg>輸入
  </a>
  <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') || request()->routeIs('analyses.*') ? 'active' : '' }}">
    <svg width="20" height="20" aria-hidden="true"><use href="#i-scale"></use></svg>分析
  </a>
  <a href="{{ route('experiments.index') }}" class="{{ request()->routeIs('experiments.*') ? 'active' : '' }}">
    <svg width="20" height="20" aria-hidden="true"><use href="#i-check"></use></svg>追蹤
  </a>
</nav>

<script>
(function(){
  var root = document.documentElement;
  var KEY = 'costco-arbitrage-theme';
  function read(){try{return localStorage.getItem(KEY);}catch(e){return null;}}
  function write(v){try{localStorage.setItem(KEY,v);}catch(e){}}
  var saved = read();
  if(saved==='dark'||saved==='light'){root.dataset.theme=saved;}
  else if(window.matchMedia('(prefers-color-scheme: dark)').matches){root.dataset.theme='dark';}
  var themeBtn=document.getElementById('theme');
  var themeIcon=document.getElementById('themeIcon');
  function paint(){
    var dark=root.dataset.theme==='dark';
    if(themeIcon)themeIcon.setAttribute('href',dark?'#i-sun':'#i-moon');
    if(themeBtn)themeBtn.setAttribute('aria-label',dark?'切換至淺色模式':'切換至深色模式');
  }
  paint();
  if(themeBtn)themeBtn.addEventListener('click',function(){
    root.dataset.theme=root.dataset.theme==='dark'?'light':'dark';
    write(root.dataset.theme);paint();
  });
})();
</script>
@stack('scripts')
</body>
</html>
