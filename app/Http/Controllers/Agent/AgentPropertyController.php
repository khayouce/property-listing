<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\PropertyType;
use App\Models\Amenities;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\PackagePlan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyMessage;
use App\Models\State;
use App\Models\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduleMail;

class AgentPropertyController extends Controller
{
    public function AllAgentProperty(){

        $id = Auth::user()->id;
        $property = Property::where('agent_id',$id)->latest()->get();
        return view('agent.property.all_property', compact('property'));
    }

    public function AgentAddProperty(){

        $propertyType = PropertyType::latest()->get();
        $pstate = State::latest()->get();
        $amenities = Amenities::latest()->get();

        $id = Auth::user()->id;
        $property = User::where('role','agent')->where('id',$id)->first();
        $pcount = $property->credit;

        if ($pcount == 1 || $pcount == 7) {
            return redirect()->route('buy.package');
        }else{
              return view('agent.property.add_property', compact('propertyType','amenities','pstate'));
        }
    }

    public function AgentStoreProperty(Request $request){

        $id = Auth::user()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        $amen = $request->amenities_id;
        $amenities = implode(",", $amen);

        $pcode = IdGenerator::generate(['table' => 'properties','field' => 'property_code','length' => 5, 'prefix' => 'PC']);

        $image = $request->file('property_thumbnail');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,250)->save('upload/property/thumbnail/'.$name_gen);
        $save_url = 'upload/property/thumbnail/'.$name_gen;

