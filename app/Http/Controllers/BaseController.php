<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helper\ApiResponse;
use Illuminate\Support\Facades\Request as RequestSupport;

class BaseController extends Controller
{
    protected function processInput() {
        $input = RequestSupport::all();

        $result = array();

        $fields = array();
        if (array_key_exists('fields', $input)) {
            $fields = explode(',', $input['fields']);
            unset($input['fields']);
        }

        $sort = array();
        if (array_key_exists('sort', $input)) {
            foreach (explode(',', $input['sort']) as $sortValue) {
                if (substr($sortValue, 0, 1) == '-') {
                    $sort[substr($sortValue, 1)] = 'Desc';
                } else {
                    $sort[$sortValue] = 'Asc';
                }
            }

            unset($input['sort']);
        }

        $limit = 10;
        if (array_key_exists('limit', $input)) {
            $limit = $input['limit'];
            unset($input['limit']);
        }

        $offset = 0;
        if (array_key_exists('offset', $input)) {
            $offset = $input['offset'];
            unset($input['offset']);
        }

        $where = $input;

        $result['fields'] = $fields;
        $result['sort'] = $sort;
        $result['limit'] = $limit;
        $result['offset'] = $offset;
        $result['where'] = $where;

        return $result;
    }
    
    public function missingMethod( $parameters = array() )
	{
        return ApiResponse::errorNotFound('Sorry, no method found');
	}
}
