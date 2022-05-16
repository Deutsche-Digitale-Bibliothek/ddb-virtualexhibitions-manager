<?php

class ApiController extends BaseController {

    public function getIndex()
    {
        return json_encode([]);
    }

    /**
     * Get list of all exhibitions.
     *
     * URL example: https://yourdomain.tld/api/exhibits?order=publish-desc
     *
     * @return void
     */
    public function getExhibits()
    {
        switch (Input::get('order')) {
            case 'title-asc':
                $orderBy = array('field' => 'title', 'direction' => 'ASC');
                break;
            case 'title-desc':
                $orderBy = array('field' => 'title', 'direction' => 'DESC');
                break;
            case 'date-asc':
                $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
                break;
            case 'date-desc':
                $orderBy = array('field' => 'created_at', 'direction' => 'DESC');
                break;
            case 'slug-asc':
                $orderBy = array('field' => 'slug', 'direction' => 'ASC');
                break;
            case 'slug-desc':
                $orderBy = array('field' => 'slug', 'direction' => 'DESC');
                break;
            case 'publish-asc':
                $orderBy = array('field' => 'last_published_at', 'direction' => 'ASC');
                break;
            case 'publish-desc':
                $orderBy = array('field' => 'last_published_at', 'direction' => 'DESC');
                break;
            default:
                $orderBy = array('field' => 'created_at', 'direction' => 'ASC');
        }
        $configOmim = Config::get('omim');
        $exhibits = OmimInstance::orderBy($orderBy['field'], $orderBy['direction'])->get()->toArray();
        $filteredExhibits = [];
        foreach ($exhibits as $key => $exhibit) {
            $last_published_at = $exhibit['last_published_at'] ? strtotime($exhibit['last_published_at']) : 0;
            $last_unpublished_at = $exhibit['last_unpublished_at'] ? strtotime($exhibit['last_unpublished_at']) : 0;
            if ($last_published_at > 0 && $last_published_at > $last_unpublished_at) {
                $filteredExhibits[$key]['id'] = $exhibit['id'];
                $filteredExhibits[$key]['title'] = $exhibit['title'];
                $filteredExhibits[$key]['subtitle'] = $exhibit['subtitle'];
                $filteredExhibits[$key]['slug'] = $exhibit['slug'];
                // $filteredExhibits[$key]['public_url'] =
                //     $configOmim['remote'][0]['production']['http']['url']
                //     . '/' . $exhibit['slug'];
                $filteredExhibits[$key]['language'] = $exhibit['language'];
                $filteredExhibits[$key]['created_at'] = $exhibit['created_at'];
                $filteredExhibits[$key]['updated_at'] = $exhibit['updated_at'];
                $filteredExhibits[$key]['last_published_at'] = $exhibit['last_published_at'];
                $filteredExhibits[$key]['exhibit_type'] = $exhibit['exhibit_type'];
                $filteredExhibits[$key]['color_palette'] = $exhibit['color_palette'];
            }
        }

        return Response::json(
            [
			    'exhibits' => $filteredExhibits,
            ],
			200
		);
    }
}
