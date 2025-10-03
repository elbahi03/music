<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Album;

class AlbumController extends Controller
{
    //  Liste des albums

    /**
 * @OA\Get(
 *     path="/api/albums",
 *     summary="Lister tous les albums",
 *     tags={"Albums"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des albums paginée",
 *     )
 * )
 */

    public function index()
    {
        return Album::with('artist')->paginate(10);
    }
/**
 * @OA\Post(
 *     path="/api/albums",
 *     summary="Créer un nouvel album",
 *     tags={"Albums"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","artist_id"},
 *             @OA\Property(property="title", type="string", example="The Marshall Mathers LP"),
 *             @OA\Property(property="year", type="integer", example=2000),
 *             @OA\Property(property="artist_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Album créé avec succès",
 *     )
 * )
 */

    //  Créer un album
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'artist_id' => 'required|exists:artists,id',
        ]);

        $album = Album::create($validated);

        return response()->json($album, 201);
    }

    //  Afficher un album

    /**
 * @OA\Get(
 *     path="/api/albums/{id}",
 *     summary="Afficher un album par ID",
 *     tags={"Albums"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'album",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Album trouvé",
 *     ),
 *     @OA\Response(response=404, description="Album non trouvé")
 * )
 */
    public function show($id)
    {
        $album = Album::with('artist')->find($id);

        if (!$album) {
            return response()->json(['message' => 'Album non trouvé'], 404);
        }

        return response()->json($album, 200);
    }

    // details de album

    /**
 * @OA\Get(
 *     path="/api/albums/{id}/songs",
 *     summary="Lister les chansons d'un album",
 *     tags={"Albums"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'album",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des chansons de l'album",
 *     ),
 *     @OA\Response(response=404, description="Album non trouvé")
 * )
 */
    public function songs($id)
    {
        $album = Album::with('songs')->find($id);

         if (!$album) {
             return response()->json(['message' => 'Album non trouvé'], 404);
    }

         return response()->json($album->songs, 200);
    }

    //  Mettre à jour un album

    /**
 * @OA\Put(
 *     path="/api/albums/{id}",
 *     summary="Mettre à jour un album",
 *     tags={"Albums"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'album",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Encore"),
 *             @OA\Property(property="year", type="integer", example=2004),
 *             @OA\Property(property="artist_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Album mis à jour avec succès",
 *     ),
 *     @OA\Response(response=404, description="Album non trouvé")
 * )
 */

    public function update(Request $request, $id)
    {
        $album = Album::find($id);

        if (!$album) {
            return response()->json(['message' => 'Album non trouvé'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'artist_id' => 'sometimes|exists:artists,id',
        ]);

        $album->update($validated);

        return response()->json($album, 200);
    }

    //  Supprimer un album

    /**
 * @OA\Delete(
 *     path="/api/albums/{id}",
 *     summary="Supprimer un album",
 *     tags={"Albums"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'album",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=204, description="Album supprimé avec succès"),
 *     @OA\Response(response=404, description="Album non trouvé")
 * )
 */


    public function destroy($id)
    {
        $album = Album::find($id);

        if (!$album) {
            return response()->json(['message' => 'Album non trouvé'], 404);
        }

        $album->delete();

        return response()->json(null, 204);
    }
}
