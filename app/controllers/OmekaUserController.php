<?php

class OmekaUserController extends \BaseController {


    public $msg = [
        'error' => [
            'select-user'           => 'Wählen Sie einen Benutzer aus der Liste.',
            'user-not-found'        => 'Der angegebene Benutzer konnte nicht gefunden werden. Wählen Sie einen Benutzer aus der Liste.',
            'no-prevelege'          => 'Sie haben keine Berechtigung, die Ressource zu verwenden.',
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
            'username'  => 'required|alpha|unique:omim_omeka_users|max:30',
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
        $rules = array(
            'username'  => 'required|regex:/^[A-Za-z0-9\-@\._]+$/|max:30',
            'name'      => 'required',
            'email'     => 'required|email'
        );
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

    /**
     * Get users in OmekaInstances
     *
     * @return Response
     */
    public function getOmekaInstances()
    {
        $instances = OmimInstance::all();
        // var_dump($instances);
        $users = [];
        foreach ($instances as $key => $instance) {
            $users[$instance->id] = DB::select('select * from omeka_exh' . $instance->id . '_users');
        }
        return View::make('omeka-users.instances', compact('instances', 'users'));
    }

    /**
     * Post users in OmekaInstances
     *
     * @return Response
     */
    public function postOmekaInstances()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('omeka-user/omeka-instances')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        $instances = OmimInstance::all();
        $users = [];
        foreach ($instances as $key => $instance) {
            $users[$instance->id] = DB::select('select * from omeka_exh' . $instance->id . '_users');
        }
        $input = Input::all();
        $filter = [
            'role' => ['super', 'admin', 'contributor', 'researcher'],
            'active' => [0,1]
        ];
        $modified = false;
        if (isset($input['exh']) && !empty($input['exh']) && is_array($input['exh'])) {
            foreach ($input['exh'] as $inputExhibitId => $inputUsers) {
                if (isset($users[$inputExhibitId]) && !empty($users[$inputExhibitId])) {
                    foreach ($users[$inputExhibitId] as $user) {
                        if (isset($inputUsers[$user->id]) && !empty($inputUsers[$user->id])) {
                            if (isset($inputUsers[$user->id]['role'])
                                && $inputUsers[$user->id]['role'] != $user->role
                                && in_array($inputUsers[$user->id]['role'], $filter['role']))
                            {
                                $role = DB::connection()->getPdo()->quote($inputUsers[$user->id]['role']);
                                DB::update('update omeka_exh' . $inputExhibitId . '_users set role = ' . $role . ' where id = ?', array($user->id));
                                $modified = true;
                            }
                            if (isset($inputUsers[$user->id]['active'])
                                && $inputUsers[$user->id]['active'] != $user->active
                                && in_array((int) $inputUsers[$user->id]['active'], $filter['active']))
                            {
                                $active = (int) $inputUsers[$user->id]['active'];
                                DB::update('update omeka_exh' . $inputExhibitId . '_users set active = ' . $active . ' where id = ?', array($user->id));
                                $modified = true;
                            }
                        }
                    }
                }
            }
        }
        if ($modified === true) {
            return Redirect::to('omeka-user/omeka-instances')->with('success-message',
                'Benutzereinstellungen erfolgreich gespeichert.');
        } else {
            return Redirect::to('omeka-user/omeka-instances')->with('success-message',
                'Keine Änderungen vorgenommen.');

        }
    }

    /**
     * Get users in all OmekaInstances
     *
     * @return Response
     */
    public function getEditInstanceUsers()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }
        $instances = OmimInstance::all();
        $omimOmekaUsers = OmimOmekaUser::all();
        $users = [];
        foreach ($instances as $key => $instance) {
            // $users[$instance->id] = DB::select('select username, name, email from omeka_exh' . $instance->id . '_users');
            $currentUsers = DB::select('select username, name, email from omeka_exh' . $instance->id . '_users');
            if (is_array($currentUsers)) {
                $users = array_merge_recursive($users, $currentUsers);
            }
        }
        // var_dump($users);
        $unipueUsers = array_map("unserialize", array_unique(array_map("serialize", $users)));
        usort($unipueUsers, function($a, $b) {
            return strcmp(strtolower($a->username), strtolower($b->username));
        });
        // var_dump($omimOmekaUsers);

