<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Access;
use Validator;
use Hash;
use Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'auth.admin'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user', [
            'users' => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if( User::where('email' , '=' , $input['email'])->count() > 0){
            //已經被管理者刪除的帳號要重新加回去
            User::where('email' , '=' , $input['email'])->update(['is_deleted' => false]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'level' => 'required|integer',
                'email'  => 'required|email|unique:users',
                'phone' => 'required',
                'password' => 'required|confirmed',
            ]);

            if ($validator->fails()) {
                return redirect('/user')
                    ->withErrors($validator)
                    ->withInput(); // Request::old('field')
            }


            $input['password'] = Hash::make($input['password']);
            $input['fb_id'] = 0;
            $created = User::create($input);
        }

        return redirect('/user')->with('status', 'User created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'level' => 'required|integer',
            'password' => 'confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('/user')
                ->withErrors($validator)
                ->withInput(); // Request::old('field')
        }

        $input = $request->all();
        unset($input['_token']);
        unset($input['password_confirmation']);

        if ($input['password'] == null){
            unset($input['password']);
        }else{
            $input['password'] = Hash::make($input['password']);
        }

        User::where('id' , '=' , $id)->update($input);
        return redirect('/user')->with('status', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ( $id == 1 ){
            abort(403);
            redirect('/user');
        } else {
            Log::debug('Going to delete id = ' . $id);
            User::where('id' , '=' , $id)->update(['is_deleted' => true] , ['password' => null]);
            Access::where('user_id' , '=' , $id)->delete();
            Log::debug('delete id = ' . $id);
            redirect('/user');
        }
    }
}
