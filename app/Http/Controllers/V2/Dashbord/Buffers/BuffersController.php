<?php

namespace App\Http\Controllers\V2\Dashbord\Buffers;

use App\Models\buffer;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\ImageController;
// use App\Http\Requests\Upvela_Max\buffer\Dashbord\createBuffersRequest;

class BuffersController extends Controller
{
   
  
    use ResponseJson;

    public function index()
    {
         $buffers=buffer::get();

         return $buffers;
    }

  
    public function create()
    {
        //
    }


    public function store(createBuffersRequest $request)
    {

        $image=new ImageController();
        $path=$request['path']='buffers';
       $check=$image->uploadImage($request);
      if($check['success']==true)
      {
        $buffers=buffer::create([
            'name'=>$request['name'],
            'amount'=>$request['amount'],
            'img'=>$check['filename'],
        ]);

        return $this->success("operation accomplished successfully");
      }else{
        return $this->error("You Have Error In Image");
      }


    }


    public function show($id)
    {

        $buffers=buffer::where('id',$id)->first();

        if(!$buffers)
        {
           return $this->error("Not Have BUffer throught the id ");
        }
        $buffers_user=buffer_user::where('buffer_id',$id)->count();
        $buffers->count_subscribtion=$buffers_user;


      return $buffers;
        return $this->success($buffers);





    }

 
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {

         $buffers=buffer::where('id',$id)->first();

         return $request;
         if ($request->hasFile('img')) {
            $imageController = new ImageController();



         }

    }


    public function destroy($id)
    {
         $buffers=buffer::where('id',$id)->first();
        $imageController = new ImageController();
         $pathToDelete = "buffers/" . "$buffers->img";
         $imageController->delete($pathToDelete);
        $buffers->delete();
        return $this->success("operation accomplished successfully");




    }

    public function updatdbuffer(Request $request,$id)
    {
         $buffers=buffer::where('id',$id)->first();
         $imageController = new ImageController();
         $pathToDelete = "buffers/" . "$buffers->img";
         $request['path']="buffers";
         $request['path_delete']=$pathToDelete;



        if ($request->hasFile('img')) {
        $check=$imageController->updatedImage($request);
        $img=$check['filename'];

        }else{
            $img=$buffers->img;
        }

        $buffers->update([
            'name'=>$request['name'],
            'amount'=>$request['amount'],
            'img'=>$img,
        ]);

        return $this->success("operation accomplished successfully");





    }
}

