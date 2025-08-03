<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryPerson;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewmanagerController extends Controller
{
   

public function getBranchReviewsSummary()
{
    $manager = auth()->user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }
     $date = request()->query('date'); 
      if ($date && !\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
        return response()->json(['message' => 'صيغة التاريخ غير صحيحة. يجب أن تكون Y-m-d'], 422);
    }
   $packages = Package::with(['allFeedbacks.user', 'allFeedbacks.feedbackType'])
    ->where('branch_id', $branch->id)
    ->get();
    

    $totalReviews = 0;
    $totalRatingSum = 0;
    $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

    $allFeedbackDetails = [];
    $allComplaintDetails = [];

    foreach ($packages as $package) {
        foreach ($package->allFeedbacks  as $fb) {
            if (!$fb->feedbackType || $fb->feedbackType->target_type !== 'package') continue;
            if ($date && $fb->created_at->format('Y-m-d') !== $date) continue;
            $user = $fb->user;
            if (!$user) continue;

            $feedbackData = [
                'user_name' => $user->name,
                'type' => $fb->type,
                'rating' => $fb->type === 'rating' ? (float) $fb->score : null,
                'message' => $fb->message,
                'created_at' => $fb->created_at ? $fb->created_at->toDateTimeString() : null,
            ];

            if ($fb->type === 'rating') {
                $ratingValue = (int) $fb->score;
                if ($ratingValue >= 1 && $ratingValue <= 5) {
                    $totalReviews++;
                    $totalRatingSum += $ratingValue;
                    $ratingDistribution[$ratingValue]++;
                }
                $allFeedbackDetails[] = $feedbackData;
            } elseif ($fb->type === 'complaint') {
                $allComplaintDetails[] = $feedbackData;
            }
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
        'complaint_details' => $allComplaintDetails,
    ]);
}



public function getDeliveryPersonsReviewsSummary()
{
    $manager = auth()->user();
    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }

    $date = request()->query('date'); 

    if ($date && !\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
        return response()->json(['message' => 'صيغة التاريخ غير صحيحة. يجب أن تكون Y-m-d'], 422);
    }

    $deliveryPersons = DeliveryPerson::whereHas('deliveries.order', function ($q) use ($branch) {
        $q->where('branch_id', $branch->id);
    })->with(['allFeedbacks.user', 'allFeedbacks.feedbackType'])->get();

    $totalReviews = 0;
    $totalRatingSum = 0;
    $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $feedbackDetails = [];
    $complaintDetails = [];

    foreach ($deliveryPersons as $deliveryPerson) {
        foreach ($deliveryPerson->feedbacks as $fb) {
            if (!$fb->feedbackType || $fb->feedbackType->target_type !== 'delivery_person') continue;

            if ($date && $fb->created_at->format('Y-m-d') !== $date) continue;

            $user = $fb->user;
            if (!$user) continue;

            $feedbackData = [
                'user_name' => $user->name,
                'type' => $fb->type,
                'rating' => $fb->type === 'rating' ? (float) $fb->score : null,
                'message' => $fb->message,
                'created_at' => optional($fb->created_at)->toDateTimeString(),
            ];

            if ($fb->type === 'rating') {
                $value = (int) $fb->score;
                if ($value >= 1 && $value <= 5) {
                    $totalReviews++;
                    $totalRatingSum += $value;
                    $ratingDistribution[$value]++;
                }
                $feedbackDetails[] = $feedbackData;
            } elseif ($fb->type === 'complaint') {
                $complaintDetails[] = $feedbackData;
            }
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
        'feedback_details' => $feedbackDetails,
        'complaint_details' => $complaintDetails,
    ]);
}

}
