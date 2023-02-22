<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\DiskDriver;
use App\Enums\WebRoles;
use App\Helpers\Uploader;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\IsValidEmail;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request): Response|ResponseFactory|JsonResponse
    {
        /** @var int $size */
        $size = $request->input('size') ?? 25;
        /** @var string $search */
        $search = $request->input('search') ?? '';

        $users = User::searchCriteria($search)->paginate($size);

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response|ResponseFactory|JsonResponse
    {
        $validated = $this->validateRequest($request);

        if ($request->hasFile('avatar')) {
            $uploaded = Uploader::saveImage($request->file('avatar'), DiskDriver::UPLOADS);
            $validated->avatar = $uploaded['url'];
        } else {
            $validated->avatar = null;
        }

        $user = new User([
            'avatar' => $validated->avatar,
            'name' => $validated->name,
            'lastname' => $validated->lastname,
            'phone' => $validated->phone,
            'email' => $validated->email,
            'password' => $validated->password,
        ]);

        $user->assignRole($validated->role);

        return response()->json(['user' => $user]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response|ResponseFactory|JsonResponse
    {
        return response()->json(['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): Response|ResponseFactory|JsonResponse
    {
        $validated = $this->validateRequest($request, $user->uuid);

        if ($request->hasFile('avatar')) {
            $uploaded = Uploader::saveImage($request->file('avatar'), DiskDriver::UPLOADS);
            $user->avatar = $uploaded['url'];
        }

        $user->name = $validated->name;
        $user->lastname = $validated->lastname;
        $user->phone = $validated->phone;
        $user->email = $validated->email;
        $user->password = $validated->password;
        $user->save();

        $user->syncRoles($validated->role);

        return response()->json(['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }

    /**
     * Handle request validation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     */
    protected function validateRequest(Request $request, string $uuid = 'NULL'): object
    {
        $validated = (object)[];

        $requiredPassword = $uuid === 'NULL' ? 'required' : 'sometimes';
        $uniqueEmail = "unique:users,email,{$uuid},id,disabled,deleted_at,NULL";

        $request->validate([
            'avatar' => ['sometimes', 'image', 'mimes:png,jpg', 'max:8096'],
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:10'],
            'email' => ['required', 'string','email', new IsValidEmail(), $uniqueEmail],
            'password' => [$requiredPassword, Password::min(10)->letters()->numbers()],
            'role' => ['required', new Enum(WebRoles::class)],
        ]);

        $validated->name = $request->input('name');
        $validated->lastname = $request->input('lastname');
        $validated->phone = $request->input('phone');
        $validated->email = $request->input('email');
        $validated->password = Hash::make($request->input('password')) ?? null;
        $validated->role = $request->input('role');

        return $validated;
    }
}
