<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notes = Note::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $category_id = $request->input('category_id', null);

        if ($category_id) {
            $request->validate([
                'category_id' => 'integer|exists:categories,id'
            ]);
        }

        $note = Note::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $category_id,
            'user_id' => $user->id
        ]);

        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'conteudo' => 'sometimes|required|string',
        ]);
        
        $note = Note::where('id', $id)->where('user_id', $user->id)->first();

        if (!$note) {
            return response()->json(['message' => 'not_found'], 404);
        }

        $note->title = $request->title;
        $note->content = $request->content;
        $note->category_id = $request->input('category_id', $note->category_id);
        $note->save();

        return response()->json($note);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $note = Note::where('id', $id)->where( 'user_id', $user->id)->first();

        if (!$note) {
            return response()->json(['message' => 'not_found'], 404);
        }
        $note->delete();

        return response()->json(['message' => 'deleted']);
    }
}
