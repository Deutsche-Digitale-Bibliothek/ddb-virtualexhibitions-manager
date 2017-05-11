<?php

class HomeController extends BaseController {

	/**
     *
     *
     * @return Response
     */
    public function getIndex()
    {

        return Redirect::to('admin');


        // $sort = Input::get('sort-list');
        // $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
        // if (isset($sort) && !empty($sort)) {
        //     switch ($sort) {
        //         case 'title-asc':
        //             $orderBy = array('field' => 'title', 'direction' => 'ASC');
        //             break;
        //         case 'title-desc':
        //             $orderBy = array('field' => 'title', 'direction' => 'DESC');
        //             break;
        //         case 'date-asc':
        //             $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
        //             break;
        //         case 'date-desc':
        //             $orderBy = array('field' => 'created_at', 'direction' => 'DESC');
        //             break;
        //         case 'slug-asc':
        //             $orderBy = array('field' => 'slug', 'direction' => 'ASC');
        //             break;
        //         case 'slug-desc':
        //             $orderBy = array('field' => 'slug', 'direction' => 'DESC');
        //             break;
        //     }
        // }

        // $omiminstance = OmimInstance::orderBy($orderBy['field'], $orderBy['direction'])->get();

        // return View::make('home', compact('omiminstance'));

    }
}
