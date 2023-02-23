<?php

namespace App\Http\Controllers\ApiAdministrator\Administrator;

use App\Enums\DiskDriver;
use App\Enums\WebAdministratorRoles;
use App\Helpers\Uploader;
use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Responses\SuccessResponse;
use App\Rules\IsValidEmail;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class AdministratorController extends Controller
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

        $administrators = Administrator::searchCriteria($search)->paginate($size);

        return response()->json($administrators);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response|ResponseFactory|JsonResponse|SuccessResponse
    {
        $validated = $this->validateRequest($request);

        $administrator = new Administrator();

        if ($request->hasFile('avatar')) {
            $uploaded = Uploader::saveImage($request->file('avatar'), DiskDriver::UPLOADS);
            $administrator->avatar = $uploaded['url'];
        }

        $administrator->name = $validated->name;
        $administrator->lastname = $validated->lastname;
        $administrator->email = $validated->email;
        $administrator->password = $validated->password;
        $administrator->save();

        $administrator->assignRole($validated->role);

        return new SuccessResponse(__('Administrator :name created successfully', ['name' => "{$administrator->name} {$administrator->lastname}"]));
    }

    /**
     * Display the specified resource.
     */
    public function show(Administrator $administrator): Response|ResponseFactory|JsonResponse
    {
        return response()->json(['administrator' => $administrator]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administrator $administrator): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administrator $administrator): Response|ResponseFactory|JsonResponse
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
        $uniqueEmail = "unique:administrators,email,{$uuid},id,disabled,deleted_at,NULL";

        $request->validate([
            'avatar' => ['sometimes', 'image', 'mimes:png,jpg', 'max:8096'],
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string','email', new IsValidEmail(), $uniqueEmail],
            'password' => [$requiredPassword, Password::min(10)->letters()->numbers()],
            'role' => ['required', new Enum(WebAdministratorRoles::class)],
        ]);

        $validated->name = $request->input('name');
        $validated->lastname = $request->input('lastname');
        $validated->email = $request->input('email');
        $validated->password = Hash::make($request->input('password')) ?? null;
        $validated->role = $request->input('role');

        return $validated;
    }
}
