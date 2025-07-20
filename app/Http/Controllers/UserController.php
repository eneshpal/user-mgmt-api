<?php

namespace App\Http\Controllers;

use App\Jobs\BulkCreateUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class UserController extends Controller
{   

    public function __construct()
    {
        //$this->middleware('role:Admin');  // Or use your own role-based middleware
    }
    /**
     * Fetch the users based on roles.
     */
    public function list(Request $request)
    {
        $authUser = Auth::user();

        if ($authUser->role_id == 4) {
            // SuperAdmin: See all users (including Admins and Users)
            $users = User::all();
        } elseif ($authUser->role_id == 5) {
            // Admin: See only regular users (exclude SuperAdmins and Admins)
            $users = User::where('role_id', 6)->get();
        } else {
            // Regular user: See only their own data
            $users = User::where('id', $authUser->id)->get();
        }

        return response()->json($users);
    }


    public function bulkCreate(Request $request)
    {
       // echo "Bulk Create called";die();

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'users' => 'required|array',
            'users.*.name' => 'required|string',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.password' => 'required|string|min:6',
            'users.*.role_id' => 'required|in:4,5,6',  // Role ID should be valid
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Dispatch the bulk creation job to the queue
        BulkCreateUsers::dispatch($request->input('users'));

        // Respond with a message indicating the job was dispatched
        return response()->json(['message' => 'User creation job has been dispatched. It will be processed shortly.']);
    }


    public function updateUser(Request $request, $id)
    {   
        $user = auth()->user();
        // dd($user->role_id);
        // Check if the authenticated user is an Admin (role_id = 5)
        if (!$user || $user->role_id !== 5) {
           return response()->json([
            'status' => false,
            'message' => 'You do not have permission to access this resource.',
            ], 403);
        }
        // Find the user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id, // ignore current user's email
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|in:4,5,6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update only the fields that were provided
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function deleteUser($id)
    {
        // Authenticated user
        $authUser = auth()->user();

        // Check admin role (assuming role_id 5 is Admin)
        if (!$authUser || $authUser->role_id !== 5) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the user to delete
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Soft delete the user
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User soft-deleted successfully.'
        ]);
    }

    public function restoreUser($id)
    {
        $authUser = auth()->user();
        
        // Check if authenticated user is an admin (role_id = 5)
        if (!$authUser || $authUser->role_id !== 5) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Find the soft-deleted user
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or not deleted.'
            ], 404);
        }

        // Restore the user
        $user->restore();

        return response()->json([
            'status' => true,
            'message' => 'User restored successfully.',
            'user' => $user
        ]);
    }

}
