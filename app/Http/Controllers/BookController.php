<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use Smalot\PdfParser\Parser;

class BookController extends Controller
{
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'pdf_file' => 'required|mimes:pdf|max:10000',
            'cover_image' => 'image|max:2000',
        ]);

        // 1. Calculate PDF Page Count
        $parser = new Parser();
        $pdf = $parser->parseFile($request->file('pdf_file')->path());
        $totalPages = count($pdf->getPages());

        // 2. Upload to Google Drive
        $path = $request->file('pdf_file')->store('books', 'google');

        // 3. Save to Database
        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'google_drive_id' => $path, // Save the path/ID for streaming
            'total_pages' => $totalPages,
        ]);

        return back()->with('success', 'Book uploaded successfully!');
    }

    public function destroy(Book $book)
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        // 1. Delete from Google Drive if path exists
        if ($book->google_drive_id) {
            Storage::disk('google')->delete($book->google_drive_id);
        }

        // 2. Delete from Database
        $book->delete();

        return back()->with('success', 'Book and associated file deleted successfully!');
    }
}
