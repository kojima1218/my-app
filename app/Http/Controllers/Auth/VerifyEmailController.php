<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, '無効な確認リンクです。');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/email/verify/success');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return redirect('/email/verify/success');
    }
}