        $property_id = Property::insertGetId([

            'ptype_id' => $request->ptype_id,
            'amenities_id' => $amenities,
            'property_name' => $request->property_name,
            'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)),
            'property_code' => $pcode,
            'property_status' => $request->property_status,
            'lowest_price' => $request->lowest_price,
            'max_price' => $request->max_price,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'garage' => $request->garage,
            'garage_size' => $request->garage_size,
            'property_size' => $request->property_size,
            'property_video' => $request->property_video,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'neighborhood' => $request->neighborhood,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'featured' => $request->featured,
            'hot' => $request->hot,
            'agent_id' => Auth::user()->id,
            'status' => 1,
            'property_thumbnail' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        // Multiple Image Upload from Here //

        $images = $request->file('multi_img');
        foreach($images as $img){
            $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            Image::make($img)->resize(770,520)->save('upload/property/multi-image/'.$make_name);
            $uploadPath = 'upload/property/multi-image/'.$make_name;
        
        MultiImage::insert([

            'property_id' => $property_id,
            'photo_name' => $uploadPath,
            'created_at' => Carbon::now(),
        ]);

        } // End Foreach

         // End Multiple Image Upload from Here //

        // Facility Add Start From Here //
            
        $facilities = Count($request->facility_name);
        if ($facilities != NULL) {
            for ($i=0; $i < $facilities; $i++){
                $fcount = new Facility();
                $fcount->property_id = $property_id;
                $fcount->facility_name = $request->facility_name[$i];
                $fcount->distance = $request->distance[$i];
                $fcount->save();
            }
        }

        // End Facility Add From Here //

        User::where('id',$id)->update([

            'credit' => DB::raw('1 + '.$nid),
        ]);

        $notification = array(
            'message' => 'Property Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.agent.property')->with($notification);
    }

    public function AgentEditProperty($id){

        $facilities = Facility::where('property_id',$id)->get();
        $property = Property::findOrFail($id);
        $type = $property->amenities_id;
        $property_amenities = explode(',', $type);

        $multiImage = MultiImage::where('property_id',$id)->get();

        $pstate = State::latest()->get();
        $propertyType = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        return view('agent.property.edit_property',compact('property','propertyType','amenities','property_amenities','multiImage','facilities','pstate'));
    }


    public function AgentUpdateProperty(Request $request){

        $amen = $request->amenities_id;
        $amenities = implode(",", $amen);

        $property_id = $request->id;

        Property::findOrFail($property_id)->update([

            'ptype_id' => $request->ptype_id,
            'amenities_id' => $amenities,
            'property_name' => $request->property_name,
            'property_slug' => strtolower(str_replace(' ', '-', $request->property_name)),
            'property_status' => $request->property_status,
            'lowest_price' => $request->lowest_price,
            'max_price' => $request->max_price,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'garage' => $request->garage,
            'garage_size' => $request->garage_size,
            'property_size' => $request->property_size,
            'property_video' => $request->property_video,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'neighborhood' => $request->neighborhood,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'featured' => $request->featured,
            'hot' => $request->hot,
            'agent_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Property Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.agent.property')->with($notification);
    }

    public function AgentUpdatePropertyThumbnail(Request $request){

        $pro_id = $request->id;
        $oldImage = $request->old_img;

        $image = $request->file('property_thumbnail');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,250)->save('upload/property/thumbnail/'.$name_gen);
        $save_url = 'upload/property/thumbnail/'.$name_gen;

        if (file_exists($oldImage)) {
            unlink($oldImage);
        }

        Property::findOrFail($pro_id)->update([

            'property_thumbnail' => $save_url,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Property Thumbnail Image Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

     public function AgentUpdatePropertyMultiImage(Request $request){

        $imgs = $request->multi_img;

        foreach($imgs as $id => $img){
            $imgDel = MultiImage::findOrFail($id);
            unlink($imgDel->photo_name);

            $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            Image::make($img)->resize(770,520)->save('upload/property/multi-image/'.$make_name);
            $uploadPath = 'upload/property/multi-image/'.$make_name;

            MultiImage::where('id',$id)->update([

                'photo_name' => $uploadPath,
                'updated_at' => Carbon::now(),
            ]);

        }

        $notification = array(
            'message' => 'Property Multi Image Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AgentDeletePropertyMultiImage($id){

        $oldImg = MultiImage::findOrFail($id);
        unlink($oldImg->photo_name);

        MultiImage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Property Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

     public function AgentStoreNewMultiImage(Request $request){

        $new_multi = $request->imageid;
        $image = $request->file('multi_img');

         $make_name = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(770,520)->save('upload/property/multi-image/'.$make_name);
            $uploadPath = 'upload/property/multi-image/'.$make_name;

            MultiImage::insert([
                'property_id' => $new_multi,
                'photo_name' => $uploadPath,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
            'message' => 'Property Multi Image Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AgentUpdatePropertyFacilities(Request $request){

        $pid = $request->id;

        if ($request->facility_name == NULL) {

            return redirect()->back();
        }else{

            Facility::where('property_id',$pid)->delete();

            $facilities = Count($request->facility_name);
                
                    for ($i=0; $i < $facilities; $i++){
                        $fcount = new Facility();
                        $fcount->property_id = $pid;
                        $fcount->facility_name = $request->facility_name[$i];
                        $fcount->distance = $request->distance[$i];
                        $fcount->save();
                    }// End for
        }

        $notification = array(
            'message' => 'Property Facility Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method

    public function AgentPropertyDetails($id){

        $facilities = Facility::where('property_id',$id)->get();
        $property = Property::findOrFail($id);
        $type = $property->amenities_id;
        $property_amenities = explode(',', $type);

        $multiImage = MultiImage::where('property_id',$id)->get();

        $propertyType = PropertyType::latest()->get();
        $amenities = Amenities::latest()->get();
        return view('agent.property.property_details',compact('property','propertyType','amenities','property_amenities','multiImage','facilities'));
    }

    public function AgentDeleteProperty($id){

        $property = Property::findOrFail($id);
        unlink($property->property_thumbnail);

        Property::findOrFail($id)->delete();

        $image = MultiImage::where('property_id',$id)->get();
        foreach($image as $img){

            unlink($img->photo_name);
            MultiImage::where('property_id',$id)->delete();
        }

        $facilitiesData = Facility::where('property_id',$id)->get();
        foreach($facilitiesData as $item){

            $item->facility_name;
            Facility::where('property_id',$id)->delete();
        }

        $notification = array(
            'message' => 'Property Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function BuyPackage(){

        return view('agent.package.buy_package');
    }

    public function BuyBusinessPlan(){

        $id = Auth::user()->id;
        $data = User::find($id);
        return view('agent.package.business_plan',compact('data'));
    }

    public function StoreBusinessPlan(Request $request){

        $id = Auth::user()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        PackagePlan::insert([

            'user_id' => $id,
            'package_name' => 'Business',
            'invoice' => 'SPR'.mt_rand(10000000,99999999),
            'package_credit' => '3',
            'package_amount' => '25',
            'created_at' => Carbon::now(),
        ]);

         User::where('id',$id)->update([

            'credit' => DB::raw('3 + '.$nid),
        ]);


        $notification = array(
            'message' => 'You have Purchased Business Package Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.agent.property')->with($notification);
    }


    public function BuyProfessionalPlan(){

        $id = Auth::user()->id;
        $data = User::find($id);
        return view('agent.package.professional_plan',compact('data'));
    }

     public function StoreProfessionalPlan(Request $request){

        $id = Auth::user()->id;
        $uid = User::findOrFail($id);
        $nid = $uid->credit;

        PackagePlan::insert([

            'user_id' => $id,
            'package_name' => 'Professional',
            'invoice' => 'SPR'.mt_rand(10000000,99999999),
            'package_credit' => '10',
            'package_amount' => '50',
            'created_at' => Carbon::now(),
        ]);

         User::where('id',$id)->update([

            'credit' => DB::raw('10 + '.$nid),
        ]);


        $notification = array(
            'message' => 'You have Purchased Professional Package Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.agent.property')->with($notification);
    }

    public function PackageHistory(){

        $id = Auth::user()->id;
        $packagehistory = PackagePlan::where('user_id',$id)->get();

        return view('agent.package.package_history',compact('packagehistory'));
    }

    public function AgentPackageInvoice($id){

        $packagehistory = PackagePlan::where('id',$id)->first();
        $pdf = Pdf::loadView('agent.package.package_history_invoice', compact('packagehistory'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->download('invoice.pdf');
    }

    public function AgentPropertyMessage(){

        $id = Auth::user()->id;
        $usermsg = PropertyMessage::where('agent_id',$id)->get();
        return view('agent.message.all_message',compact('usermsg'));
    }

    public function AgentMessageDetails($id){

        $uid = Auth::user()->id;
        $usermsg = PropertyMessage::where('agent_id',$uid)->get();

        $msgdetails = PropertyMessage::findOrFail($id);
        return view('agent.message.message_details',compact('usermsg','msgdetails'));

    }

    public function AgentScheduleRequest(){

        $id = Auth::user()->id;
        $usermsg = Schedule::where('agent_id',$id)->get();
        return view('agent.schedule.schedule_request',compact('usermsg'));

    }

    public function AgentScheduleDetails($id){

        $schedule = Schedule::findOrFail($id);
        return view('agent.schedule.schedule_details',compact('schedule'));
    }

    public function AgentScheduleUpdate(Request $request){

        $sid = $request->id;

        Schedule::findOrFail($sid)->update([
           
            'status' => '1',
        ]);

        // Start Sending Schedule Email

        $sendmail = Schedule::findOrFail($sid);

        $data = [

            'tour_date' => $sendmail->tour_date,
            'tour_time' => $sendmail->tour_time,
        ];

        Mail::to($request->email)->send(new ScheduleMail($data));

        // End Schedule Email

         $notification = array(
            'message' => 'You have Confirmed Schedule Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('agent.schedule.request')->with($notification);
    }
}
