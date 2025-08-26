<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
        public function store(Request $request)
{
    $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'subject' => 'required|string|max:255',
        'details' => 'required|string',
    ]);

    $branch = Branch::findOrFail($request->branch_id);

    if (auth()->user()->id !== $branch->manager_id) {
        abort(403, 'Unauthorized');
    }

    $report = Report::create([
        'branch_id' => $branch->id,
        'manager_id' => auth()->id(),
        'subject' => $request->subject,
        'details' => $request->details,
    ]);

    return response()->json(['message' => 'Report created successfully', 'report' => $report]);
}
public function index11()
{
    $user = auth()->user(); 

    $reports = Report::whereIn('branch_id', $user->restaurant->pluck('id'))->with(['branch'])->get();

    return response()->json($reports);
}
public function index(Request $request)
{
    $user = auth()->user(); 

    $query = Report::whereIn('branch_id', $user->restaurant->pluck('id'))
        ->with(['branch']);

   
   if ($request->has('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $reports = $query->get();

    return response()->json($reports);
}

public function allReports(Request $request)
{
    $query = Report::with(['branch']);

    if ($request->has('date')) {
        $query->whereDate('created_at', $request->date);
    }

    
    $reports = $query->get();

    return response()->json($reports);
}
public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,resolved'
        ]);

        $report = Report::findOrFail($id);
        $report->status=$request->status;
         $report->save();
         return response()->json([$report]);
       
    }


}
