<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller

{

    public function createTask(Request $request) {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',

        ]);
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()->all(),'status'=>404],404);
        }
        if(!$user) {
            return  response()->json([
                "statusCode"=> 401,
                "message"=>'Unauthorized'
            ],401);
        }
        $title = $request->title;
        $description = $request->description;
        $status = 'OPEN';
       $taskId = DB::table('tasks')
        ->insertGetId([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'userId' => $user->id
        ]);

        return  response()->json([
            "title"=> $title,
    "description"=>$description,
    "status"=>$status,
    "userId"=> $user->id,
    "id"=> $taskId
        ],201);
    }
    public function getTasks(Request $request) {
        $userId = $request->user()->id;
        $tasksQuery = DB::table('tasks')
        ->where('userId', $userId);



     if(isset($_GET['status'])) {
        $tasksQuery = $tasksQuery
        ->where('status', $_GET['status']);
    }
    if(isset($_GET['search'])) {
        $tasksQuery = $tasksQuery
        ->where('title', 'LIKE', "%{$_GET['search']}%");

    }
    $tasks = $tasksQuery
     ->get();
     return response()->json($tasks,200);

    }
    public function getTaskById(Request $request,$id){
    $taskQuery = DB::table('tasks')
    ->where(['id' => $id,'userId'=>$request->user()->id]);
    if($taskQuery->exists()) {
        $task = $taskQuery->get();
       return response()->json($task,200);
    }
    else {

         return response()->json(

            [
                "statusCode"=> 404,
                "message"=> "Task with " . $id . " not found",
                "error"=> "Not Found"

            ],404
         );
    }
    }
    public function deleteTaskById(Request $request,$id) {
  $result = DB::table('tasks')
   ->where(
       [
'id' => $id,
'userId' => $request->user()->id
       ]
   )
   ->delete();
   return response()->json($result,200);

    }

    public function updateTaskById(Request $request,$id) {

    $taskQuery   =    DB::table('tasks')
        ->where([
            'id' =>$id,
            'userId' => $request->user()->id
        ]);
    $taskQuery
        ->update([
            'status' => $request->status
        ]);
   $task =  $taskQuery
            ->get();
        return response()->json($task,200);


    }
}
