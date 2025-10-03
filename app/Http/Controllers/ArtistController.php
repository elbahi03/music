<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;





class ArtistController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/artists",
 *     summary="Lister les artistes",
 *     description="Retourne la liste paginée des artistes",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Numéro de page",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des artistes",
 *     )
 * )
 */

    public function index()
    {
        return Artist::paginate(10);
    }

    /**
 * @OA\Post(
 *     path="/api/artists",
 *     summary="Créer un nouvel artiste",
 *     tags={"Artists"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Eminem"),
 *             @OA\Property(property="genre", type="string", example="Rap"),
 *             @OA\Property(property="country", type="string", example="USA")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Artiste créé avec succès",
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $artist = Artist::create($validated);

        return response()->json($artist, 201);
    }

    /**
 * @OA\Get(
 *     path="/api/artists/{id}",
 *     summary="Afficher un artiste par ID",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'artiste",
 *        
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Artiste trouvé",
 *     ),
 *     @OA\Response(response=404, description="Artiste non trouvé")
 * )
 */

    //  Afficher un artiste précis
    public function show(Artist $artist)
    {
        return $artist;
    }

    /**
 * @OA\Get(
 *     path="/api/artists/genre/{genre}",
 *     summary="Lister les artistes par genre",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="genre",
 *         in="path",
 *         required=true,
 *         description="Genre musical",
 *         @OA\Schema(type="string", example="Rap")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des artistes",
 *     ),
 *     @OA\Response(response=404, description="Aucun artiste trouvé pour ce genre")
 * )
 */

     //  Lister les artistes par genre
     public function getByGenre($genre)
     {
         $artists = Artist::where('genre', $genre)->get();
 
         if ($artists->isEmpty()) {
             return response()->json(['message' => 'Aucun artiste trouvé pour ce genre'], 404);
         }
 
         return response()->json($artists, 200);
     }

     /**
 * @OA\Get(
 *     path="/api/artists/name/{name}",
 *     summary="Trouver un artiste par nom",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="name",
 *         in="path",
 *         required=true,
 *         description="Nom de l'artiste",
 *         @OA\Schema(type="string", example="Eminem")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Artiste trouvé",
 *     ),
 *     @OA\Response(response=404, description="Artiste non trouvé")
 * )
 */
 
     //  Trouver un artiste par nom
     public function getByName($name)
     {
         $artist = Artist::where('name', 'LIKE', "%$name%")->first();
 
         if (!$artist) {
             return response()->json(['message' => 'Artiste non trouvé'], 404);
         }
 
         return response()->json($artist, 200);
     }

    
 
     //  Trouver un artiste par ID
     public function getById($id)
     {
         $artist = Artist::find($id);
 
         if (!$artist) {
             return response()->json(['message' => 'Artiste non trouvé'], 404);
         }
 
         return response()->json($artist, 200);
     }


      /**
 * @OA\Get(
 *     path="/api/artists/{id}/details",
 *     summary="Afficher un artiste avec ses albums",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'artiste",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails de l'artiste et ses albums",
 *     ),
 *     @OA\Response(response=404, description="Artiste non trouvé")
 * )
 */

    // affiche albums de artist

     public function details($id)
    {
         $artist = Artist::with('albums')->find($id);

         if (!$artist) {
             return response()->json(['message' => 'Artiste non trouvé'], 404);
         }

        return response()->json($artist, 200);
    }

/**
 * @OA\Put(
 *     path="/api/artists/{id}",
 *     summary="Mettre à jour un artiste",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'artiste",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Slim Shady"),
 *             @OA\Property(property="genre", type="string", example="Hip-Hop"),
 *             @OA\Property(property="country", type="string", example="USA")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Artiste mis à jour",
 *     ),
 *     @OA\Response(response=404, description="Artiste non trouvé")
 * )
 */

    //  Mettre à jour un artiste
    public function update(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'genre' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $artist->update($validated);

        return response()->json($artist, 200);
    }

/**
 * @OA\Delete(
 *     path="/api/artists/{id}",
 *     summary="Supprimer un artiste",
 *     tags={"Artists"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'artiste",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=204, description="Artiste supprimé avec succès"),
 *     @OA\Response(response=404, description="Artiste non trouvé")
 * )
 */

    //  Supprimer un artiste
    public function destroy(Artist $artist)
    {
        $artist->delete();
        return response()->json(null, 204);
    }
}