<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:restaurant,branch,package,food_item,delivery_person,service',
            'target_id'   => 'required|integer',
            'type'        => 'required|in:rating,complaint',
            'score'       => 'nullable|numeric|min:1|max:5',
            'message'     => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();


            $feedbackType = FeedbackType::firstOrCreate([
                'target_type'  => $request->target_type,
                'target_ref_id'=> $request->target_id,
            ]);


            $existing = Feedback::where('user_id', $user->id)
                ->where('FeedbackType_id', $feedbackType->id)
                ->first();

            if ($existing) {

                $existing->update([
                    'type'    => $request->type,
                    'score'   => $request->type === 'rating' ? $request->score : null,
                    'message' => $request->message,

                ]);

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => ucfirst($request->type) . ' updated successfully.',
                   // 'data'    => $existing->makeHidden(['status']),
                ]);
            }


            $feedback = Feedback::create([
                'user_id'        => $user->id,
                'FeedbackType_id'=> $feedbackType->id,
                'type'           => $request->type,
                'score'          => $request->score,
                'message'        => $request->message,

            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Feedback submitted successfully.',
               // 'data'    => $feedback->makeHidden(['status']),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Failed to save feedback.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
