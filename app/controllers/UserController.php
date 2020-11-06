<?php

class UserController extends \BaseController {

    /**
     * Display a listing of omimusers
     *
     * @return Response
     */
    public function getIndex()
    {
        return Redirect::to('user/list');
    }

    /**
     * Show Login Form
     *
     * @return Response
     */
    public function getLogin()
    {
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
                'Sie haben keine Berechtigung, die Ressource \'user/register\' zu verwenden.');
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
                'Sie haben keine Berechtigung, die Ressource \'user/register\' zu verwenden.');
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
            return Redirect::to('user/list')->with('success-message',
                'Benutzer &quot;' . strip_tags($user->username) . '&quot; erfolgreich angelegt.');
        } else {
            return Redirect::to('user/register')->withInput()->withErrors($validator);
        }
    }

    /**
     * Show User list
     *
     * @return Response
     */
    public function getList()
    {
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        $omimusers = OmimUser::all();
        // var_dump($omimusers);
        return View::make('users.list', compact('omimusers'));
    }

    /**
     * Get Edit User
     *
     * @return Response
     */
    public function getEdit($id = null)
    {
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        if (!isset($id)) {
            return Redirect::to('user/list')->with('error-message',
                'Wählen Sie einen Benutzer aus der Liste.');
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimuser = OmimUser::find($id);
        if (!isset($omimuser) || empty($omimuser)) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        return View::make('users.edit', compact('omimuser'));
    }

    /**
     * Post Edit User
     *
     * @return Response
     */
    public function postEdit($id = null)
    {
        // var_dump(Input::all());
        $params = Input::all();
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        if (!isset($id)) {
            return Redirect::to('user/list')->with('error-message',
                'Wählen Sie einen Benutzer aus der Liste.');
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimuser = OmimUser::find($id);
        if (!isset($omimuser) || empty($omimuser)) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $modified = false;
        if (isset($params['username']) && !empty($params['username']) && $params['username'] !== $omimuser->username) {
            $registedUser = User::where('username', '=', $params['username'])->get();
            if (!$registedUser->isEmpty()) {
                return Redirect::to('user/list')->with('error-message',
                'Dieser Benutzername existiert bereits. Änerung nicht möglich.');
            } else {
                $omimuser->username = $params['username'];
                $modified = true;
            }
        }
        if (isset($params['forname']) && !empty($params['forname']) && $params['forname'] !== $omimuser->forname) {
            $omimuser->forname = $params['forname'];
            $modified = true;
        }
        if (isset($params['surename']) && !empty($params['surename']) && $params['surename'] !== $omimuser->surename) {
            $omimuser->surename = $params['surename'];
            $modified = true;
        }
        if (isset($params['isroot']) && $params['isroot'] !== $omimuser->isroot) {
            $omimuser->isroot = $params['isroot'];
            $modified = true;
        }
        if ($modified === true) {
            $omimuser->save();
        }
        return Redirect::to('user/list')->with('success-message',
                'Benutzer &quot;' . strip_tags($omimuser->username) . '&quot; erfolgreich editiert.');
        return;
    }


    /**
     * Get Change User Password
     *
     * @return Response
     */
    public function getChpwd($id = null)
    {
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        if (!isset($id)) {
            return Redirect::to('user/list')->with('error-message',
                'Wählen Sie einen Benutzer aus der Liste.');
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimuser = OmimUser::find($id);
        if (!isset($omimuser) || empty($omimuser)) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        return View::make('users.chpwd', compact('omimuser'));
    }

    /**
     * Post Change User Password
     *
     * @return Response
     */
    public function postChpwd($id = null)
    {
        $params = Input::all();
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        if (!isset($id)) {
            return Redirect::to('user/list')->with('error-message',
                'Wählen Sie einen Benutzer aus der Liste.');
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimuser = OmimUser::find($id);
        if (!isset($omimuser) || empty($omimuser)) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $rules = array('password' => 'required');
        $messages = array();
        $validator = Validator::make($params, $rules, $messages);
        if ($validator->passes()) {
            $password = Hash::make($params['password']);
            $omimuser->password = $password;
            $omimuser->save();
            return Redirect::to('user/list')->with('success-message',
                'Passwort für Benutzer &quot;'
                . strip_tags($omimuser->username)
                . '&quot; erfolgreich gesetzt.');
        } else {
            return Redirect::to('user/chpwd/' . $id)->withInput()->withErrors($validator);
        }
    }

    /**
     * Get Delete User
     *
     * @return Response
     */
    public function getDelete($id = null)
    {
        $confirm = Input::get('confirm');
        if (!Auth::check()) {
            return Redirect::to('user/login');
        }
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message',
                'Sie haben keine Berechtigung, die Ressource \'user/list\' zu verwenden.');
        }
        if (!isset($id)) {
            return Redirect::to('user/list')->with('error-message',
                'Wählen Sie einen Benutzer aus der Liste.');
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimuser = OmimUser::find($id);
        if (!isset($omimuser) || empty($omimuser)) {
            return Redirect::to('user/list')->with('error-message',
                'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.');
        }
        $omimusers = OmimUser::all();
        if (count($omimusers) == 1) {
            return Redirect::to('user/list')->with('error-message',
                'Sie könnnen nicht alle Benutzer löschen. Stellen Sie sicher, dass es mehr als einen Benutzer gibt.');
        }
        $rootUserLeft = false;
        foreach ($omimusers as $ou) {
            if ((int) $ou->id !== $id && $ou->isroot) {
                $rootUserLeft = true;
            }
        }
        if ($rootUserLeft !== true) {
            return Redirect::to('user/list')->with('error-message',
                'Sie müssen sicherstellen, dasss es mindestens einen Benutzer mit Rootrechten gibt.');
        }
        if ($confirm == 'ok') {
            $username = $omimuser->username;
            $omimuser->delete();
            return Redirect::to('user/list')->with('success-message',
                'Benutzer &quot;'
                . strip_tags($username)
                . '&quot; erfolgreich gelöscht.');
        }

        return View::make('users.delete', compact('omimuser'));
    }

}