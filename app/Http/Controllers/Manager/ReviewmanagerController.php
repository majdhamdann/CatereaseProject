<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewmanagerController extends Controller
{
    public function getBranchReviewsSummary1($branch_id)
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
   public function getBranchReviewsSummary()
{
    $manager = auth()->user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }
    $packages = Package::with(['feedbacks.user'])
        ->where('branch_id', $branch->id)
        ->get();

    $totalReviews = 0;
    $totalRatingSum = 0;
    $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

    $allFeedbackDetails = [];

    foreach ($packages as $package) {
        foreach ($package->feedbacks as $fb) {
            if (!$fb->feedbackType || $fb->feedbackType->target_type !== 'package') continue;

            $user = $fb->user;
            if (!$user) continue;

            $userTotalFeedbacks = $user->feedbacks()->count();

            $feedbackData = [
                'user_name' => $user->name,
                'user_image' => $user->profile_image ?? null,
                //'user_total_reviews' => $userTotalFeedbacks,
                'type' => $fb->type,
                'rating' => $fb->type === 'rating' ? (float) $fb->score : null,
                'message' => $fb->message,
                'created_at' => $fb->created_at->toDateTimeString(),
            ];

            if ($fb->type === 'rating') {
                $ratingValue = (int) $fb->score;
                if ($ratingValue >= 1 && $ratingValue <= 5) {
                    $totalReviews++;
                    $totalRatingSum += $ratingValue;
                    $ratingDistribution[$ratingValue]++;
                }
            }

            $allFeedbackDetails[] = $feedbackData;
        }
    }

    $averageRating = $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 2) : null;

    return response()->json([
        'branch' => $branch->location_note ?? $branch->description,
        'total_reviews' => $totalReviews,
        'average_rating' => $averageRating,
        'ratings_distribution' => [
            '5' => $ratingDistribution[5],
            '4' => $ratingDistribution[4],
            '3' => $ratingDistribution[3],
            '2' => $ratingDistribution[2],
            '1' => $ratingDistribution[1],
        ],
        'feedback_details' => $allFeedbackDetails,
    ]);
}




}
