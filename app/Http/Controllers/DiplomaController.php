<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiplomaController extends Controller
{
    // Get all diplomas
    public function index()
    {
        $diplomas = Diploma::all();
        return response()->json($diplomas);
    }

    // Create new diploma
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:bachelor,master,doctorate',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $diploma = Diploma::create($request->all());
        return response()->json([
            'message' => 'Diploma created successfully',
            'diploma' => $diploma
        ], 201);
    }

    // Get single diploma
    public function show($id)
    {
        $diploma = Diploma::findOrFail($id);
        return response()->json($diploma);
    }

    // Update diploma
    public function update(Request $request, $id)
    {
        $diploma = Diploma::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:bachelor,master,doctorate',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $diploma->update($request->all());
        return response()->json([
            'message' => 'Diploma updated successfully',
            'diploma' => $diploma
        ]);
    }

    // Delete diploma
    public function destroy($id)
    {
        $diploma = Diploma::findOrFail($id);
        $diploma->delete();
        return response()->json(['message' => 'Diploma deleted successfully']);
    }


    public function getDiplomasWithFields()
{
    $diplomas = Diploma::with(['fields' => function($query) {
        $query->where('is_active', true);
    }])->where('is_active', true)->get();
    
    return response()->json($diplomas);
}
}