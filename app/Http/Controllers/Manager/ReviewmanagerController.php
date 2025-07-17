<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Package;
use Illuminate\Http\Request;

class ReviewmanagerController extends Controller
{
    public function getBranchReviewsSummary($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $packages = Package::with('feedbacks')
        ->where('branch_id', $branch_id)
        ->get();

    $totalReviews = 0;
    $totalRatingSum = 0;

    foreach ($packages as $package) {
        $reviewsCount = $package->feedbacks->count();
        $ratingSum = $package->feedbacks->sum('rating');

        $totalReviews += $reviewsCount;
        $totalRatingSum += $ratingSum;
    }

    $averageRating = $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 2) : null;

    return response()->json([
        'branch' => $branch->location_note ?? $branch->description,
        'total_reviews' => $totalReviews,
        'average_rating' => $averageRating,
    ]);
}

}
