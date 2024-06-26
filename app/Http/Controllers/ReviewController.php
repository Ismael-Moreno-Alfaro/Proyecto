<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($movieId)
    {
        $reviews = Review::with('user:id,name')
            ->where('movie_id', $movieId)
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function reportedReviews()
    {
        $reviews = Review::with('user')->where('reported', true)->get();

        return Inertia::render('Admin/ReportedReviewsIndex', [
            'reviews' => $reviews
        ]);
    }

    public function approveReportedReview(Review $review)
    {
        $review->reported = false;
        $review->save();

        return Inertia::render('Admin/ReportedReviewsIndex');
    }


    public function report(Request $request, Review $review)
    {

        $review->update(['reported' => true]);

        return redirect()->back()->with('message', 'Crítica reportada exitosamente');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|between:0,10',
            'review' => 'nullable|string'
        ]);

        Review::create($validated);

        return redirect()->back()->with('message', 'Review added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $movieId,  $userId)
    {

        $validated = $request->validate([
            'rating' => 'required|between:0,10',
            'review' => 'nullable|string'
        ]);

        $review = Review::where('movie_id', $movieId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $review->update($validated);

        return response()->json($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {

        $review->delete();
    }
}
