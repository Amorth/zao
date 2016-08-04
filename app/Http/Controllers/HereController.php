<?php

namespace App\Http\Controllers;

use Curl\Curl;

use App\{User, Here};
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
        return View::make('here.map')->with('user', User::getInfo());
    }

    /**
     * 位置记录
     *
     * @return Response
     */
    public function index()
    {
        // get here list
        $list = User::isLogin() ?
            Here::usered(User::getInfo()->id)->orderBy('date')->get() : [];

        // render page
        return View::make('here.index')->with('list', $list);
    }

    /**
     * 新增位置
     *
     * @return Response
     */
    public function create()
    {
        // render page
        return View::make('here.edit');
    }

    /**
     * 编辑位置
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        // get current data
        $data = Here::find($id);

        // render page
        return View::make('here.edit')->with('data', $data);
    }

    /**
     * 保存位置
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // get params & validate
        if ( ! User::isLogin()) {
            return Response::json(['status' => 'Not login']);
        }

        $params = $request::all();
        $validator = Validator::make($params, [
            'date'     => 'required|in:2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014,2015,2016',
            'location' => 'required|min:27|max:39'
        ]);
        if ($validator->fails()) {
            return Response::json(['status' => 'Invalid params']);
        }

        // request place details
        $result = $this->placeDetails($params['location']);
        if ('OK' !== $result['status']) {
            return Response::json(['status' => $result['status']]);
        }

        // save
        $details = $result['result'];
        $data = [
            'user_id'     => User::getInfo()['id'],
            'date'        => $params['date'],
            'lat'         => $details['geometry']['location']['lat'],
            'lng'         => $details['geometry']['location']['lng'],
            'country'     => $details['address_components']['country']['long_name'] ?? '',
            'province'    => $details['address_components']['administrative_area_level_1']['long_name'] ?? '',
            'location'    => $details['name'],
            'gm_url'      => $details['url'],
            'gm_place_id' => $details['place_id']
        ];
        if ( ! empty($params['id'])) {
            Here::where('id', $params['id'])->update($data);
        } else {
            Here::create($data);
        }

        // response
        return Response::json(['status' => 'OK']);
    }

    /**
     * 删除位置
     *
     * @param int $id 
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        $status = Here::find($id)->delete();

        // response
        return Response::json(['status' => $status ? 'OK' : 'ERROR']);
    }

    /**
     * 地图数据接口
     *
     * @param Request $request
     * @return Response
     */
    public function mapData(Request $request)
    {
        $mode = Request::get('mode', 'world');
        $method = sprintf('get%sMapData', ucfirst($mode));

        return Response::json($this->$method());
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
     * 「世界」模式地图数据
     *
     * @return array
     */
    private function getWorldMapData()
    {
        // get here list
        $list = Here::select('lng', 'lat', 'date', 'gm_place_id')
            ->orderBy('date')
            ->get()
            ->toArray();

        // grouping
        $listOne = [];
        foreach ($list as $item) {
            $listOne[$item['date']][] = $item;
        }

        $listTwo = $listCache = [];
        foreach ($listOne as $year => $dataItem) {
            $listTwo[$year] = array_merge($listCache, $dataItem);
            $listCache = array_merge($listCache, $dataItem);
        }

        $listThree = $gmpidCounts = [];
        foreach ($listTwo as $year => $dataItem) {
            foreach ($dataItem as $item) {
                if (isset($gmpidCounts[$year][$item['gm_place_id']])) {
                    $gmpidCounts[$year][$item['gm_place_id']]++;
                } else {
                    $gmpidCounts[$year][$item['gm_place_id']] = 1;
                }
            }
            foreach ($dataItem as $item) {
                $listThree[$year][$item['gm_place_id']][] = $item;
            }
        }

        $list = [];
        $yearIndex = 0;
        foreach ($gmpidCounts as $year => $dataItem) {
            $list[$yearIndex][0] = $year;
            foreach ($dataItem as $gmpid => $count) {
                $item = $listThree[$year][$gmpid][0];
                $list[$yearIndex][1][] = [$item['lng'], $item['lat'], $count];
            }
            $yearIndex++;
        }

        return $list;
    }

    /**
     * 「自己」模式地图数据
     *
     * @return array
     */
    private function getPersonalMapData()
    {
        // check login status
        if ( ! User::isLogin()) {
            return ['status' => 'Not login'];
        }

        // get here list
        $list = Here::usered(User::getInfo()->id)
            ->orderBy('date')
            ->get()
            ->toArray();

        $coord = $address = $date = $data = [];
        for ($i = 0, $n = count($list); $i < $n; $i++) {
            $coord[$list[$i]['location']] = [
                $list[$i]['lng'],
                $list[$i]['lat']
            ];

            $address[$list[$i]['location']] = sprintf(
                '%s %s',
                $list[$i]['province'],
                $list[$i]['location']
            );

            $date[$list[$i]['location']] = $list[$i]['date'];

            $data[$i] = [
                $list[$i]['location'],
                [[
                    ['name' => $list[$i - 1]['location'] ?? []],
                    ['name' => $list[$i]['location']]
                ]]
            ];
        }

        // return data
        return compact('coord', 'address', 'date', 'data');
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
            $response = $curl->response;
            foreach ($response['result']['address_components'] as $i => $item) {
                $response['result']['address_components'][$item['types'][0]] = $item;
                unset($response['result']['address_components'][$i]);
            }
            return $response;
        }
    }
}
