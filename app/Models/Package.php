<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupon
 * @property-read int|null $coupon_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageDiscount> $discounts
 * @property-read int|null $discounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchServiceType> $extraServices
 * @property-read int|null $extra_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OccasionType> $occasionTypes
 * @property-read int|null $occasion_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $allFeedbacks
 * @property-read int|null $all_feedbacks_count
 * @property-read int|null $branch_service_type_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $complaint
 * @property-read int|null $complaint_count
 * @mixin \Eloquent
 */
class Package extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function items() {
        return $this->hasMany(PackageItem::class);
    }

    public function extras() {
        return $this->hasMany(PackageExtra::class);
    }
    public function branchServiceType()
    {
        return $this->belongsToMany(BranchServiceType::class);
    }


    public function categories() {
        return $this->belongsToMany(Category::class, 'package_categories', 'package_id', 'category_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function occasionTypes()
    {
        return $this->belongsToMany(OccasionType::class, 'package_occasion_map', 'package_id', 'occasion_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

      public function coupon(){

        return $this->belongsToMany(Coupon::class, 'package_coupon');
    }

    public function extraServices()
    {
        return $this->belongsToMany(
            \App\Models\BranchServiceType::class,
            'package_branch_service_map',
            'package_id',
            'branch_service_type_id'
        )->withTimestamps();
    }


     public function coupons()
     {
       return $this->belongsToMany(Coupon::class, 'package_coupon');
     }

    public function discounts()
    {
        return $this->hasMany(PackageDiscount::class);
    }
    public function feedbacks()
{
    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'FeedbackType_id',
        'id',
        'id'
    )->where('feedback_types.target_type', 'package')
     ->where('feedback.type', 'rating');
}

    public function complaint()
{
    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'FeedbackType_id',
        'id',
        'id'
    )->where('feedback_types.target_type', 'package')
     ->where('feedback.type', 'complaint');
}

    public function allFeedbacks()
{
    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'feedbackType_id',
        'id',
        'id'
    )->where('feedback_types.target_type', 'package');
}


}
