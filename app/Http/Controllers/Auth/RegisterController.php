<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDiplomaField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:20',
            'diploma_fields' => 'required|array|min:1',
            'diploma_fields.*.diploma_id' => 'required|exists:diplomas,id',
            'diploma_fields.*.field_id' => 'required|exists:fields,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user with hashed password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone_number' => $request->phone_number,
            ]);

            // Store diploma field selections
            foreach ($request->diploma_fields as $selection) {
                UserDiplomaField::create([
                    'user_id' => $user->id,
                    'diploma_id' => $selection['diploma_id'],
                    'field_id' => $selection['field_id'],
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Load relationships for response
            $user->load(['diplomaFields.diploma', 'diplomaFields.field']);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}