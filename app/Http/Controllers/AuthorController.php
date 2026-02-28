<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest'); // Default to newest first
        $filter = $request->input('filter');

        $query = Author::with('books');

            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%")
                  ->orWhereHas('books', function($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });

        // Apply sorting - newest first by default
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Filter for newly added records (last 7 days)
        if ($filter === 'new') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        $authors = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('authors.partials.table', compact('authors'))->render();
        }

        return view('authors.index', compact('authors', 'search', 'sort', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:authors',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string',
        ]);

        $author = Author::create($validated);

        return redirect()->route('authors.index')->with('success', 'Author created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author, Request $request)
    {
        $author->load('books');
        
        if ($request->ajax()) {
            return view('authors.partials.show_content', compact('author'))->render();
        }
        
        return view('authors.show', compact('author'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string',
        ]);

        $author->update($validated);

        return redirect()->route('authors.index')->with('success', 'Author updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Author deleted successfully.');
    }
}
