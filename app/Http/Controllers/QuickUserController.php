<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuickUserStoreRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class QuickUserController extends Controller
{
    /**
     * Create a simple user with a fixed password and return minimal data.
     */
    public function store(QuickUserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            // Plain value, the model cast will hash it
            'password' => 'Secret*123*',
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
