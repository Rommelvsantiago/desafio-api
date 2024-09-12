<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    // Listar todas as notas (Ler)
    public function index()
    {
        $notas = Nota::all();
        return response()->json($notas);
    }

    // Criar uma nova nota (Criar)
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'categoria_id' => 'required|integer|exists:categorias,id',
        ]);

        $nota = Nota::create([
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'usuario_id' => $request->usuario_id,
            'categoria_id' => $request->categoria_id,
        ]);

        return response()->json($nota, 201);
    }

    // Exibir uma nota específica (Ler)
    public function show($id)
    {
        $nota = Nota::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota não encontrada'], 404);
        }

        return response()->json($nota);
    }

    // Atualizar uma nota existente (Atualizar)
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'conteudo' => 'sometimes|required|string',
            'usuario_id' => 'sometimes|required|integer|exists:usuarios,id',
            'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
        ]);

        $nota = Nota::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota não encontrada'], 404);
        }

        $nota->update($request->all());

        return response()->json($nota);
    }

    // Deletar uma nota (Deletar)
    public function destroy($id)
    {
        $nota = Nota::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota não encontrada'], 404);
        }

        $nota->delete();

        return response()->json(['message' => 'Nota deletada com sucesso']);
    }
}
