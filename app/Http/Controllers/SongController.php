<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;

class SongController extends Controller
{
     //  Liste des chansons

     /**
 * @OA\Get(
 *     path="/api/songs",
 *     summary="Lister toutes les chansons",
 *     tags={"Songs"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste paginée des chansons",
 *     )
 * )
 */


     public function index()
     {
         return Song::with('album.artist')->paginate(10);
     }
 
     //  Créer une chanson

     /**
 * @OA\Post(
 *     path="/api/songs",
 *     summary="Créer une nouvelle chanson",
 *     tags={"Songs"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","duration","album_id"},
 *             @OA\Property(property="title", type="string", example="Lose Yourself"),
 *             @OA\Property(property="duration", type="integer", example=326),
 *             @OA\Property(property="album_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Chanson créée avec succès",
 *     )
 * )
 */

     public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer',
            'album_id' => 'required|exists:albums,id',
        ]);

        $song = Song::create($validated);

        return response()->json($song, 201);
    }
 
     //  Afficher une chanson

     /**
 * @OA\Get(
 *     path="/api/songs/{id}",
 *     summary="Afficher une chanson par ID",
 *     tags={"Songs"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la chanson",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Chanson trouvée",
 *     ),
 *     @OA\Response(response=404, description="Chanson non trouvée")
 * )
 */

     public function show($id)
     {
         $song = Song::with('album.artist')->find($id);
 
         if (!$song) {
             return response()->json(['message' => 'Chanson non trouvée'], 404);
         }
 
         return response()->json($song, 200);
     }
 
     //  Mettre à jour une chanson

     /**
 * @OA\Put(
 *     path="/api/songs/{id}",
 *     summary="Mettre à jour une chanson",
 *     tags={"Songs"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la chanson",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Stan"),
 *             @OA\Property(property="duration", type="integer", example=400),
 *             @OA\Property(property="album_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Chanson mise à jour avec succès",
 *     ),
 *     @OA\Response(response=404, description="Chanson non trouvée")
 * )
 */
     public function update(Request $request, $id)
     {
         $song = Song::find($id);
 
         if (!$song) {
             return response()->json(['message' => 'Chanson non trouvée'], 404);
         }
 
         $validated = $request->validate([
             'title' => 'sometimes|string|max:255',
             'duration' => 'nullable|integer|min:30|max:900',
             'album_id' => 'sometimes|exists:albums,id',
         ]);
 
         $song->update($validated);
 
         return response()->json($song, 200);
     }
 
     //  Supprimer une chanson

     
/**
 * @OA\Delete(
 *     path="/api/songs/{id}",
 *     summary="Supprimer une chanson",
 *     tags={"Songs"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la chanson",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=204, description="Chanson supprimée"),
 *     @OA\Response(response=404, description="Chanson non trouvée")
 * )
 */

     public function destroy($id)
     {
         $song = Song::find($id);
 
         if (!$song) {
             return response()->json(['message' => 'Chanson non trouvée'], 404);
         }
 
         $song->delete();
 
         return response()->json(['message' => 'Chanson suprimee'], 204);
     }


     /**
 * @OA\Get(
 *     path="/api/songs/search",
 *     summary="Rechercher une chanson par titre ou artiste",
 *     tags={"Songs"},
 *     @OA\Parameter(
 *         name="title",
 *         in="query",
 *         required=false,
 *         description="Titre partiel ou complet de la chanson",
 *         @OA\Schema(type="string", example="Lose Yourself")
 *     ),
 *     @OA\Parameter(
 *         name="artist",
 *         in="query",
 *         required=false,
 *         description="Nom de l'artiste",
 *         @OA\Schema(type="string", example="Eminem")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Résultats de la recherche",
 *     ),
 *     @OA\Response(response=404, description="Aucune chanson trouvée")
 * )
 */


     public function chercherr(Request $request)
{
    $title = $request->query('title');
    $artist = $request->query('artist');
    

    $query = Song::with('album.artist');

    if ($title) {
        $query->where('title', 'like', "%$title%");
    }

    if ($artist) {
        $query->whereHas('album.artist', function ($q) use ($artist) {
            $q->where('name', 'like', "%$artist%");
        });
    }

    $songs = $query->get();

    if ($songs->isEmpty()) {
        return response()->json(['message' => 'Chanson'], 404);
    }

    return response()->json($songs, 200);
}

}
