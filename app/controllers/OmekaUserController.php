<?php

class OmekaUserController extends \BaseController {


    public $msg = [
        'error' => [
            'select-user'           => 'Wählen Sie einen Benutzer aus der Liste.',
            'user-not-found'        => 'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.',
            'no-prevelege'          => 'Sie haben keine Berechtigung die Ressource zu verwenden.',
            'username-not-unique'   => 'Dieser Benutzername existiert bereits. Änerung nicht möglich.'
        ]
    ];

    /**
     * The salt for the hashed password.
     *
     * @var string
     */
    protected $salt;

    /**
     * Display a listing of users
     *
     * @return Response
     */
    public function getIndex()
    {
        return Redirect::to('omeka-user/list');
    }


    /**
     * Show Create Form
     *
     * @return Response
     */
    public function getCreate()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')
                ->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        return View::make('omeka-users.create');
    }

    /**
     * Create a new user
     *
     * @return Response
     */
    public function postCreate()
    {

        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')
                ->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $input = Input::all();
        $rules = array(
            'username'  => 'required|alpha|unique:omim_omeka_users',
            'password'  => 'required|min:6',
            'name'      => 'required',
            'email'     => 'required|email'
        );
        $messages = array();
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->passes()) {
            $this->generateSalt();
            $password = $this->hashPassword($input['password']);
            $user = new OmimOmekaUser();
            $user->username = $input['username'];
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->password = $password;
            $user->salt = $this->salt;
            $user->role = $input['role'];
            $user->save();
            return Redirect::to('omeka-user/list')->with('success-message',
                'Omeka Standard-Benutzer &quot;'
                    . strip_tags($user->username)
                    . '&quot; erfolgreich angelegt.');
        } else {
            return Redirect::to('omeka-user/create')
                ->withInput()->withErrors($validator);
        }
    }

    /**
     * Show User list
     *
     * @return Response
     */
    public function getList()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        $users = OmimOmekaUser::all();
        return View::make('omeka-users.list', compact('users'));
    }

    /**
     * Get Edit User
     *
     * @return Response
     */
    public function getEdit($id = null)
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        if (!isset($id)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'select-user'});
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $user = OmimOmekaUser::find($id);
        if (!isset($user) || empty($user)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        return View::make('omeka-users.edit', compact('user'));
    }

    /**
     * Post Edit User
     *
     * @return Response
     */
    public function postEdit($id = null)
    {
        $params = Input::all();
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        if (!isset($id)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'select-user'});
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $user = OmimOmekaUser::find($id);
        if (!isset($user) || empty($user)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $rules = array('username' => 'required|alpha', 'email' => 'required|email');
        $messages = array();
        $validator = Validator::make($params, $rules, $messages);
        if (!$validator->passes()) {
            return Redirect::to('omeka-user/edit/' . $id)->withInput()->withErrors($validator);
        }
        $modified = false;
        if (isset($params['username']) && !empty($params['username']) && $params['username'] !== $user->username) {
            $registedUser = OmimOmekaUser::where('username', '=', $params['username'])->get();
            if (!$registedUser->isEmpty()) {
                return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'username-not-unique'});
            } else {
                $user->username = $params['username'];
                $modified = true;
            }
        }
        if (isset($params['name']) && !empty($params['name']) && $params['name'] !== $user->name) {
            $user->name = $params['name'];
            $modified = true;
        }
        if (isset($params['email']) && !empty($params['email']) && $params['email'] !== $user->email) {
            $user->email = $params['email'];
            $modified = true;
        }
        if (isset($params['role']) && $params['role'] !== $user->role) {
            $user->role = $params['role'];
            $modified = true;
        }
        if ($modified === true) {
            $user->save();
        }
        return Redirect::to('omeka-user/list')->with('success-message',
                'Benutzer &quot;' . strip_tags($user->username) . '&quot; erfolgreich editiert.');
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
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        if (!isset($id)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'select-user'});
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $user = OmimOmekaUser::find($id);
        if (!isset($user) || empty($user)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        return View::make('omeka-users.chpwd', compact('user'));
    }

    /**
     * Post Change User Password
     *
     * @return Response
     */
    public function postChpwd($id = null)
    {
        $params = Input::all();
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        if (!isset($id)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'select-user'});
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $user = OmimOmekaUser::find($id);
        if (!isset($user) || empty($user)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $rules = array('password' => 'required|min:6');
        $messages = array();
        $validator = Validator::make($params, $rules, $messages);
        if ($validator->passes()) {
            if (!empty($user->salt)) {
                $this->salt = $user->salt;
            } else {
                $this->generateSalt();
            }
            $password = $this->hashPassword($params['password']);
            $user->password = $password;
            $user->save();
            return Redirect::to('omeka-user/list')->with('success-message',
                'Passwort für Benutzer &quot;'
                . strip_tags($user->username)
                . '&quot; erfolgreich gesetzt.');
        } else {
            return Redirect::to('omeka-user/chpwd/' . $id)->withInput()->withErrors($validator);
        }
    }

    /**
     * Generate a simple 16 character salt for the user.
     */
    public function generateSalt()
    {
        $this->salt = substr(md5(mt_rand()), 0, 16);
    }

    /**
     * SHA-1 hash the given password with the current salt.
     *
     * @param string $password Plain-text password.
     * @return string Salted and hashed password.
     */
    public function hashPassword($password)
    {
        return sha1($this->salt . $password);
    }

    /**
     * Get Delete User
     *
     * @return Response
     */
    public function getDelete($id = null)
    {
        $confirm = Input::get('confirm');
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        if (!isset($id)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'select-user'});
        }
        $id = (int) $id;
        if (!isset($id) || $id == 0) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        $user = OmimOmekaUser::find($id);
        if (!isset($user) || empty($user)) {
            return Redirect::to('omeka-user/list')->with('error-message', $this->msg{'error'}{'user-not-found'});
        }
        if ($confirm == 'ok') {
            $username = $user->username;
            $user->delete();
            return Redirect::to('omeka-user/list')->with('success-message',
                'Benutzer &quot;'
                . strip_tags($username)
                . '&quot; erfolgreich gelöscht.');
        }
        return View::make('omeka-users.delete', compact('user'));
    }

}