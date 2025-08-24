<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserManagementService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }
    public function allRole(){
        $role=Role::get(['name','id']);
        return response()->json($role);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'required|numeric',
            'gender' => ['required', Rule::in(['m', 'f'])],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userService->createUser($request->all());

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json($user);
    }
       public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    $data = $request->validate([
        'name' => 'sometimes|string|max:255',
        'role_id' => 'sometimes|exists:roles,id',
        'phone' => 'sometimes|numeric',
        'gender' => ['sometimes', Rule::in(['m', 'f'])],
        'email' => 'sometimes|email|unique:users,email,' . $id,
        'password' => 'sometimes|string|min:8',
        'status' => ['sometimes', Rule::in(['active', 'deleted'])],
    ]);

    if (empty($data)) {
        return response()->json(['message' => 'No data provided for update.'], 400);
    }

    if (isset($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    }

    $user->update($data);

    return response()->json(['message' => 'User updated successfully', 'user' => $user]);
}

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
    
    
public function getallManager(Request $request)
{
    $query = User::with('role')
        ->whereHas('role', function($query) {
            $query->where('name', 'Manager');
        });
    
    if ($request->has('name') && !empty($request->name)) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }
    
  
    if ($request->has('date') && !empty($request->date)) {
      $query->whereDate('created_at', \Carbon\Carbon::parse($request->date));
}
    
    if ($request->has('status') && !empty($request->status)) {
        $query->where('status', $request->status);
    }
    
    $managers = $query->get();
    
    return response()->json(['allManager' => $managers]);
}
  public function getallOwner(Request $request)
{
    $query = User::with('role')
        ->whereHas('role', function($query) {
            $query->where('name', 'Owner');
        });
    
    if ($request->has('name') && !empty($request->name)) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }
    
  
    if ($request->has('date') && !empty($request->date)) {
      $query->whereDate('created_at', \Carbon\Carbon::parse($request->date));
}
    
    if ($request->has('status') && !empty($request->status)) {
        $query->where('status', $request->status);
    }
    
    $managers = $query->get();
    
    return response()->json(['allOwner' => $managers]);
}
}
