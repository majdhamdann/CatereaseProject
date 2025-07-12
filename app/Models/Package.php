<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(BranchServiceType::class);
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
    public function occasionType()
    {
        return $this->belongsTo(OccasionType::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
     
     public function coupons()
     {
       return $this->belongsToMany(Coupon::class, 'package_coupon');
     }


}
