<?php
use App\Http\Controllers\Api\VimeoController;

Route::get('/vimeo/search', [VimeoController::class, 'search']);
