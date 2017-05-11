<?php

class UserController extends \BaseController {

    /**
     * Display a listing of omimusers
     *
     * @return Response
     */
    // public function getIndex()
    // {
    //     // $omimusers = Omimuser::all();

    //     // return View::make('omimusers.index', compact('omimusers'));
    // }

    /**
     * Show Login Form
     *
     * @return Response
     */
    public function getLogin()
    {
        // $omimusers = Omimuser::all();
        // var_dump($omimusers);
        // die();

        // return View::make('omimusers.login', compact('omimusers'));
        return View::make('users.login');
    }

    /**
     * Try to Login
     *
     * @return Response
     */
    public function postLogin()
    {
        $input = Input::all();
        $rules = array('username' => 'required', 'password' => 'required');
        $messages = array();
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('user/login')->withErrors($validator);
        } else {
            $credentials = array(
                'username' => $input['username'],
                'password' => $input['password']
            );
            if (Auth::attempt($credentials)) {
                return Redirect::to('admin');
            } else {
                return Redirect::to('user/login')->withInput()->withErrors($validator);
            }
        }
        // $omimusers = Omimuser::all();
        // var_dump($omimusers);
        // die();

        // return View::make('omimusers.login', compact('omimusers'));
        // return View::make('users.login');
    }


    /**
     * Logout the user
     *
     * @return Response
     */
    public function getLogout()
    {
        Auth::logout();
        return Redirect::to('/');

    }


    /**
     * Show Register Form
     *
     * @return Response
     */
    public function getRegister()
    {
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'user/register\' zu verwenden.');
        }
        return View::make('users.register');
    }

    /**
     * Try to Register a new user
     *
     * @return Response
     */
    public function postRegister()
    {
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung die Ressource \'user/register\' zu verwenden.');
        }

        $input = Input::all();
        $rules = array('username' => 'required|unique:omim_users', 'password' => 'required|unique:omim_users');
        $messages = array();
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->passes()) {
            $password = Hash::make($input['password']);
            $user = new User();
            $user->username = $input['username'];
            $user->password = $password;
            $user->forname = $input['forname'];
            $user->surename = $input['surename'];
            $user->isroot = (int) $input['isroot'];
            $user->save();
            return Redirect::to('user/login');
        } else {
            return Redirect::to('user/register')->withInput()->withErrors($validator);
        }
    }


    /**
     * Show the form for creating a new omimuser
     *
     * @return Response
     */
    // public function create()
    // {
    //     return View::make('omimusers.create');
    // }

    /**
     * Store a newly created omimuser in storage.
     *
     * @return Response
     */
    // public function store()
    // {
    //     $validator = Validator::make($data = Input::all(), Omimuser::$rules);

    //     if ($validator->fails())
    //     {
    //         return Redirect::back()->withErrors($validator)->withInput();
    //     }

    //     Omimuser::create($data);

    //     return Redirect::route('omimusers.index');
    // }

    /**
     * Display the specified omimuser.
     *
     * @param  int  $id
     * @return Response
     */
    // public function show($id)
    // {
    //     $omimuser = Omimuser::findOrFail($id);

    //     return View::make('omimusers.show', compact('omimuser'));
    // }

    /**
     * Show the form for editing the specified omimuser.
     *
     * @param  int  $id
     * @return Response
     */
    // public function edit($id)
    // {
    //     $omimuser = Omimuser::find($id);

    //     return View::make('omimusers.edit', compact('omimuser'));
    // }

    /**
     * Update the specified omimuser in storage.
     *
     * @param  int  $id
     * @return Response
     */
    // public function update($id)
    // {
    //     $omimuser = Omimuser::findOrFail($id);

    //     $validator = Validator::make($data = Input::all(), Omimuser::$rules);

    //     if ($validator->fails())
    //     {
    //         return Redirect::back()->withErrors($validator)->withInput();
    //     }

    //     $omimuser->update($data);

    //     return Redirect::route('omimusers.index');
    // }

    /**
     * Remove the specified omimuser from storage.
     *
     * @param  int  $id
     * @return Response
     */
    // public function destroy($id)
    // {
    //     Omimuser::destroy($id);

    //     return Redirect::route('omimusers.index');
    // }

}
