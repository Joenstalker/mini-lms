<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('author');
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('author', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        $books = $query->latest()->paginate(10)->withQueryString();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::orderBy('name')->get();
        return view('books.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'isbn'               => 'nullable|string|unique:books',
            'author_id'          => 'required|exists:authors,id',
            'description'        => 'nullable|string',
            'quantity'           => 'required|integer|min:1',
        ]);

        $data = $request->only('title', 'isbn', 'author_id', 'description', 'quantity');
        $data['available_quantity'] = $data['quantity'];
        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Book added successfully.');
    }

    public function show(Book $book)
    {
        $book->load(['author', 'borrowings.student']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        return view('books.edit', compact('book', 'authors'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'isbn'        => 'nullable|string|unique:books,isbn,' . $book->id,
            'author_id'   => 'required|exists:authors,id',
            'description' => 'nullable|string',
            'quantity'    => 'required|integer|min:1',
        ]);

        $oldQuantity   = $book->quantity;
        $newQuantity   = $request->quantity;
        $diffQuantity  = $newQuantity - $oldQuantity;
        $newAvailable  = max(0, $book->available_quantity + $diffQuantity);

        $book->update(array_merge(
            $request->only('title', 'isbn', 'author_id', 'description', 'quantity'),
            ['available_quantity' => $newAvailable]
        ));

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