        return View::make('omeka-users.edit-instance-users', compact('omimOmekaUsers', 'unipueUsers'));
    }

    /**
     * Reset user in all OmekaInstances
     *
     * @return Response
     */
    public function getResetInstancesUser()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')
                ->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $params = Input::all();
        if (!isset($params['username']) || empty($params['username'])
            || !isset($params['name']) || empty($params['name'])
            || !isset($params['email']) || empty($params['email']))
        {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }

        // \DB::listen(function($sql, $bindings, $time) {
        //     var_dump($sql);
        //     var_dump($bindings);
        //     var_dump($time);
        // });

        $omimOmekaUser = OmimOmekaUser::
            where('username', '=', $params['username'])
            ->where('name', '=', $params['name'])
            ->where('email', '=', $params['email'])
            ->get();

        if ($omimOmekaUser->isEmpty()) {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }
        // var_dump($omimOmekaUser{0}->name);

        $instances = OmimInstance::all();
        foreach ($instances as $key => $instance) {
            $user = DB::table('omeka_exh' . $instance->id . '_users')
                ->where('username', $params['username'])
                ->where('name', $params['name'])
                ->where('email', $params['email'])
                ->update(array(
                    'password' => $omimOmekaUser{0}->password,
                    'salt' => $omimOmekaUser{0}->salt,
                    'role' => $omimOmekaUser{0}->role
                ));

            // $user = DB::table('omeka_exh' . $instance->id . '_users')
            //     ->where('username', $params['username'])
            //     ->where('name', $params['name'])
            //     ->where('email', $params['email'])
            //     ->get();
            // var_dump($user);
        }

        return Redirect::to('omeka-user/edit-instance-users')
                ->with('success-message', 'Benutzer erfolgreich zurückgesetzt.');

        // dd($omimOmekaUser);
    }

    /**
     * Get edit user in all OmekaInstances
     *
     * @return Response
     */
    public function getEditInstancesUser()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')
                ->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $params = Input::all();
        if (!isset($params['username']) || empty($params['username'])
            || !isset($params['name']) || empty($params['name'])
            || !isset($params['email']) || empty($params['email']))
        {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }

        return View::make('omeka-users.edit-instances-user', compact('params'));
    }

    /**
     * Post edit user in all OmekaInstances
     *
     * @return Response
     */
    public function postEditInstancesUser()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')
                ->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $params = Input::all();


        if (!isset($params['oldusername']) || empty($params['oldusername'])
            || !isset($params['oldname']) || empty($params['oldname'])
            || !isset($params['oldemail']) || empty($params['oldemail']))
        {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }

        $rules = array(
            'username' => 'required|regex:/^[A-Za-z0-9\-@\._]+$/|max:30',
            'name'    => 'required',
            'email'   => 'required|email'
        );
        $messages = array();
        $validator = Validator::make($params, $rules, $messages);
        if (!$validator->passes()) {
            return Redirect::to('omeka-user/edit-instances-user'
                . '?name='. urlencode($params['oldname'])
                . '&username=' . urlencode($params['oldusername'])
                . '&email=' . urlencode($params['oldemail']))
                ->withInput()->withErrors($validator);
        }
        // var_dump($params);

        $instances = OmimInstance::all();
        $update = [
            'username'  => $params['username'],
            'name'      => $params['name'],
            'email'     => $params['email']
        ];
        $filter = [
            'role' => ['super', 'admin', 'contributor', 'researcher'],
            'active' => [0,1]
        ];
        if (isset($params['role']) && in_array($params['role'], $filter['role']))
        {
            $update['role'] = $params['role'];
        }
        if (isset($params['active']) && in_array((int) $params['active'], $filter['active']))
        {
            $update['active'] = (int) $params['active'];
        }
        foreach ($instances as $key => $instance) {
            $user = DB::table('omeka_exh' . $instance->id . '_users')
                ->where('username', $params['oldusername'])
                ->where('name', $params['oldname'])
                ->where('email', $params['oldemail'])
                ->update($update);
        }

        return Redirect::to('omeka-user/edit-instance-users')
                ->with('success-message', 'Benutzer erfolgreich bearbeitet.');

    }

    /**
     * Get Change User Password
     *
     * @return Response
     */
    public function getChpwdInstancesUser()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $params = Input::all();
        if (!isset($params['username']) || empty($params['username'])
            || !isset($params['name']) || empty($params['name'])
            || !isset($params['email']) || empty($params['email']))
        {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }

        return View::make('omeka-users.chpwd-instances-user', compact('params'));
    }

    /**
     * Post Change User Password
     *
     * @return Response
     */
    public function postChpwdInstancesUser()
    {
        if (Auth::user()->isroot != 1) {
            return Redirect::to('admin')->with('error-message', $this->msg{'error'}{'no-prevelege'});
        }

        $params = Input::all();

        if (!isset($params['username']) || empty($params['username'])
            || !isset($params['name']) || empty($params['name'])
            || !isset($params['email']) || empty($params['email']))
        {
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('error-message', $this->msg{'error'}{'select-user'});
        }

        $rules = array('password' => 'required|min:6');
        $messages = array();
        $validator = Validator::make($params, $rules, $messages);
        if ($validator->passes()) {
            $this->generateSalt();
            $password = $this->hashPassword($params['password']);
            $update = [
                'salt'      => $this->salt,
                'password'  => $password
            ];
            $instances = OmimInstance::all();
            foreach ($instances as $key => $instance) {
                $user = DB::table('omeka_exh' . $instance->id . '_users')
                    ->where('username', $params['username'])
                    ->where('name', $params['name'])
                    ->where('email', $params['email'])
                    ->update($update);
            }
            return Redirect::to('omeka-user/edit-instance-users')
                ->with('success-message', 'Passwort für Benutzer &quot;'
                    . $params['username'] . '&quot; erfolgreich geändert.');

        } else {
            return Redirect::to('omeka-user/chpwd-instances-user'
                . '?name='. urlencode($params['name'])
                . '&username=' . urlencode($params['username'])
                . '&email=' . urlencode($params['email']))
                ->withInput()->withErrors($validator);
        }

    }

}