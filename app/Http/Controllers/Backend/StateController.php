<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\State;
use Intervention\Image\Facades\Image;


class StateController extends Controller
{
    public function AllState(){

        $state = State::latest()->get();
        return view('backend.state.all_state', compact('state'));
    }

    public function AddState(){

        return view('backend.state.add_state');
    }

    public function StoreState(Request $request){

        $image = $request->file('state_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,275)->save('upload/state/'.$name_gen);
        $save_url = 'upload/state/'.$name_gen;
         
         // Validation 
        $request->validate([
            'state_name' => 'required|unique:states|max:200',

        ]);

        State::insert([

            'state_name' => $request->state_name,
            'state_image' => $save_url,
        ]);

           $notification = array(
            'message' => 'State Created Successfully!',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification); 
    }


    public function EditState($id){

        $state = State::findOrFail($id);
        return view('backend.state.edit_state', compact('state'));
    }

    public function UpdateState(Request $request){

        $state_id = $request->id;

        if ($request->file('state_image')) {

        $image = $request->file('state_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(370,275)->save('upload/state/'.$name_gen);
        $save_url = 'upload/state/'.$name_gen;
         
        State::findOrFail($state_id)->update([

            'state_name' => $request->state_name,
            'state_image' => $save_url,
        ]);

           $notification = array(
            'message' => 'State Updated with Image Successfully!',
            'alert-type' => 'success'
        );

           return redirect()->route('all.state')->with($notification); 
        }else{

            State::findOrFail($state_id)->update([

            'state_name' => $request->state_name,
        ]);

           $notification = array(
            'message' => 'State Updated without Image Successfully!',
            'alert-type' => 'success'
        );

           return redirect()->route('all.state')->with($notification); 
        }
        
    }

     public function DeleteState($id){

        $state = State::findOrFail($id);
        $img = $state->state_image;
        unlink($img);

        State::findOrFail($id)->delete();

        $notification = array(
            'message' => 'State Deleted Successfully!',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 
    }

}
