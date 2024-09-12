<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // Listar todas as categorias (Ler)
    public function index()
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    // Criar uma nova categoria (Criar)
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $categoria = Categoria::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        return response()->json($categoria, 201);
    }

    // Exibir uma categoria específica (Ler)
    public function show($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        return response()->json($categoria);
    }

    // Atualizar uma categoria existente (Atualizar)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        $categoria->update($request->all());

        return response()->json($categoria);
    }

    // Deletar uma categoria (Deletar)
    public function destroy($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        $categoria->delete();

        return response()->json(['message' => 'Categoria deletada com sucesso']);
    }
}
