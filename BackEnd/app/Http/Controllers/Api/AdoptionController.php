<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Adoption;
use App\Models\Pet;
use Illuminate\Http\Request;

class AdoptionController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $adoptions = Adoption::where('adopter_id', $userId)
            ->orWhereHas('pet', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['pet.user', 'adopter'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($adoptions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $pet = Pet::findOrFail($request->pet_id);

        // Check if pet is available
        if ($pet->status !== 'available') {
            return response()->json(['message' => 'Pet is not available for adoption'], 400);
        }

        // Check if user already has a pending adoption for this pet
        $existingAdoption = Adoption::where('pet_id', $pet->id)
            ->where('adopter_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingAdoption) {
            return response()->json(['message' => 'You already have a pending adoption request for this pet'], 400);
        }

        $adoption = Adoption::create([
            'pet_id' => $pet->id,
            'adopter_id' => $request->user()->id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Update pet status to pending
        $pet->update(['status' => 'pending']);

        return response()->json($adoption->load(['pet.user', 'adopter']), 201);
    }

    public function show($id)
    {
        $adoption = Adoption::with(['pet.user', 'adopter'])->findOrFail($id);
        return response()->json($adoption);
    }

    public function update(Request $request, $id)
    {
        $adoption = Adoption::with('pet')->findOrFail($id);
        $userId = $request->user()->id;

        // Only pet owner can update adoption status
        if ($adoption->pet->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $adoption->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $adoption->notes,
        ]);

        // Update pet status based on adoption status
        if ($request->status === 'approved') {
            $adoption->pet->update(['status' => 'pending']);
        } elseif ($request->status === 'completed') {
            $adoption->pet->update(['status' => 'adopted']);
            $adoption->update(['adopted_at' => now()]);
        } elseif ($request->status === 'rejected') {
            $adoption->pet->update(['status' => 'available']);
        }

        return response()->json($adoption->load(['pet.user', 'adopter']));
    }
}
