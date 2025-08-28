<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\User;

class ComplaintmanagerController extends Controller
{
    private function canManageComplaint(User $user, Feedback $feedback): bool
    {
        $feedbackType = $feedback->feedbackType;

        if ($feedbackType->target_type === 'package') {
            return $feedbackType->package 
                && $feedbackType->package->branch 
                && $feedbackType->package->branch->manager_id === $user->id;
        }

        if ($feedbackType->target_type === 'delivery_person') {
            return $feedbackType->deliveryPerson 
                && $feedbackType->deliveryPerson->branches 
                && $feedbackType->deliveryPerson->branches->contains('manager_id', $user->id);
        }

        if ($feedbackType->target_type === 'branch') {
            return $feedbackType->branch 
                && $feedbackType->branch->manager_id === $user->id;
        }

        return false;
    }

    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'feedbackType'])
            ->where('type', 'complaint')
            ->orderBy('created_at', 'desc');

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $complaints = $query->get();
        $user = auth()->user();

        // فقط المدير يشوف الشكاوى الخاصة بفرعه
        $filtered = $complaints->filter(function ($complaint) use ($user) {
            return $this->canManageComplaint($user, $complaint);
        })->values();

        return response()->json($filtered);
    }

    public function show($id)
    {
        $complaint = Feedback::with(['user', 'feedbackType'])
            ->where('type', 'complaint')
            ->findOrFail($id);

        if (!$this->canManageComplaint(auth()->user(), $complaint)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($complaint);
    }

    public function updateStatusfeedback(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);

        if (!$this->canManageComplaint(auth()->user(), $feedback)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:under_review,resolved'
        ]);

        $feedback->status = $request->status;
        $feedback->save();

        return response()->json($feedback);
    }

    public function destroy($id)
    {
        $complaint = Feedback::where('type', 'complaint')->findOrFail($id);

        if (!$this->canManageComplaint(auth()->user(), $complaint)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $complaint->delete();

        return response()->json(['message' => 'Complaint deleted.']);
    }
}
