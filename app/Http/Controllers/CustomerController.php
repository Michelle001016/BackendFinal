<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Customer;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Imports\ContactsImport;
use App\Exports\ContactExport;
Use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name','desc')->paginate(6);
        // return ([
        // 'id' => $this->id,
        // 'name' => $this->name,
        // 'email' => $this->email,
        // ],200);
        return response()->json([
            'Success'=>true,
            'Data'=> $customers
         ],200);
    }

    public function show($id)
    {
        $customer = auth()->customer()->find($id);
        if(!$customer){
            return response()->json([
                'Success'=>false,
                'Data'=> 'customer with id'.$id.'not found',
            ],404);
    }
        return response()->json([
            'Success'=>true,
            'Data'=> $customer->toArray(),
        ],202);
    }

    public function store(StoreUserRequest $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        return new UserResource($customer);
    }

    public function update(StoreUserRequest $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer){
             return response()->json([
                    'success'=>false,
                     'data'=>'customer with id'.$id.'not found',
                 ],404);
        }
        $customer->fill($request->all());
        $customer->save();
    
        if($customer){
        return response()->json([
            'Success'=>true,
            'Message'=>'Customer updated successfully',
            'data' => $customer
            ],202);
        } else {
        return response()->json([
            'Success'=>false,
            'Message'=>'Customer could not be updated',
            ],401);
        }

        $customer = Customer::update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return new UserResource($customer); 
    }

    public function destroy(Customer $customer)
    {
        $customer = Customer::find($id);

        if(!$customer){
            return response()->json([
                'Success'=>false,
                'Message'=>'Customer with id'.$id.'not found'
            ],404);
        } if($product->delete()){
            return response()->json([
                'Success'=>true,
                'Message'=>'Customer deleted successfully'
            ]);
        } else {
            return response()->json([
                'Success'=>false,
                'Message'=>'Customer could not be deleted'
            ],401);
        }
    }
    public function import(Request $request)
    { 
        $customers = Excel::toCollection(new ContactsImport, $request->file('test'));
        
            foreach ($customers[0] as $customer){
                $validate = Validator::make([
                    'name'   =>$customer[1],
                    'email'  =>$customer[2],
                ],
                [
                    'name'   =>'required',
                    'email'  =>'required',
                ]); 

                $check = Customer::where('name','=',$customer[1])->first();
                if(!isset($customer[7])){
                    continue;
                } else{
                    if ($check && $customer[8] == "update")
                    {
                        Customer::where('name',$customer[1]->update([
                            'name' =>$customer[1],
                        ]));
                    }
                        if(!$check && $customer[7] == "create")
                        {
                            if($validate->fails())
                            {
                                continue;
                            } else {
                                Customer::create([
                                    'name'   =>$customer[1],
                                    'email'  =>($customer[2])
                                ]);
                                   }
                        }
                        if($check && $customer[7] == "delete")
                        {
                            Customer::where('name',$customer[2])->delete();
                        }
                    } 
                }
                return response()->json([
                    'Success'=>true,
                    'Message'=>'Import done'
                ],200);
    }
    public function export($slug)
    {
    return Excel::download(new ContactsExport, 'customers.'.$slug);
    }
}
