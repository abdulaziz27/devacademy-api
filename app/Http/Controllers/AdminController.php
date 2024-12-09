<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllUsers()
    {
        return UserResource::collection(User::with('roles')->get());
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return response()->json(['error' => 'Cannot delete your own account'], 403);
            }

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->tokens()->delete();
            $user->roles()->detach();
            $user->delete();

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function promoteToTeacher(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $validated['email'])->first();
        $user->removeRole('student');
        $user->assignRole('teacher');

        return response()->json([
            'message' => 'User promoted to teacher',
            'user' => new UserResource($user->load('roles'))
        ]);
    }
}
