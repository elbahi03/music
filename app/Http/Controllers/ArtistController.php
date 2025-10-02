<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class ArtistController extends Controller
{
    //  Liste des artistes
    public function index()
    {
        return Artist::paginate(10);
    }

    //  Créer un nouvel artiste
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

    //  Afficher un artiste précis
    public function show(Artist $artist)
    {
        return $artist;
    }

     //  Lister les artistes par genre
     public function getByGenre($genre)
     {
         $artists = Artist::where('genre', $genre)->get();
 
         if ($artists->isEmpty()) {
             return response()->json(['message' => 'Aucun artiste trouvé pour ce genre'], 404);
         }
 
         return response()->json($artists, 200);
     }
 
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

    // affiche albums de artist

     public function details($id)
    {
         $artist = Artist::with('albums')->find($id);

         if (!$artist) {
             return response()->json(['message' => 'Artiste non trouvé'], 404);
         }

        return response()->json($artist, 200);
    }


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

    //  Supprimer un artiste
    public function destroy(Artist $artist)
    {
        $artist->delete();
        return response()->json(null, 204);
    }
}