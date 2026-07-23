# COSTCO ARBITRAGE — MVP Spec v2

> 本文件為目前唯一有效規格。
>
> 它取代並廢止先前所有 Costco Quant / Global Retail Intelligence / Google Maps 相關 Appendix。
>
> 專案重新聚焦於最初目的：
>
> **在 Costco 發現可能有價差的商品，先計算真實淨利，再用小量試賣驗證，成功後才補貨。**

---

## 0. 一句話

> 站在 Costco 賣場裡拍價格牌，辨識商品與價格，換算成可比較單價，再判斷這件商品是否值得買一件回去試賣。

---

## 1. 核心目標

```text
發現商品
→ 計算套利
→ 小量試賣
→ 成功後補貨
```

這不是一般比價網站，也不是全球零售情報平台，而是：

> **Costco 零售套利決策工具。**

---

## 2. 要解決的問題

### 2.1 商品是否真的便宜

Costco 常見大包裝，其他通路常見小包裝。工具必須換算為相同基準，例如每 100 g、每 100 ml、每片、每顆或每個刀頭。

### 2.2 價差是否足以形成淨利

```text
可實際賣出的價格
- 採購成本
- 平台與金流費
- 物流
- 包材
- 活動費
- 退貨損耗
= 預估淨利
```

### 2.3 商品是否賣得掉

系統必須記錄自己的真實試賣結果，包括上架時間、首次成交時間、成交價、實際費用與真實淨利。

### 2.4 是否值得補貨

只有在第一件成功賣出、真實淨利符合預期、成交速度可接受、仍能補貨且市場價格未崩跌時，才進入補貨。

---

## 3. 明確不做

- Google Maps / Places API
- 多使用者與眾包
- 全球分店自動探索
- 商品外觀 Vision 辨識
- 高頻爬蟲
- 自動購買與自動刊登
- 自動庫存同步
- 機器學習評分
- 即時 3 秒回覆保證
- 跨國電商自動抓取
- 路線規劃、地理圍欄、MapProviderInterface
- Global Retail Intelligence Platform

---

## 4. Phase 0：先驗證，後寫 schema

### 4.1 驗證方式

1. 在 Costco 拍 20 項常買或正在特價的商品價格牌。
2. 回家手動查 Costco 線上、momo、PChome，蝦皮只做少量人工查詢。
3. 手動換算單價。
4. 找出可能的實際轉售價格。
5. 扣除全部費用後計算預估淨利。

### 4.2 商品對應狀態

```text
EXACT_MATCH
COMPARABLE_AFTER_NORMALIZATION
AMBIGUOUS
NOT_FOUND
```

成功對應率由前兩者計算。

### 4.3 Phase 0 要回答

1. 20 項裡有幾項可可靠對應？
2. 有幾項在其他通路真的比較貴？
3. 價差中位數是多少？
4. 扣除費用後，有幾項仍有正淨利？
5. 有幾項達到 `TEST_ONE_UNIT`？
6. 哪些類別最容易形成價差？
7. 哪些商品因物流、重量或規格問題失敗？

### 4.4 判斷標準

```text
可對應率 < 50%
→ 商品識別是主要瓶頸

扣成本後淨利率中位數 < 5%
→ 套利空間偏弱

可對應率 > 60%
且至少 20% 商品達到 TEST_ONE_UNIT
→ 值得完整實作
```

---

## 5. 核心設計原則

1. 金額一律使用整數最小貨幣單位。
2. 不使用 float / double 儲存金額與百分比。
3. OCR 只負責讀取價格牌，不直接推理商品。
4. OCR 結果必須人工確認後，才能成為正式 observation。
5. 商品對應是累積式資產，確認一次後永久重用。
6. `price_observations` 採 append-only。
7. 錯誤資料不物理刪除，改為 invalidated。
8. 單位比較與套利試算分成兩個獨立計算層。
9. 沒有真實成交資料前，只能給 `TEST_ONE_UNIT`，不能給 `SCALE`。
10. 外部價格來源可替換，但只做必要抽象。

---

## 6. 系統流程

### 6.1 賣場流程

```text
拍價格牌
→ OCR
→ 顯示解析結果
→ 使用者確認或修正
→ 查 Costco 商品號碼是否已有對應
    ├─ 已對應
    │   → 建立正式 price_observation
    │   → 執行單位比較
    │   → 執行套利試算
    └─ 未對應
        → 保存 capture
        → 進待辦佇列
        → 回家後建立 canonical product 與 offer
        → 完成後再建立 observation
```

### 6.2 試賣流程

```text
套利試算通過
→ 系統顯示 TEST_ONE_UNIT
→ 買 1 件
→ 建立 resale_experiment
→ 實際刊登
→ 記錄成交時間與成交價
→ 記錄所有真實成本
→ 計算 actual net profit
→ 決定 PASS / RESTOCK / SCALE
```

