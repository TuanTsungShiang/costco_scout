<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $brand
 * @property string $name
 * @property string|null $gtin
 * @property \App\Enums\ComparisonMode $comparison_mode
 * @property int|null $package_count
 * @property int|null $content_per_package
 * @property string|null $content_unit
 * @property int|null $comparison_quantity
 * @property string|null $comparison_unit
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOffer> $productOffers
 * @property-read int|null $product_offers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResaleAnalysis> $resaleAnalyses
 * @property-read int|null $resale_analyses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereComparisonMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereComparisonQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereComparisonUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereContentPerPackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereContentUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereGtin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct wherePackageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CanonicalProduct whereUpdatedAt($value)
 */
	class CanonicalProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_offer_id
 * @property int|null $store_id
 * @property int $amount_minor
 * @property string $currency_code
 * @property int $tax_included
 * @property int $tax_rate_basis_points
 * @property numeric|null $fx_rate_to_base
 * @property string|null $fx_rate_source
 * @property string|null $fx_captured_at
 * @property string $observed_at
 * @property \App\Enums\ObservationSourceType $source_type
 * @property int|null $raw_capture_id
 * @property \App\Enums\ObservationStatus $status
 * @property \Illuminate\Support\Carbon|null $invalidated_at
 * @property string|null $invalidated_reason
 * @property int|null $superseded_by_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\PriceTagCapture|null $priceTagCapture
 * @property-read \App\Models\ProductOffer|null $productOffer
 * @property-read PriceObservation|null $supersededBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereAmountMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereFxCapturedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereFxRateSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereFxRateToBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereInvalidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereInvalidatedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereObservedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereProductOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereRawCaptureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereSupersededById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereTaxIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceObservation whereTaxRateBasisPoints($value)
 */
	class PriceObservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $store_id
 * @property string|null $image_path
 * @property string|null $ocr_raw_text
 * @property array<array-key, mixed>|null $ocr_parsed_json
 * @property string|null $parsed_item_number
 * @property string|null $parsed_name
 * @property int|null $parsed_amount_minor
 * @property string $parsed_currency_code
 * @property \Illuminate\Support\Carbon|null $parsed_at
 * @property \App\Enums\CaptureStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Store|null $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereOcrParsedJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereOcrRawText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereParsedAmountMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereParsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereParsedCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereParsedItemNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereParsedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceTagCapture whereUpdatedAt($value)
 */
	class PriceTagCapture extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $canonical_product_id
 * @property int $retailer_id
 * @property string|null $external_product_id
 * @property string|null $external_url
 * @property string|null $external_title
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property string $confirmed_by
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CanonicalProduct|null $canonicalProduct
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PriceObservation> $priceObservations
 * @property-read int|null $price_observations_count
 * @property-read \App\Models\Retailer|null $retailer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereCanonicalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereConfirmedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereExternalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereExternalTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereExternalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereRetailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductOffer whereUpdatedAt($value)
 */
	class ProductOffer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $canonical_product_id
 * @property int $purchase_price_observation_id
 * @property int $sales_channel_id
 * @property int $expected_sale_amount_minor
 * @property int $estimated_platform_fee_minor
 * @property int $estimated_payment_fee_minor
 * @property int $estimated_promotion_fee_minor
 * @property int $estimated_shipping_minor
 * @property int $estimated_packaging_minor
 * @property int $estimated_return_loss_minor
 * @property int $estimated_other_cost_minor
 * @property int $estimated_membership_reward_minor
 * @property int $estimated_net_profit_minor
 * @property int $roi_basis_points
 * @property int $profit_margin_basis_points
 * @property int $break_even_amount_minor
 * @property \App\Enums\MarketDataStatus $market_data_status
 * @property \App\Enums\ResaleDecision $decision
 * @property \Illuminate\Support\Carbon $analyzed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CanonicalProduct|null $canonicalProduct
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResaleExperiment> $experiments
 * @property-read int|null $experiments_count
 * @property-read \App\Models\PriceObservation|null $purchasePriceObservation
 * @property-read \App\Models\SalesChannel|null $salesChannel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereAnalyzedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereBreakEvenAmountMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereCanonicalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedMembershipRewardMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedNetProfitMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedOtherCostMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedPackagingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedPaymentFeeMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedPlatformFeeMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedPromotionFeeMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedReturnLossMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereEstimatedShippingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereExpectedSaleAmountMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereMarketDataStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereProfitMarginBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis wherePurchasePriceObservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereRoiBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereSalesChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleAnalysis whereUpdatedAt($value)
 */
	class ResaleAnalysis extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $resale_analysis_id
 * @property int $quantity_purchased
 * @property int $quantity_listed
 * @property int $quantity_sold
 * @property int|null $purchase_total_minor
 * @property int|null $actual_average_sale_amount_minor
 * @property int|null $actual_platform_fee_minor
 * @property int|null $actual_payment_fee_minor
 * @property int|null $actual_shipping_minor
 * @property int|null $actual_packaging_minor
 * @property int|null $actual_other_cost_minor
 * @property int|null $actual_net_profit_minor
 * @property \Illuminate\Support\Carbon|null $listed_at
 * @property \Illuminate\Support\Carbon|null $first_sold_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \App\Enums\ExperimentStatus $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ResaleAnalysis|null $resaleAnalysis
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualAverageSaleAmountMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualNetProfitMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualOtherCostMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualPackagingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualPaymentFeeMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualPlatformFeeMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereActualShippingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereFirstSoldAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereListedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment wherePurchaseTotalMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereQuantityListed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereQuantityPurchased($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereQuantitySold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereResaleAnalysisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResaleExperiment whereUpdatedAt($value)
 */
	class ResaleExperiment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \App\Enums\RetailerType $type
 * @property string $country_code
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOffer> $productOffers
 * @property-read int|null $product_offers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store> $stores
 * @property-read int|null $stores_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer whereUpdatedAt($value)
 */
	class Retailer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $platform_fee_basis_points
 * @property int $payment_fee_basis_points
 * @property int $promotion_fee_basis_points
 * @property int $default_shipping_minor
 * @property int $default_packaging_minor
 * @property int $expected_return_loss_basis_points
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResaleAnalysis> $resaleAnalyses
 * @property-read int|null $resale_analyses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereDefaultPackagingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereDefaultShippingMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereExpectedReturnLossBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel wherePaymentFeeBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel wherePlatformFeeBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel wherePromotionFeeBasisPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereUpdatedAt($value)
 */
	class SalesChannel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $retailer_id
 * @property string $branch_name
 * @property string $country_code
 * @property string $currency_code
 * @property string $timezone
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PriceTagCapture> $priceTagCaptures
 * @property-read int|null $price_tag_captures_count
 * @property-read \App\Models\Retailer|null $retailer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereBranchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereRetailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereUpdatedAt($value)
 */
	class Store extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

