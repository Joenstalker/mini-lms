<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Book::with('authors');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('authors', function($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $books = $query->get();

        return view('books.index', compact('books', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('books.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
            'cover_image' => 'nullable|string',
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'total_quantity' => $validated['total_quantity'],
            'available_quantity' => $validated['total_quantity'],
            'publisher' => $validated['publisher'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
            'cover_image' => $validated['cover_image'] ?? null,
        ]);

        if (!empty($validated['authors'])) {
            $book->authors()->attach($validated['authors']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Book created successfully.']);
        }

        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load('authors');
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return redirect()->route('books.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
            'cover_image' => 'nullable|string',
        ]);

        $book->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'total_quantity' => $validated['total_quantity'],
            'publisher' => $validated['publisher'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
            'cover_image' => $validated['cover_image'] ?? null,
        ]);

        if (isset($validated['authors'])) {
            $book->authors()->sync($validated['authors']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Book updated successfully.']);
        }

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Book deleted successfully.']);
        }

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
