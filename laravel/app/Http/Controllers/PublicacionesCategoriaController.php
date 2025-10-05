<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Categorias;
use App\Models\Publicaciones;

class PublicacionesCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "estado"    => "ok",
            'mensaje'   => "sin acción"
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json([
            "estado"    => "ok",
            'mensaje'   => "sin acción"
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Verificar que la categoría existe
            $categoria = Categorias::findOrFail($id);
            
            // Obtener las publicaciones de esta categoría
            $publicaciones = Publicaciones::with('categoria')
                ->where('categorias_id', $id)
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json([
                "estado" => "ok",
                "categoria" => [
                    "id" => $categoria->_id,
                    "nombre" => $categoria->nombre,
                    "slug" => $categoria->slug
                ],
                "publicaciones" => $publicaciones,
                "total" => $publicaciones->count()
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            "estado"    => "ok",
            'mensaje'   => "sin acción"
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->json([
            "estado"    => "ok",
            'mensaje'   => "sin acción"
        ], Response::HTTP_OK);
    }
}
