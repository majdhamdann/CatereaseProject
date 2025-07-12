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
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property string|null $street
 * @property string|null $building
 * @property string|null $floor
 * @property string|null $apartment
 * @property string|null $coordinate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereApartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCoordinate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $is_default
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLongitude($value)
 * @mixin \Eloquent
 */
	class Address extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $amount
 * @property string $issued_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bill whereUserId($value)
 * @mixin \Eloquent
 */
	class Bill extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $Restaurant_id
 * @property string $location
 * @property string|null $description
 * @property string|null $photo
 * @property int|null $Manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @property-read \App\Models\User|null $manager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Restaurant|null $restaurant
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @property int $restaurant_id
 * @property int|null $manager_id
 * @property int|null $city_id
 * @property string|null $location_note
 * @property string|null $latitude
 * @property string|null $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocationNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLongitude($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $branch
 * @property-read int|null $branch_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchServiceType> $branchServiceTypes
 * @property-read int|null $branch_service_types_count
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkingDay> $workingDays
 * @property-read int|null $working_days_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodCategory> $foodCategories
 * @property-read int|null $food_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @mixin \Eloquent
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property int $service_type_id
 * @property string|null $custom_price
 * @property string $service_cost
 * @property int $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderServiceType> $orderServiceTypes
 * @property-read int|null $order_service_types_count
 * @property-read \App\Models\ServiceType $serviceType
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereCustomPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereServiceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class BranchServiceType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @mixin \Eloquent
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cart_id
 * @property int $package_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartPackageExtra> $packageExtras
 * @property-read int|null $package_extras_count
 */
	class CartItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cart_item_id
 * @property int $extra_id
 * @property int $quantity
 * @property string $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CartItem $cartItem
 * @property-read \App\Models\PackageExtra $extra
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereExtraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereUpdatedAt($value)
 */
	class CartPackageExtra extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property int $food_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\FoodCategory $foodCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereFoodCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @property string $name
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodCategory> $foodCategories
 * @property-read int|null $food_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $packageCategories
 * @property-read int|null $package_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @mixin \Eloquent
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @mixin \Eloquent
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property string $code
 * @property string $discount_amount
 * @property string $expiration_date
 * @property int $used
 * @property int|null $promo_code_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\PromoCode|null $promoCode
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsed($value)
 * @mixin \Eloquent
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $delivery_person_id
 * @property string $status
 * @property string|null $estimated_time
 * @property string|null $delivered_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeliveryPerson|null $deliveryPerson
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeliveryTracking> $tracking
 * @property-read int|null $tracking_count
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery query()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereDeliveryPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereEstimatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Delivery extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $vehicle_type
 * @property int $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Delivery> $deliveries
 * @property-read int|null $deliveries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeliveryTracking> $tracking
 * @property-read int|null $tracking_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereVehicleType($value)
 * @mixin \Eloquent
 */
	class DeliveryPerson extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $delivery_id
 * @property int $delivery_person_id
 * @property string $latitude
 * @property string $longitude
 * @property string $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Delivery $delivery
 * @property-read \App\Models\DeliveryPerson $deliveryPerson
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereDeliveryPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class DeliveryTracking extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $FeedbackType_id
 * @property string $type
 * @property string|null $score
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FeedbackType $feedbackType
 * @property-read Model|\Eloquent $target
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereFeedbackTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUserId($value)
 * @mixin \Eloquent
 */
	class Feedback extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $target_type
 * @property int $target_ref_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read Model|\Eloquent $target
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereTargetRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class FeedbackType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereUpdatedAt($value)
 * @property int $branch_id
 * @property int $category_id
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereCategoryId($value)
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @mixin \Eloquent
 */
	class FoodCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property int $category_id
 * @property string $name
 * @property string|null $description
 * @property string $price
 * @property string|null $discount_price
 * @property string|null $image_url
 * @property int $available
 * @property int|null $calories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackType> $feedbackTypes
 * @property-read int|null $feedback_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDetail> $orderDetails
 * @property-read int|null $order_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereUpdatedAt($value)
 * @property string|null $photo
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereType($value)
 * @property int $food_category_id
 * @property-read \App\Models\FoodCategory $foodCategory
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereFoodCategoryId($value)
 * @mixin \Eloquent
 */
	class FoodItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OccasionType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property int|null $delivery_id
 * @property string $status
 * @property int|null $promo_code_id
 * @property string $total_price
 * @property int|null $address_id
 * @property int|null $cart_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Address|null $address
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDetail> $orderDetails
 * @property-read int|null $order_details_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderServiceType> $orderServiceTypes
 * @property-read int|null $order_service_types_count
 * @property int $is_approved
 * @property string|null $approved_at
 * @property string|null $rejection_reason
 * @property string|null $approval_deadline
 * @property int|null $approved_by
 * @property-read \App\Models\Bill|null $bill
 * @property-read \App\Models\Cart|null $cart
 * @property-read \App\Models\Delivery|null $delivery
 * @property-read \App\Models\PromoCode|null $promoCode
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderServiceType> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereApprovalDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRejectionReason($value)
 * @mixin \Eloquent
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $food_item_id
 * @property int $quantity
 * @property string $unit_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FoodItem $foodItem
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereFoodItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereUpdatedAt($value)
 * @property int $package_id
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail wherePackageId($value)
 * @property-read \App\Models\Package $package
 * @mixin \Eloquent
 */
	class OrderDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $branch_service_type_id
 * @property int $quantity
 * @property string $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BranchServiceType $branchServiceType
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereBranchServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class OrderServiceType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $otp
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUserId($value)
 * @mixin \Eloquent
 */
	class Otp extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property int|null $category_id
 * @property int|null $service_type_id
 * @property int|null $occasion_type_id
 * @property string $name
 * @property string|null $description
 * @property string|null $photo
 * @property string $base_price
 * @property string|null $cancellation_policy
 * @property int $prepayment_required
 * @property string|null $prepayment_amount
 * @property int $is_active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageExtra> $extras
 * @property-read int|null $extras_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCancellationPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereOccasionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrepaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrepaymentRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDetail> $orderDetails
 * @property-read int|null $order_details_count
 * @property-read \App\Models\OccasionType|null $occasionType
 * @property-read \App\Models\ServiceType|null $serviceType
 * @mixin \Eloquent
 * @property int|null $branch_service_type_id
 * @property int $serves_count
 * @property int $max_extra_persons
 * @property string $price_per_extra_person
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\BranchServiceType|null $branchServiceType
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereBranchServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereMaxExtraPersons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePricePerExtraPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereServesCount($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $package_id
 * @property string $name
 * @property string $price
 * @property int $is_optional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereIsOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $food_item_id
 * @property int|null $branch_service_type_id
 * @property string $type
 * @property-read \App\Models\BranchServiceType|null $branchServiceType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartPackageExtra> $cartPackageExtras
 * @property-read int|null $cart_package_extras_count
 * @property-read \App\Models\FoodItem|null $foodItem
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereBranchServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereFoodItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereType($value)
 */
	class PackageExtra extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $package_id
 * @property int $food_item_id
 * @property int $quantity
 * @property int $is_optional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FoodItem $foodItem
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereFoodItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereIsOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PackageItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $bill_id
 * @property int $user_id
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 * @mixin \Eloquent
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $discount_percent
 * @property string|null $max_discount
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereMaxDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PromoCode extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $photo
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackType> $feedbackTypes
 * @property-read int|null $feedback_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereIsActive($value)
 */
	class Restaurant extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $pricing_model
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchServiceType> $branchServiceTypes
 * @property-read int|null $branch_service_types_count
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType wherePricingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ServiceType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $Full_Name
 * @property int $role_id
 * @property int $phone
 * @property string|null $photo
 * @property string $gender
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $verified
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerified($value)
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @property-read \App\Models\DeliveryPerson|null $deliveryPerson
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bill> $bills
 * @property-read int|null $bills_count
 * @property-read \App\Models\Cart|null $cart
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \App\Models\Branch|null $managedBranch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Restaurant|null $restaurant
 * @mixin \Eloquent
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property string $day_of_week
 * @property string|null $open_time
 * @property string|null $close_time
 * @property int $is_closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereCloseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereIsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereOpenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class WorkingDay extends \Eloquent {}
}

