<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
   

    
    public function index(Request $request)
{
    $query = Feedback::with(['user', 'feedbackType'])
        ->where('type', 'complaint')
        ->orderBy('created_at', 'desc');

      if ($request->has('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $complaints = $query->get();

    return response()->json($complaints);
}

    public function show($id)
    {
        $complaint = Feedback::with(['user', 'feedbackType'])
            ->where('type', 'complaint')
            ->findOrFail($id);

        return response()->json($complaint);
    }
    public function updateStatusfeedback(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,resolved'
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->status=$request->status;
         $feedback->save();
         return response()->json([$feedback]);
       
    }


    public function destroy($id)
    {
        $complaint = Feedback::where('type', 'complaint')->findOrFail($id);
        $complaint->delete();

        return response()->json(['message' => 'Complaint deleted.']);
    }
    

}
