<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\FindFriendsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile-picture', [ProfileController::class, 'updatePicture'])->name('profile.update-picture');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Friends Request Routes
Route::middleware('auth')->name('friends.')->controller(FindFriendsController::class)->group(function(){
    Route::get('/find-friends', 'index')->name('index');
    Route::get('/search-friends', 'search')->name('search');
    Route::post('/add-friend', 'add')->name('add');
    Route::match(['get', 'post'],'/friend-requests', 'friendRequests')->name('requests');
    Route::post('/cancel-friend-request', 'cancelFriendRequest')->name('cancel');
    Route::post('/accept-friend-request', 'acceptFriendRequest')->name('accept');
    Route::post('/reject-friend-request', 'rejectFriendRequest')->name('reject');
});

Route::middleware('auth')->name('chat.')->controller(ChatController::class)->group(function(){
    Route::get('/chat', 'chat')->name('index');
    Route::get('/get-chat-rooms', 'getChatRooms')->name('get.chat.rooms');
    Route::get('/get-unread-message-count', 'getUnreadMessageCount')->name('get.unread.message.count');
    Route::get('/get-messages', 'getMessages')->name('get.messages');
});

Route::controller(TestController::class)->prefix('test')->group(function(){
    Route::match(['get', 'post'], 'file-upload', 'fileUpload');
    Route::get('chat', 'chat');
    Route::get('user-status', 'userStatus');
    Route::get('friends', 'friends');
});

require __DIR__.'/auth.php';
