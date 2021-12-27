<?php

namespace App\Http\Controllers;

use App\Repository\CustomerRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Load users main view.
     */
    public function index()
    {
        return view('customer.index');
    }//..... end of index() .....//

    /**
     * @return mixed
     * Load users list for dataTables.
     */
    public function loadUsersForDataTable(): mixed
    {
        return (new CustomerRepository())->dataTable();
    }//..... end of loadUsersForDataTable() ......//
    /**
     * @return mixed
     * Load users list for dataTables.
     */
    public function loadCheckinsForDataTable(Request $request): mixed
    {
        return (new CustomerRepository())->checkInDataTable($request->id);
    }//..... end of loadUsersForDataTable() ......//

    /**
     * @return array
     * Save record.
     */
    public function save(): array
    {
        if (request()->has('id') and request('id'))
            $validator = Validator::make(request()->all(), [
                'name' => 'required',
                'email' => ['required', Rule::unique('users')->ignore(request()->id)]
            ]);
        else
            $validator = Validator::make(request()->all(), [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|min:5'
            ]);

        if ($validator->fails())
            return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];

        (new CustomerRepository())->save();

        return ['status' => true, 'message' => __('all.record_saved')];
    }//..... end of save() .....//

    /**
     * @return array
     * Update single field.
     */
    public function updateField(): array
    {
        return (new CustomerRepository())->updateField();
    }//..... end of updateField() .....//

    /**
     * @return array
     * Delete user.
     */
    public function delete(): array
    {
        return (new CustomerRepository())->delete();
    }//..... end of delete() .....//
}
