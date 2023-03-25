<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Admin;
use Image;
use Auth;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {

        $banners = Banner::get()->toArray();

        // dd($banners);
        return view('admin.banners.banners')->with(compact('banners'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $banners = Banner::get()->toArray();
        $banner = Banner::find($request->id)->toArray();
        // dd ($banner);

        if (Gate::forUser(Auth::guard('admin')->user())->allows('isEmployee')) {
            abort(403);
        }
        return view('admin.banners.update_banner', compact('banner', 'banners'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $banner = Banner::find($request->id);
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'banner_name' => 'required',
                // 'banner_des' => 'required|max:500',
                // 'banner_link' => 'required',
                // 'banner_type' => 'required',
            ];

            $customMessage = [
                'banner_name.required' => 'Banner Name is required!',
                // 'banner_des.required' => 'Banner Description is required!',
                // 'banner_type.required' => 'Banner Type is required!',
                // 'banner_link.required' => 'Banner Link is required!',
            ];

            $this->validate($request, $rules, $customMessage);

            // Upload banner image
            if($request->hasFile('banner_image')) {
                $image_tmp = $request->file('banner_image');

                if($image_tmp->isValid()) {
                    // Get image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'storage/images/banners/'.$imageName;
                    // Upload new image
                    Image::make($image_tmp)->save($imagePath);

                    // Delete old image in folder
                    unlink('storage/images/banners/'.$data['current_banner_image']);
                }
            } else if (!empty($data['current_banner_image'])) {
                $imageName = $data['current_banner_image'];
            } else {
                $imageName = "" ;
            }

            Banner::where('id', $request->id)->update([
                'name'=>$data['banner_name'], 
                'description'=>$data['banner_des'], 
                'image'=>$imageName, 
                'type'=>$data['banner_type'], 
                'link'=>$data['banner_link']
            ]);

            return redirect('/admin/banners')->with('success_message','Banner update successfully!');
        }

        return view('admin.banners.update_banner', compact('banner'));
    }

}
