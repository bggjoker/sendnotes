<?php

use Illuminate\Support\Facades\Route;
use App\Models\Note;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');


// {
//  "admin":"1|xhWIN4jAeCzBiF7KFghjhRT3iWIgybmhIMhqVXEA8aafc88d",
//  "update":"2|LI6jKKEBKCPP41hYK3YfoR8DdpkAmRa9Ij1nQYao3799b2a8",
//  "basic":"3|TCwiTVTpXYmUW9V8VuVMYOMaEGhHCYzQ3yQvhTm36b92464d"
//}
Route::get('setup', function() {
    $credentials = [
        'email' => 'admin@admin.com',
        'password' => 'password'
    ];

    if(!\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $user = new \App\Models\User();

        $user->name = 'Admin';
        $user->email = $credentials['email'];
        $user->password = \Illuminate\Support\Facades\Hash::make($credentials['password']);

        $user->save();

        if(\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::user();

            $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
            $updateToken = $user->createToken('update-token', ['create', 'update']);
            $basicToken = $user->createToken('basic-token');

            return [
                'admin' => $adminToken->plainTextToken,
                'update' => $updateToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ];
        }
    }

});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('notes', 'notes.index')
    ->middleware(['auth'])
    ->name('notes.index');

Route::view('notes/create', 'notes.create')
    ->middleware(['auth'])
    ->name('notes.create');

Livewire\Volt\Volt::route('notes/{note}/edit', 'notes.edit-note')
    ->middleware(['auth'])
    ->name('notes.edit');

Route::get('notes/{note}', function(Note $note) {
    if(!$note->is_published) {
        abort(404);
    }

    $user = $note->user;

    return view('notes.view', ['note' => $note, 'user' => $user]);
})->name('notes.view');

require __DIR__.'/auth.php';