---

## 7. 資料模型

### retailers

```text
id
name
type
country_code
is_active
created_at
updated_at
```

`type`：`PHYSICAL | ONLINE | BOTH`

初期 seed：Costco Taiwan、Costco Online Taiwan、momo、PChome、Shopee Taiwan、Local Pickup。

### stores

```text
id
retailer_id
branch_name
country_code
currency_code
timezone
is_active
created_at
updated_at
```

手動 seed，不需要座標或 Google Place ID。

### canonical_products

```text
id
brand
name
gtin
comparison_mode
package_count
content_per_package
content_unit
comparison_quantity
comparison_unit
notes
created_at
updated_at
```

`comparison_mode`：

```text
WEIGHT
VOLUME
COUNT
SHEET
BUNDLE
```

除 `BUNDLE` 外，下列欄位必填：

```text
package_count
content_per_package
content_unit
comparison_quantity
comparison_unit
```

範例：

```text
奶粉 2.6 kg
comparison_mode = WEIGHT
package_count = 1
content_per_package = 2600
content_unit = G
comparison_quantity = 100
comparison_unit = G
```

```text
WD-40 440 ml × 2
comparison_mode = VOLUME
package_count = 2
content_per_package = 440
content_unit = ML
comparison_quantity = 100
comparison_unit = ML
```

```text
刮鬍刀架 + 12 刀頭
comparison_mode = BUNDLE
```

混合套組不可強行製造假單價。

### product_offers

```text
id
canonical_product_id
retailer_id
external_product_id
external_url
external_title
confirmed_at
confirmed_by
is_active
created_at
updated_at
```

`confirmed_by`：`MANUAL | AUTO_GTIN`

索引：

```text
unique(retailer_id, external_product_id)
```

### price_tag_captures

```text
id
store_id
image_path
ocr_raw_text
ocr_parsed_json
parsed_item_number
parsed_name
parsed_amount_minor
parsed_currency_code
parsed_at
status
created_at
updated_at
```

`status`：`PENDING | PARSED | FAILED | LINKED`

未完成商品對應前，只保存 capture，不建立正式 observation。

### price_observations

```text
id
product_offer_id
store_id
amount_minor
currency_code
tax_included
tax_rate_basis_points
fx_rate_to_base
fx_rate_source
fx_captured_at
observed_at
source_type
raw_capture_id
status
invalidated_at
invalidated_reason
superseded_by_id
created_at
```

`source_type`：`PRICE_TAG_OCR | MANUAL | SCRAPE | API`

`status`：`VALID | INVALIDATED | SUPERSEDED`

規則：不修改原始價格、不物理刪除；錯誤資料改為 `INVALIDATED`，新價格建立新 observation。

---

## 8. 金額、稅率與匯率

### amount_minor

依 ISO 4217 最小貨幣單位儲存：

```text
TWD 769 → 769
JPY 980 → 980
USD 12.99 → 1299
```

### 百分比

所有費率以 basis points 儲存：

```text
5% = 500
8% = 800
10% = 1000
2.5% = 250
```

### 匯率

匯率使用 `DECIMAL(20,10)`，禁止使用 float / double。

```php
config('arbitrage.base_currency', 'TWD')
```

同幣別觀測時，FX 欄位可為 null。

---

## 9. 單位價格計算

```text
total_content = package_count × content_per_package
```

```text
normalized_unit_price
= effective_purchase_amount
÷ total_content
× comparison_quantity
```

UI 同時顯示：

```text
現金實付單價
回饋調整後單價（估算）
```

不可只顯示回饋後結果。

---

## 10. Costco 回饋與線上運費

所有條款進 config：

```php
return [
    'base_currency' => 'TWD',

    'costco' => [
        'membership_tier' => 'EXECUTIVE',
        'executive_reward_rate_basis_points' => null,
        'executive_reward_cap_minor' => null,
        'cobrand_card_rate_basis_points' => null,
    ],

    'online' => [
        'shipping_threshold_minor' => [],
        'default_shipping_fee_minor' => [],
    ],
];
```

規則：

- 不寫死回饋比例與年度上限。
- 必須能反映回饋已達上限後，邊際回饋為 0。
- 單品比價必須含運費。
- 有免運門檻時，同時顯示「單買含運」與「湊單免運」。

---

## 11. 套利層

### sales_channels

```text
id
name
platform_fee_basis_points
payment_fee_basis_points
promotion_fee_basis_points
default_shipping_minor
default_packaging_minor
expected_return_loss_basis_points
is_active
created_at
updated_at
```

初期：Shopee、Ruten、Facebook Group、LINE Group Buy、Local Pickup、Own Website。

