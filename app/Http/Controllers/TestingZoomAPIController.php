<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItdpCompanyUser;
use App\Models\ZoomRoom;
use App\Models\ZoomToken;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use stdClass;

class TestingZoomAPIController extends Controller
{

    public function index(Request $request)
    {

        if (env('ZOOM_OWN_URL') == null) {
            return 'Can not read env file';
        }
        $data['ZOOM_CLIENT_ID'] = env('ZOOM_CLIENT_ID');
        $data['ZOOM_REDIRECT_URI'] = env('ZOOM_REDIRECT_URI');
        $data['ZOOM_OWN_URL'] = env('ZOOM_OWN_URL');

        if ($this->get_meetings() != 'Token Expired') {
            $data['origin_data'] = json_decode($this->get_meetings(), true);

            if ($data['origin_data'] != null) {
                $merge = [];
                // Merge data dari zoom dengan password yang tersimpan di local
                foreach ($data['origin_data']['meetings'] as $key => $d) {
                    $local_meeting_data = ZoomRoom::where('meeting_id', $d['id'])->first();

                    $array_user_invited = [];
                    foreach ($local_meeting_data->itdp_company_user as $user_company) {
                        $array_user_invited[] = $user_company->email;
                    };

                    $merge['meetings'][] = array_merge($data['origin_data']['meetings'][$key], [
                        'password' => ($local_meeting_data != '') ? $local_meeting_data->password : '',
                        'quota' => ($local_meeting_data != '') ? $local_meeting_data->quota : 0,
                        'is_passed' => (Carbon::parse($d['start_time']) > Carbon::now()) ? false : true,
                        'invited' => $array_user_invited
                    ]);
                }
                unset($data['origin_data']['meetings']);
                $data['data'] = array_merge($data['origin_data'], $merge);

                // dd($data['data']['meetings']);

                // Jika setelah di merge datanya masih kosong maka buat array meeting kosong
                if (!isset($data['data']['meetings'])) {
                    $data['data']['meetings'] = [];
                } else {
                    // Dorting data yg lama di bawah
                    $start_time = array_column($data['data']['meetings'], 'start_time');
                    array_multisort($start_time, SORT_DESC, $data['data']['meetings']);
                }
            } else {
                // Jika memang belum ada data meeting
                $data['data']['meetings'] = [];
            }

            $data['is_login'] = (ZoomToken::first()  != null) ? true : false;
        } else {
            $data['data']['meetings'] = [];
            $data['is_login'] = false;
        }

        return view('index', $data);
    }
    // Get access token
    public function get_access_token()
    {
        $access_token = ZoomToken::all();
        return json_decode($access_token);
    }

    // Get referesh token
    public function get_refresh_token()
    {
        $result = $this->get_access_token();
        $refreshToken = $result[0]->refresh_token;
        return $refreshToken;
    }

