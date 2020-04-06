<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Events\UserCreatedEvent;
use App\Events\ConfirmEmailEvent;
class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];
        $this->validate($request,$rules);
        
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerification();
        $data['admin'] = User::REGULAR_USER;
        
        $user = User::create($data);
        
        //Trigger event
        event(new UserCreatedEvent($user));
        //Return data
        return $this->showOne($user,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //$user = User::findOrFail($id);
        return $this->showOne($user,200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $updatedEmail = false;
        //$user = User::findOrFail($id);
        $rules = [
            'name' => 'min:4',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];
        $this->validate($request,$rules);
        
        if($request->has('name')){
            $user->name = $request->name;
        }
        
        if($request->has('email') && $request->email != $user->email){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerification();
            $user->email = $request->email;
            $updatedEmail = true;
        }
        
        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }
        
        if($request->has('admin')){
            if(!$user->isVerified()){
                return $this->errorResponse('Only verified users can set as admin', 409);
            }
            $user->admin = $request->admin;
        }
        
        //check the model has been edited (isDirty)
        if(!$user->isDirty()){
            return $this->errorResponse('Nothing to update', 422);
        }
        
        $user->save();
        if($updatedEmail){
            //Trigger event
            event(new ConfirmEmailEvent($user)); 
        }
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //$user = User::findOrFail($id);
        $user->delete();
        return response()->json(['data',$user],200);
    }
    
    public function verify($token){
        $user = User::where('verification_token',$token)->firstOrFail();
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return $this->showMessage('User account verified');
    }
    
    public function resend(User $user){
        if($user->isVerified()){
            return $this->errorResponse('User email already verified', 409);
        }
        
        //Trigger event
        event(new ConfirmEmailEvent($user));         
        return $this->showMessage('Verification email has been sent');
    }
}
