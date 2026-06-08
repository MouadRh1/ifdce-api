<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Diploma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    // Get all fields
    public function index()
    {
        $fields = Field::with('diploma')->get();
        return response()->json($fields);
    }

    // Get fields for a specific diploma
    public function getFieldsByDiploma($diplomaId)
    {
        $fields = Field::where('diploma_id', $diplomaId)->get();
        return response()->json($fields);
    }

    // Create new field
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diploma_id' => 'required|exists:diplomas,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $field = Field::create($request->all());
        $field->load('diploma'); // Load the diploma relationship

        return response()->json([
            'message' => 'Field created successfully',
            'field' => $field
        ], 201);
    }

    // Get single field
    public function show($id)
    {
        $field = Field::with('diploma')->findOrFail($id);
        return response()->json($field);
    }

    // Update field
    public function update(Request $request, $id)
    {
        $field = Field::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'diploma_id' => 'required|exists:diplomas,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $field->update($request->all());
        $field->load('diploma');

        return response()->json([
            'message' => 'Field updated successfully',
            'field' => $field
        ]);
    }

    // Delete field
    public function destroy($id)
    {
        $field = Field::findOrFail($id);
        $field->delete();
        
        return response()->json(['message' => 'Field deleted successfully']);
    }

    // Get available diplomas for dropdown
    public function getAvailableDiplomas()
    {
        $diplomas = Diploma::where('is_active', true)->get(['id', 'name', 'level']);
        return response()->json($diplomas);
    }
}