<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function allTodos(Request $request){
        $user = $this->getAuthUserID($request->header('Authorization'));
        $todos = Todo::where('user_id',$user->userID)->get();
        return response()->json(['todos'=>$todos],200);
    }

    public function store(Request $request){
        $user = $this->getAuthUserID($request->header('Authorization'));
        $user_id = $user->user_id;

        Todo::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'user_id'=>$user_id,
        ]);
        return response()->json(['message' => 'Todo Created Successfully..!'], 200);
    }

    public function show(Request $request, $id){
        $user = $this->getAuthUserID($request->header('Authorization'));
        $todo = Todo::where('user_id',$user->userID)->find($id);

        if(!$todo){
            return response()->json(['message' => 'Todo Not Found..!'], 404);
        }
        return response()->json(['Todo'=>$todo], 200);
    }

    public function update(Request $request, $id){
        $user = $this->getAuthUserID($request->header('Authorization'));
        $todo = Todo::where('user_id',$user->userID)->find($id);

        if(!$todo){
            return response()->json(['message' => 'Todo Not Found..!'], 404);
        }
        Todo::update([
            'title'=>$request->title,
            'description'=>$request->description,
        ]);
        return response()->json(['message' => 'Todo Updated Successfully..!'], 200);
    }

    public function destroy(Request $request, $id){
        $user = $this->getAuthUserID($request->header('Authorization'));
        $todo = Todo::where('user_id',$user->userID)->find($id);

        if(!$todo){
            return response()->json(['message' => 'Todo Not Found..!'], 404);
        }
        $todo->delete();
        return response()->json(['message' => 'Todo Deleted Successfully..!'], 200);
    }

    public static function getAuthUserID($token){
        $key =env('JWT_KEY');
        $decode=JWT::decode($token,new Key($key,'HS256'));
        return $decode->userID;
    }

}
