<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\User;


// Breeze の認証ルート
require __DIR__.'/auth.php';

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/test-mail', function () {
    Mail::to('kojimashugo.1218@gmail.com')->send(new TestMail());
    return 'テストメールを送信しました！';
});

// ✅ ここを修正！！ VerifyEmailControllerを使わずクロージャ関数で対応
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::findOrFail($request->route('id'));

    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403, '無効な確認リンクです。');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return response()->json(['message' => '✅ メール認証が完了しました！ログインしてください。']);
})->middleware(['signed'])->name('verification.verify');

// 認証後の表示用ルート（任意）
Route::get('/email/verify/success', function () {
    return '✅ メール認証が完了しました！ログインしてください。';
});





