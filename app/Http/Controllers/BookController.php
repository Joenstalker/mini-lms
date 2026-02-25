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
    public function index()
    {
        $books = Book::with('authors')->paginate(10);
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors = Author::all();
        return view('books.create', compact('authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'required|string|unique:books',
            'total_quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'isbn' => $validated['isbn'],
            'total_quantity' => $validated['total_quantity'],
            'available_quantity' => $validated['total_quantity'],
            'publisher' => $validated['publisher'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
        ]);

        if (!empty($validated['authors'])) {
            $book->authors()->attach($validated['authors']);
        }

        return redirect()->route('books.show', $book)->with('success', 'Book created successfully.');
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
        $authors = Author::all();
        return view('books.edit', compact('book', 'authors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'total_quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
        ]);

        $book->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'isbn' => $validated['isbn'],
            'total_quantity' => $validated['total_quantity'],
            'publisher' => $validated['publisher'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
        ]);

        if (isset($validated['authors'])) {
            $book->authors()->sync($validated['authors']);
        }

        return redirect()->route('books.show', $book)->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
