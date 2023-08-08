<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryAddress;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;

class AddressesController extends Controller
{
    public function index($id) {
        $address = DeliveryAddress::find($id);
        return response()->json($address);
    }

    // Find all address by user_id
    public function addresses($id) {
        $addresses = DeliveryAddress::where('user_id', $id)->get();
    
        // Format the addresses to have a line break after the first part
        foreach ($addresses as $address) {
            // Chuỗi địa chỉ ban đầu
            $address_user = $address->address;
            // Tìm vị trí của dấu phẩy
            $comma_position = strpos($address_user, ',');
            // Tách chuỗi thành hai phần dựa trên vị trí của dấu phẩy
            $user_address_detail = trim(substr($address_user, 0, $comma_position));
            $user_address = trim(substr($address_user, $comma_position + 1));
            $address->address_detail = $user_address_detail;
            $address->address = $user_address;
        }
    
        return response($addresses);
    }
    
    
    public function getCities() {
        $cities = City::select('id', 'name')->get();
        return response()->json($cities);
    }

    public function getDistricts($city)
    {
        $districts = District::where("city_id", $city)->get();
        return response()->json($districts);
    }

    public function getWards($district)
    {
        $wards = Ward::where('district_id', $district)->get();

        return response()->json($wards);

    }

    // Create new address with $user = id_user
    public function store($user, Request $request) {
        $address_first = DeliveryAddress::where('user_id', $user)->first();
        $addressOld = DeliveryAddress::where(['user_id' => $user, 
            'address' => $request->address])->first();

        $address = new DeliveryAddress;

        if(!$addressOld) {
            if(!$address_first) {
                $address->default = 1;
            }
            $address->user_id = $user;
            $address->name = $request['name'];
            $address->address = $request['address_detail'] . ", " . $request['address'];
            $address->phone = $request['phone'];
            $address->save();
            return response()->json([
                'success'=>'success',
                'message'=>'Địa chỉ được thêm thành công.'
            ]);
        } else {
            return response()->json([
                'success' => 'warning',
                'message' => "Địa chỉ đã tồn tại!"
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        DeliveryAddress::where('id', $id)->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return response()->json([
            'success'=>'success',
            'message'=>'Địa chỉ được cập nhật thành công.'
        ]);
    }

    public function setDefault($id) {
        DeliveryAddress::where('id', $id)->update(['default' => 1]);
        DeliveryAddress::where('id', '!=', $id)->update(['default' => 0]);
        return response()->json([
            'success'=>'success',
            'message'=>'Địa chỉ được đặt làm mặc định.'
        ]);
    }

    public function destroy($id)
    {
        $address = DeliveryAddress::find($id);
        $address->delete();
        return response()->json([
            'success'=>'success',
            'message'=>'Địa chỉ được xóa thành công.'
        ]);
    }

}
