<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Album;

class SongController extends Controller
{
     //  Liste des chansons
     public function index()
     {
         return Song::with('album.artist')->paginate(10);
     }
 
     //  Créer une chanson
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
     public function show($id)
     {
         $song = Song::with('album.artist')->find($id);
 
         if (!$song) {
             return response()->json(['message' => 'Chanson non trouvée'], 404);
         }
 
         return response()->json($song, 200);
     }
 
     //  Mettre à jour une chanson
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
     public function destroy($id)
     {
         $song = Song::find($id);
 
         if (!$song) {
             return response()->json(['message' => 'Chanson non trouvée'], 404);
         }
 
         $song->delete();
 
         return response()->json(['message' => 'Chanson suprimee'], 204);
     }
}