### resale_analyses

```text
id
canonical_product_id
purchase_price_observation_id
sales_channel_id
expected_sale_amount_minor
estimated_platform_fee_minor
estimated_payment_fee_minor
estimated_promotion_fee_minor
estimated_shipping_minor
estimated_packaging_minor
estimated_return_loss_minor
estimated_other_cost_minor
estimated_net_profit_minor
roi_basis_points
profit_margin_basis_points
break_even_amount_minor
market_data_status
decision
analyzed_at
created_at
updated_at
```

`market_data_status`：

```text
UNVERIFIED
LISTING_PRICE_ONLY
MANUAL_MARKET_CHECK
OWN_SALES_HISTORY
```

`decision`：

```text
PASS
WATCH
TEST_ONE_UNIT
RESTOCK
SCALE
```

### resale_experiments

```text
id
resale_analysis_id
quantity_purchased
quantity_listed
quantity_sold
purchase_total_minor
actual_average_sale_amount_minor
actual_platform_fee_minor
actual_payment_fee_minor
actual_shipping_minor
actual_packaging_minor
actual_other_cost_minor
actual_net_profit_minor
listed_at
first_sold_at
completed_at
status
notes
created_at
updated_at
```

`status`：`PLANNED | LISTED | PARTIALLY_SOLD | SOLD_OUT | CANCELLED | FAILED`

---

## 12. 套利計算公式

```text
estimated_platform_fee
= expected_sale_amount × platform_fee_rate

estimated_payment_fee
= expected_sale_amount × payment_fee_rate

estimated_promotion_fee
= expected_sale_amount × promotion_fee_rate

estimated_return_loss
= expected_sale_amount × expected_return_loss_rate
```

```text
estimated_net_profit
= expected_sale_amount
- cash_purchase_cost
+ estimated_membership_reward
- estimated_platform_fee
- estimated_payment_fee
- estimated_promotion_fee
- estimated_shipping
- estimated_packaging
- estimated_return_loss
- estimated_other_cost
```

```text
roi = estimated_net_profit ÷ cash_purchase_cost
profit_margin = estimated_net_profit ÷ expected_sale_amount
```

`break_even_sale_price` 為可使 `estimated_net_profit >= 0` 的最低售價。

---

## 13. 決策規則

### PASS

- 預估淨利小於等於 0
- ROI 低於最低門檻
- 商品規格無法可靠比較

### WATCH

- 有價差但售價只是刊登價
- 成交速度未知
- 尚無真實銷售資料
- 價格接近損益兩平

### TEST_ONE_UNIT

最低條件：

- 預估淨利為正
- ROI 達最低門檻
- 商品可合法販售
- 物流可行
- 規格確認完成
- 但尚無自己的真實成交資料

### RESTOCK

- 已完成至少一次真實成交
- 實際淨利為正
- 成交時間可接受
- 商品仍可補貨
- 市場售價未大幅下跌

### SCALE

- 多次真實成交
- 成交速度穩定
- 有可持續補貨來源
- 實際淨利穩定
- 退貨與客服風險可接受

---

## 14. 使用者介面

### 拍照頁

```text
拍價格牌
→ OCR 結果
→ 手動確認
```

可修正商品號碼、商品名稱、現價、原價、折扣、活動日期與購買上限。

### 商品比較頁

顯示 Costco 現金實付單價、回饋後單價、Costco 線上單價、momo 單價與 PChome 單價。

### 套利試算卡

```text
預估售價              999
採購成本             -769
估計會員回饋          +15
平台與金流            -85
物流                  -45
包材                   -8
退貨損耗               -5

預估淨利              102
ROI                  13.3%

市場資料：刊登價，尚未驗證成交
決策：TEST_ONE_UNIT
```

### 實驗追蹤頁

記錄購買數量、上架日期、刊登價格、首次成交時間、售出數量、真實費用、真實淨利與是否補貨。

---

## 15. 價格來源

MVP 順序：

```text
1. 手動輸入
2. Costco Taiwan 官網
3. momo
4. PChome
5. 蝦皮最後考慮
```

寫爬蟲前先確認官方 API、Affiliate datafeed、iChannels 通路王、AFFILIATES.one、robots.txt、使用條款、查詢頻率與快取策略。

MVP 即使完全沒有爬蟲，也必須可用。

---

## 16. Laravel 建議結構

