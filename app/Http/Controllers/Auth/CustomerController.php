<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Notifications\PasswordReset;
use App\Repository\Repository;
use App\Repository\SubscriberRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function authenticate(Request $request)
    {

        $user = Customer::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ['status' => false, 'error' => 'Invalid credentials.', 'message' => 'Invalid credentials.'];
        }

        return [
            'token' => $user->createToken($user->email)->plainTextToken,
            'user' => new \App\Http\Resources\CustomerResource($user)
        ];
    }

    public function user(Request $request)
    {
        return new \App\Http\Resources\CustomerResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

    public function register(Request $request)
    {
        if (Customer::where('email', $request->email)->first())
            return ['status' => false, 'message' => 'Email already taken, Please use another email'];

        $data = $request->only(['name', 'email', 'phone']);
        $data['password'] = bcrypt($request->password ?? Str::random(6));

        Customer::create($data);

        if ($request->subscribe_newsletter)
            (new SubscriberRepository())->save($request->email);

        if ($request->loggedIn)
            return ['status' => true, 'user' => $this->authenticate($request)];

        return ['status' => true, 'message' => 'User registered successfully. Please login using your credentials.'];
    }

    public function resetPassword(Request $request)
    {
        $customer = Customer::whereEmail($request->email)->firstOrFail();
        $customer->password_reset_token = Str::random(20);
        $customer->save();

        $customer->notify(new PasswordReset($customer->password_reset_token));

        return ['status' => true, 'message' => 'Password reset link is sent to your email, Please check your inbox.'];
    }

    public function loadResetForm($code)
    {
        $customer = Customer::where('password_reset_token', $code)->first();

        if (!$customer)
            abort(410, 'Link Expired.');

        return view('customer_password_reset', ['customer' => $customer]);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6'
        ]);

        $customer = Customer::where('password_reset_token', $request->code)->first();

        if (!$customer)
            abort(410, 'Link Expired.');

        $customer->password = bcrypt($request->password);
        $customer->password_reset_token = null;
        $customer->save();

        return ['message' => 'Your password changed successfully.'];
    }

    /**
     * @param Request $request
     * @return array
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user();
        $data = $request->only(['name']);

        if ($request->file)
            $data['image'] = (new Repository())->uploadBase64Image($request->file);

        $customer->update($data);

        return ['status' => true, 'message' => 'Profile updated successfully.'];
    }

    /**
     * @param Request $request
     * Change loggedIn Customer password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'password' => 'required|min:6',
            'old_password' => 'required'
        ]);

        if ($validator->fails())
            return ['status' => false, 'message' => $validator->errors()];

        $customer = $request->user();

        if (! Hash::check($request->old_password, $customer->password))
            return ['status' => false, 'message' => 'Old password is incorrect, please enter correct one.'];

        $customer->password = bcrypt($request->password);
        $customer->save();

        return ['status' => true, 'message' => 'Your password changed successfully.'];
    }
}
