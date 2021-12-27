<?php


namespace App\Repository;


use App\Http\Resources\CustomerResource;
use App\Models\Checkin;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CustomerRepository
{
    public function save(): Customer
    {
        $data = request()->only(['name', 'email']);

        if (request()->has('password') and request('password'))
            $data['password'] = bcrypt(request()->password);

        return Customer::updateOrCreate(['id' => request()->id], $data);
    }

    public function updateField(): array
    {
        $ids = explode('_', request()->pk);

        switch (request('name')) {
            case 'name':
                Customer::where(['id' => $ids[0]])->update([request()->name => request()->value]);
            case 'email':
                $validator = Validator::make(request()->all(), [
                    'value' => 'required|unique:customers,email,' . request('id')
                ]);

                if ($validator->fails())
                    return ['status' => false, 'message' => implode(' ', $validator->errors()->all())];

                Customer::where(['id' => $ids[0]])->update([request()->name => request()->value]);
                break;
            default:
                return ['status' => false, 'message' => __('all.action_not_recognized')];
        }//..... end of switch() .....//

        return ['status' => true, 'message' => __('all.value_saved')];
    }

    public function delete(): array
    {
        $status = Customer::destroy(request()->id);

        return ['status' => !!$status, 'message' => $status ? __('all.record_deleted') : __('all.incomplete_action')];
    }

    public function dataTable()
    {
        return Datatables::of(Customer::query())
            ->addColumn('action', function ($user) {
                return '<a href="javascript:void(0)" class="edit btn btn-round btn-info btn-sm"><i class="fa fa-edit"></i></a> | <a href="javascript:void(0)" class="delete btn btn-round btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
            })->rawColumns(['action'])->make(true);
    }

    public function checkInDataTable($id)
    {
        return Datatables::of(Checkin::where('customer_id', $id))
            ->addColumn('date', function ($record) {
                return $record->created_at->format("Y-m-d H:i:s A");
            })->addColumn('action', function ($record) {
                return '<a href="javascript:void(0)" data-id="'.$record->customer_id.'" class="viewOnMap btn btn-round btn-info btn-sm"><i class="fa fa-eye"></i></a>';
            })->rawColumns(['action'])->make(true);
    }

    public function single($id): CustomerResource
    {
        return new CustomerResource(Customer::findOrFail($id));
    }
}
