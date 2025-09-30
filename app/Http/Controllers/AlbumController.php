<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Album;

class AlbumController extends Controller
{
    //  Liste des albums
    public function index()
    {
        return Album::with('artist')->paginate(10);
    }

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
    public function show($id)
    {
        $album = Album::with('artist')->find($id);

        if (!$album) {
            return response()->json(['message' => 'Album non trouvé'], 404);
        }

        return response()->json($album, 200);
    }

    //  Mettre à jour un album
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
