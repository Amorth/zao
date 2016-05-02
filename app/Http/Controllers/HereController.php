<?php

namespace App\Http\Controllers;

use Curl\Curl;

use App\User;
use View, Request, Response, Config, Validator;

/**
 * 打卡控制器
 *
 * @author popfeng <popfeng@yeah.net> 2016-04-19
 */
class HereController extends Controller
{ 

    /**
     * 首页
     *
     * @return Response
     */
    public function map()
    {
        // render page
        return View::make('here.map')->with('user', User::getCurrent());
    }

    /**
     * 位置记录
     *
     * @return Response
     */
    public function index()
    {
        // render page
        return View::make('here.index');
    }

    /**
     * 新增位置
     *
     * @return Response
     */
    public function create()
    {
        // render page
        return Response::view('here.create');
    }

    /**
     * 保存位置
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ( ! User::isLogin()) {
            return Response::json(['status' => 'Not login']);
        }

        $rules = [
            'date'     => 'required|date',
            'location' => 'required|size:27',
        ];
        $validator = Validator::make(Request::all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 'Invalid params']);
        }

        $date = $request::get('date');
        $placeId = $request::get('location');

        $details = $this->placeDetails($placeId);
        if ('OK' !== $details['status']) {
            return Response::json(['status' => $details['status']]);
        }
        print_r($details['result']);
    }

    /**
     * Google Places API Web Service
     * Place Autocomplete
     *
     * @param Request $request
     * @return Response
     * @see https://developers.google.com/places/web-service/autocomplete
     */
    public function placeAutocomplete(Request $request)
    {
        // TODO
        return file_get_contents('/tmp/geo.json');

        $input = trim($request::get('input'));
        if (empty($input)) {
            return Response::json([
                'status' => 'No results found'
            ]);
        }

        $curl = new Curl();
        $curl->setTimeout(15);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $curl->get(Config::get('googleplaces.api_autocomplete'), [
            'key'   => Config::get('googleplaces.key'),
            'types' => 'geocode',
            'input' => $input
        ]);
        if ($curl->error) {
            return Response::json([
                'status' => $curl->errorMessage
            ]);
        } else {
            return Response::json($curl->response);
        }
    }

    /**
     * Google Places API Web Service
     * Place Details
     *
     * @param string $placeId
     * @return array
     * @see https://developers.google.com/places/web-service/details
     */
    private function placeDetails($placeId)
    {
        $curl = new Curl();
        $curl->setTimeout(15);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $curl->setJsonDecoder(function($string) {
            return json_decode($string, true);
        });
        $curl->get(Config::get('googleplaces.api_details'), [
            'key'      => Config::get('googleplaces.key'),
            'language' => 'zh-CN',
            'placeid'  => $placeId
        ]);
        if ($curl->error) {
            return ['status' => $curl->errorMessage];
        } else {
            return $curl->response;
        }
    }
}
