<?php

class OmimUsersController extends \BaseController {

	/**
	 * Display a listing of omimusers
	 *
	 * @return Response
	 */
	public function index()
	{
		$omimusers = Omimuser::all();

		return View::make('omimusers.index', compact('omimusers'));
	}

	/**
	 * Try to Login as a user
	 *
	 * @return Response
	 */
	public function login()
	{
		// $omimusers = Omimuser::all();
		// var_dump($omimusers);
		// die();

		// return View::make('omimusers.login', compact('omimusers'));
		return View::make('omimusers.login');
	}

	/**
	 * Show the form for creating a new omimuser
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('omimusers.create');
	}

	/**
	 * Store a newly created omimuser in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Omimuser::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Omimuser::create($data);

		return Redirect::route('omimusers.index');
	}

	/**
	 * Display the specified omimuser.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$omimuser = Omimuser::findOrFail($id);

		return View::make('omimusers.show', compact('omimuser'));
	}

	/**
	 * Show the form for editing the specified omimuser.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$omimuser = Omimuser::find($id);

		return View::make('omimusers.edit', compact('omimuser'));
	}

	/**
	 * Update the specified omimuser in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$omimuser = Omimuser::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Omimuser::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$omimuser->update($data);

		return Redirect::route('omimusers.index');
	}

	/**
	 * Remove the specified omimuser from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Omimuser::destroy($id);

		return Redirect::route('omimusers.index');
	}

}