```text
app/
├── Models/
│   ├── Retailer.php
│   ├── Store.php
│   ├── CanonicalProduct.php
│   ├── ProductOffer.php
│   ├── PriceTagCapture.php
│   ├── PriceObservation.php
│   ├── SalesChannel.php
│   ├── ResaleAnalysis.php
│   └── ResaleExperiment.php
├── Services/
│   ├── Ocr/
│   │   ├── PriceTagOcrService.php
│   │   └── PriceTagParser.php
│   ├── Pricing/
│   │   ├── UnitPriceCalculator.php
│   │   ├── RewardCalculator.php
│   │   └── BreakEvenCalculator.php
│   ├── Arbitrage/
│   │   ├── ResaleProfitCalculator.php
│   │   ├── ResaleDecisionService.php
│   │   └── ExperimentResultService.php
│   └── PriceSource/
│       ├── PriceSourceInterface.php
│       ├── ManualPriceSource.php
│       └── CostcoOnlinePriceSource.php
├── Enums/
│   ├── ComparisonMode.php
│   ├── ContentUnit.php
│   ├── ObservationStatus.php
│   ├── CaptureStatus.php
│   ├── ResaleDecision.php
│   └── ExperimentStatus.php
└── Http/
    ├── Controllers/
    └── Requests/
```

---

## 17. 實作階段

| Phase | 內容 | 完成標準 |
|---|---|---|
| 0 | 手動驗證 | 20 項商品完成對應率與淨利分析 |
| 1 | 拍照 + OCR + 人工確認 | 價格牌能轉成 capture |
| 2 | canonical product + 單位換算 | 可計算標準化單價 |
| 3 | Costco 線上價格 | 可比較賣場與官網 |
| 4 | 套利試算 | 可算淨利、ROI、損益兩平 |
| 5 | 試賣追蹤 | 可記錄一件商品的真實銷售 |
| 6 | 補貨決策 | 可產生 RESTOCK / SCALE |
| 7 | momo / PChome | 擴充市場資料 |
| 8 | 跨國手動輸入 | 日本價格可加入比較 |

---

## 18. 測試要求

至少測試：

- 金額不使用 float
- 單位價格換算正確
- Bundle 不強制產生單位價格
- 負淨利必須為 PASS
- 沒有真實銷售資料時不可 SCALE
- 第一筆正淨利分析最多只能 TEST_ONE_UNIT
- Local Pickup 可因低成本優於平台銷售
- 黑卡回饋達上限後回饋為 0
- Observation invalidation 不修改原始價格
- Break-even price 正確
- 含運與免運情境結果不同
- 錯誤 OCR 不可直接寫入正式 observation

---

## 19. Definition of Done — MVP

使用者可以：

1. 拍一張 Costco 價格牌
2. 查看 OCR 結果
3. 手動確認或修正
4. 對應到 canonical product
5. 查看標準化單價
6. 輸入或取得可能轉售價格
7. 選擇銷售通路
8. 計算完整淨利
9. 查看 ROI 與損益兩平價
10. 收到 PASS / WATCH / TEST_ONE_UNIT
11. 建立真實試賣
12. 記錄成交時間與真實成本
13. 得到是否 RESTOCK 的結論

---

## 20. 給 Claude Code 的重啟指示

```text
專案：Costco 零售套利決策工具
Stack：Laravel + MySQL + Docker

這是重新收斂後的唯一有效 spec。
先前的 Costco Quant、Global Retail Intelligence、Google Maps、
國家層級、MapProvider、Risk Score 等設計全部廢止。

核心目的：

1. 在 Costco 拍價格牌
2. OCR + 人工確認
3. 正規化不同包裝規格
4. 計算真實套利淨利
5. 只先買一件試賣
6. 成交後再決定補貨

開始前：

1. 讀完整份 COSTCO_ARBITRAGE_MVP_SPEC_V2.md
2. 檢查目前 repository 已存在的 migration、model、service 與 controller
3. 列出哪些既有實作與本 spec 衝突
4. 提出保留、修改、刪除與重新建立的安全計畫
5. 在我確認前，不要直接大規模重寫

重要規則：

- 金額一律整數最小單位
- 百分比一律 basis points
- 匯率使用 DECIMAL，不用 float / double
- OCR 必須人工確認
- 未完成 product mapping 前只存 capture
- price_observations append-only
- 錯誤 observation 使用 invalidation
- Bundle 不強制換算單價
- 回饋比例、運費、費率全部進 config
- 沒有真實成交資料前不可輸出 SCALE
- 第一次可行的商品只能輸出 TEST_ONE_UNIT
- 不實作 Google Maps、Places、眾包、全球分店探索或高頻爬蟲

先只做以下工作：

A. Repository audit
B. Phase 0 資料模板
C. Phase 1 migration proposal
D. OCR capture flow proposal

先不要開始 Phase 2 之後的程式碼。
```

---

## 21. 最終原則

這個工具不是用來證明 Costco 一定便宜，也不是用來看到價差就大量進貨。

它的價值是：

> **先淘汰不值得賣的商品，再用一件商品低風險驗證，成功後才放大。**
