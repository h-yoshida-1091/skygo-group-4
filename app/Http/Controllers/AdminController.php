<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftRequest;

class AdminController extends Controller
{
    public function dashboard()
    {
        $requests = ShiftRequest::with('user')->get();

        return view('admin.dashboard', compact('requests'));
    }

    public function approve($id)
    {
        $shift = ShiftRequest::findOrFail($id);
        $shift->status = 'approved';
        $shift->comment = null;
        $shift->save();

        return back();
    }

    public function reject(Request $request, $id)
    {
        $shift = ShiftRequest::findOrFail($id);
        $shift->status = 'rejected';
        $shift->comment = $request->comment;
        $shift->save();

        return back();
    }
}