    // Update access token
    public function update_access_token($token)
    {
        $check = ZoomToken::first();
        if ($check != null) {
            $id = ZoomToken::first()->id;
            ZoomToken::whereId($id)->update(
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'],
                    'token_type' => $token['token_type'],
                    'expires_in' => $token['expires_in'],
                    'scope' => $token['scope'],
                ]
            );
        } else {
            ZoomToken::create(
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'],
                    'token_type' => $token['token_type'],
                    'expires_in' => $token['expires_in'],
                    'scope' => $token['scope'],
                ]
            );
        }
    }

    public function callback(Request $request)
    {
        try {
            if (env('ZOOM_OWN_URL') == null) {
                return 'Can not read env file';
            }

            $client = new Client(['base_uri' => env('ZOOM_URI')]);

            $response = $client->request('POST', 'oauth/token', [
                "headers" => [
                    "Authorization" => "Basic " . base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET'))
                ],
                'form_params' => [
                    "grant_type" => "authorization_code",
                    "code" => $_GET['code'],
                    "redirect_uri" => env('ZOOM_REDIRECT_URI')
                ],
            ]);

            $token = json_decode($response->getBody()->getContents(), true);

            $this->update_access_token($token);

            session()->put('status', 'Login');
            return redirect('/');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function get_meetings()
    {
        if (env('ZOOM_OWN_URL') == null) {
            return 'Can not read env file';
        }
        $client = new Client(['base_uri' => env('ZOOM_API_URI')]);

        $arr_token = $this->get_access_token();
        if (count($arr_token) == 0) {
            $data = "";
            return $data;
        } else {
            $accessToken = $arr_token[0]->access_token;

            try {
                $response = $client->request('GET', 'users/me/meetings', [
                    "headers" => [
                        "Authorization" => "Bearer $accessToken"
                    ]
                ]);

                $data = json_encode(json_decode($response->getBody()), true);
                return $data;
            } catch (Exception $e) {
                if (401 == $e->getCode()) {

                    return "Token Expired";

                    // $refresh_token = $this->get_refresh_token();

                    // $client = new Client(['base_uri' => env('ZOOM_URI')]);

                    // $response = $client->request('POST', 'oauth/token', [
                    //     "headers" => [
                    //         "Authorization" => "Basic " . base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET'))
                    //     ],
                    //     'form_params' => [
                    //         "grant_type" => "refresh_token",
                    //         "refresh_token" => $refresh_token
                    //     ],
                    // ]);
                    // $this->update_access_token($response->getBody());

                    // $this->create_meeting(30);
                } else {
                    echo $e->getMessage();
                }
            }
        }
    }

    public function create_meeting(Request $request)
    {
        Session::flush('createMeeting');

        if (env('ZOOM_OWN_URL') == null) {
            return 'Can not read env file';
        }

        $client = new Client(['base_uri' => env('ZOOM_API_URI')]);

        $arr_token = $this->get_access_token();
        $accessToken = $arr_token[0]->access_token;

        try {
            //
            // The type of meeting:
            // * `1` — An instant meeting.
            // * `2` — A scheduled meeting.
            // * `3` — A recurring meeting with no fixed time.
            // * `8` — A recurring meeting with fixed time. (This can only be one of 1,2,3,8)

            // Pre Schedule Meeting
            // Whether to create a prescheduled meeting. This field only supports schedule meetings (`2`):
            // * `true` — Create a prescheduled meeting.
            // * `false` — Create a regular meeting.

            // json
            // * `topic` => The meeting's topic.
            // * `type` => The type of meeting (see above)
            $datetime = explode(" ", $request->start_time);
            $date =  date('Y-m-d', strtotime($datetime[0]));
            $time =  $datetime[1] . ":00";

            // if you have userid of user than change it with me in url
            $response = $client->request('POST', 'users/me/meetings', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ],
                'json' => [
                    "topic" => $request->topic,
                    "type" => 2,
                    "start_time" => $date . "T" . $time,    // meeting start time
                    "duration" => $request->duration,                       // 30 minutes                       // 30 minutes
                    "password" => $request->password,
                    "timezone" => "Asia/Jakarta"                    // meeting password
                ],
            ]);

            $data = json_decode($response->getBody());

            ZoomRoom::create(
                [
                    'meeting_id' => $data->id,
                    'host_id' => $data->host_id,
                    'host_email' => $data->host_email,
                    'topic' => $data->topic,
                    'status' => $data->status,
                    'start_time' => $data->start_time,
                    'duration' => $data->duration,
                    'quota' => $request->quota,
                    'timezone' => $data->timezone,
                    'start_url' => $data->start_url,
                    'join_url' => $data->join_url,
                    'password' => $data->password,
                    'h323_password' => $data->h323_password,
                    'pstn_password' => $data->pstn_password,
                    'encrypted_password' => $data->encrypted_password,
                    'pre_schedule' => $data->pre_schedule,
                ]
            );

            session()->put('createMeeting', true);
            return redirect('/');
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                $refresh_token = $this->get_refresh_token();

                $client = new Client(['base_uri' => env('ZOOM_URI')]);
                $response = $client->request('POST', 'oauth/token', [
                    "headers" => [
                        "Authorization" => "Basic " . base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET'))
                    ],
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token
                    ],
                ]);
                $this->update_access_token($response->getBody());

                // $this->create_meeting($duration);
            } else {
                echo $e->getMessage();
            }
        }
    }



    public function delete_meeting($meetingId)
    {
        if (env('ZOOM_OWN_URL') == null) {
            return 'Can not read env file';
        }

        $client = new Client(['base_uri' => env('ZOOM_API_URI')]);

        $arr_token = $this->get_access_token();
        $accessToken = $arr_token[0]->access_token;
        $meetingId = $meetingId;

        try {
            $response = $client->request('DELETE', "meetings/{$meetingId}", [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);

            ZoomRoom::where('meeting_id', $meetingId)->delete();
            return redirect('/');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function dataAjaxUser(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            $data = ItdpCompanyUser::select('id', 'email')->where('email', 'LIKE', "%$search%")->get();
        }
        return response()->json($data);
    }

    public function add_invitation(Request $request)
    {
        if ($request->has('user_id') && $request->has('zoom_room_id')) {
            $dataItdpCompany = ItdpCompanyUser::whereIn('id', $request->user_id)->get();
            $dataZoomRoom = ZoomRoom::where('meeting_id', $request->zoom_room_id)->first();

            // Cek quota
            $quota_remaining = (int)$dataZoomRoom->quota - (int)$dataZoomRoom->itdp_company_user()->count() - count($request->user_id);

            if ($quota_remaining < 1) {
                return response()->json([
                    'status' => 201,
                    'message' => 'quota exceeded'
                ]);
            }

            $zoom_room_id = $dataZoomRoom->id;

            // $dataZoomRoom->itdp_company_user()->detach();
            foreach ($dataItdpCompany as $d) {
                $d->zoom_rooms()->attach($zoom_room_id);
            }

            return response()->json([
                'status' => 200,
                'message' => 'ok'
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'not ok'
            ]);
        }
    }

    public function view_invitation(Request $request)
    {
        $dataZoomRoom = ZoomRoom::with('itdp_company_user')->where('meeting_id', $request->zoom_room_id)->first();
        $data = [];
        foreach ($dataZoomRoom->itdp_company_user as $key => $d) {
            $data[$key]['zoom_room_id'] = $dataZoomRoom->id;
            $data[$key]['no'] = $key + 1;
            $data[$key]['id'] = $d->id;
            $data[$key]['email'] = $d->email;
        }
        return response()->json([
            'status' => 200,
            'message' => 'ok',
            'data' => $data
        ], 200);
    }

    public function delete_invitation(Request $request)
    {
        $dataZoomRoom = ZoomRoom::whereId($request->zoom_room_id)->first();

        $dataZoomRoom->itdp_company_user()->detach($request->user_id);

        $data = [];
        foreach ($dataZoomRoom->itdp_company_user as $key => $d) {
            $data[$key]['zoom_room_id'] = $dataZoomRoom->id;
            $data[$key]['no'] = $key + 1;
            $data[$key]['id'] = $d->id;
            $data[$key]['email'] = $d->email;
        }

        return response()->json([
            'status' => 200,
            'message' => 'ok',
            'data' => $data
        ], 200);
    }
}
