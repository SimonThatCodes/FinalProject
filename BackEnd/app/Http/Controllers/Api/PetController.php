<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::with('user');

        // Filter by species
        if ($request->has('species')) {
            $query->where('species', $request->species);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('breed', 'like', '%' . $request->search . '%');
            });
        }

        $pets = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($pets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('pets', 'public');
                $images[] = $path;
            }
        }

        $pet = Pet::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'species' => $request->species,
            'breed' => $request->breed,
            'age' => $request->age,
            'gender' => $request->gender,
            'description' => $request->description,
            'location' => $request->location,
            'price' => $request->price,
            'status' => 'available',
            'images' => $images,
        ]);

        return response()->json($pet->load('user'), 201);
    }

    public function show($id)
    {
        $pet = Pet::with('user', 'adoptions')->findOrFail($id);
        return response()->json($pet);
    }

    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        // Check if user owns the pet
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'species' => 'sometimes|required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:available,adopted,pending',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = $request->only([
            'name', 'species', 'breed', 'age', 'gender',
            'description', 'location', 'price', 'status'
        ]);

        if ($request->hasFile('images')) {
            // Delete old images
            if ($pet->images) {
                foreach ($pet->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('pets', 'public');
                $images[] = $path;
            }
            $updateData['images'] = $images;
        }

        $pet->update($updateData);

        return response()->json($pet->load('user'));
    }

    public function destroy(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        // Check if user owns the pet
        if ($pet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete images
        if ($pet->images) {
            foreach ($pet->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $pet->delete();

        return response()->json(['message' => 'Pet deleted successfully']);
    }

    public function myPets(Request $request)
    {
        $pets = Pet::where('user_id', $request->user()->id)
            ->with('adoptions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pets);
    }
}
