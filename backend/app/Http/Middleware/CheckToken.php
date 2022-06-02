<?php

/**
 * Created by PhpStorm.
 * User: Xoxo
 * Date: 12/04/2021
 * Time: 4:42 PM
 */

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Traits\AuthToken;

class CheckToken
{
    use AuthToken;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tokenForBridging = false;

        $token =  $request->header('X-AUTH-TOKEN');
        if (!$token) {
            $token =  $request->header('x-token');
            $tokenForBridging = true;
        }
        
        try {
            if ($token) {
                $arr = explode('.', $token);

                $token = $arr[0] . '.' . $arr[1] . '.' . $arr[2];
                if ($tokenForBridging == true) {
                    if (!isset($arr[3])) {
                        $result = array(
                            "metadata" =>
                            array(
                                'message' => "Token tidak valid",
                                'code' => 201,
                            )
                        );
                        return response()->json($result);
                        // $data = array(
                        //     'code' => 403,
                        //     'message' => trans('auth.token_not_valid')
                        // );
                        // return Response::json($data, 403)->header('X-MESSAGE', trans('auth.token_not_valid'));
                    }
                    $expired = base64_decode($arr[3]);
                }
                if (!$this->checkToken($token)) {
                    $result = array(
                        "metadata" =>
                        array(
                            'message' => "Token tidak valid",
                            'code' => 201,
                        )
                    );
                    return response()->json($result);
                    // $data = array(
                    //     'code' => 403,
                    //     'message' => trans('auth.token_not_valid')
                    // );
                    // return Response::json($data, 403)->header('X-MESSAGE', trans('auth.token_not_valid'));
                } else {
                    if ($this->userData != null) {
                        if ($tokenForBridging == true) {
                            $exp = date('Y-m-d H:i:s', strtotime($expired));

                            $now = date('Y-m-d H:i:s');
                            if (!($exp >= $now)) {
                                $result = array(
                                    "metadata" =>
                                    array(
                                        'message' => "Token Expired",
                                        'code' => 201,
                                    )
                                );
                                return response()->json($result);
                                // $data = array(
                                //     'code' => 403,
                                //     'message' => trans('auth.token_expired')
                                // );
                                // return Response::json($data, 403)->header('X-MESSAGE', trans('auth.token_expired'));
                            }
                        }
                        $userData = $this->userData;
                        $request->merge(compact('userData'));
                    }
                }
            } else {
                $result = array(
                    "metadata" =>
                    array(
                        'message' => "Token tidak tersedia",
                        'code' => 201,
                    )
                );
                return response()->json($result);
                // $data = array(
                //     'code' => 401,
                //     'message' => trans('auth.token_not_provided')
                // );
                // return Response::json($data, 401)->header('X-MESSAGE', trans('auth.token_not_provided'));
            }


            return $next($request);
        } catch (\Exception $e) {
            $result = array(
                "metadata" =>
                array(
                    'message' => "Token tidak valid",
                    'code' => 201,
                )
            );
            return response()->json($result);
        }
    }
}
