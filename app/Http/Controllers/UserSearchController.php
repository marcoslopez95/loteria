<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
