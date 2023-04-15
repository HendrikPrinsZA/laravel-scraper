<?php

use App\Actions\Scrapers\FetchBlogPost;
use App\Exceptions\ApiException;
use App\Http\Controllers\BicycleController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'scrapers'], function () {
    Route::post('blog', FetchBlogPost::class);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('token', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::firstWhere('email', $request->email);
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw new ApiException('Invalid credentials');
        }

        // Revoke existing tokens
        $user->revokeAllTokens();

        return [
            'success' => true,
            'token' => $user->createToken('api', ['api-full'])->plainTextToken,
        ];
    });

    Route::post('register', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
        ]);

        if (User::firstWhere('email', $request->email)) {
            throw new ApiException('The email already exists');
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
        ]);

        // TODO: Send email verification here
        // - Bypassed for now
        $user->markEmailAsVerified();

        return [
            'success' => true,
            'message' => 'Sucessfully registered',
        ];
    });
});

Route::middleware(['auth:sanctum', 'abilities:api-full'])->group(function () {
    Route::get('me', fn (Request $request) => $request->user());

    Route::apiResource('bicycles', BicycleController::class);
});
