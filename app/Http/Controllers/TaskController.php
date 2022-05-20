<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

class TaskController extends Controller
{
    public function get_date_view()
    {
        return view("task.get_date_view");
    }

    public function submit_datepicker_form(Request $request)
    {
        $request->validate([
            'start_date'          => 'required|date',
            'end_date'         => 'required|date',
        ]);

        $start_date = new DateTime($request->get('start_date'));
        $end_date = new DateTime($request->get('end_date'));
        $difference = $start_date->diff($end_date);
        $days = $difference->d;
        if($days<=7) {
            $api_key = "tgu83iUzq3Ebfx49KQUhMZDtGFvmzSSTFJYmN4ZS";
            $start_date = $start_date->format('Y-m-d');
            $end_date = $end_date->format('Y-m-d');
            $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=".$start_date."&end_date=".$end_date."&api_key=".$api_key;
            $client = new \GuzzleHttp\Client();
            $res = $client->get($url);
            $content = (string) $res->getBody();
            $data = json_decode($content, TRUE);
            $dates_arr=[];
            $closest_approarch_data =[];
            $element_count = $data['element_count'];
            $get_close_min_arr=[];
            $get_close_max_arr=[];
            $sum_miss_distance = 0;
            if(isset($data['near_earth_objects'])) {
                foreach($data['near_earth_objects'] as $near_earth_objects_keys => $near_earth_objects_vals) {
                    $dates_arr[$near_earth_objects_keys] = count($near_earth_objects_vals);
                    foreach($near_earth_objects_vals as $k => $val) {
                        $closest_approarch_data[$val['id']]['close_approach_date_full'] = date("H:i",strtotime($val['close_approach_data'][0]['close_approach_date_full']));
                        // miss_distance
                        $closest_approarch_data[$val['id']]['miss_distance'] = $val['close_approach_data'][0]['miss_distance']['kilometers'];
                        $closest_approarch_data[$val['id']]['relative_velocity'] = $val['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'];
                    }
                }
                $get_min_ast = $this->getMin($closest_approarch_data,"close_approach_date_full");
                $get_max_ast_Speed = $this->getMax($closest_approarch_data,"relative_velocity");
            
                foreach($closest_approarch_data as $k => $v) {
                    if($v['close_approach_date_full'] == $get_min_ast) {
                        $get_close_min_arr=['id'=>$k,'distance'=>$v['miss_distance']];
                    }
                    if($v['relative_velocity'] == $get_max_ast_Speed) {
                        $get_close_max_arr=['id'=>$k,'speed'=>$v['relative_velocity']];
                    }
                    $sum_miss_distance = $sum_miss_distance+$v['miss_distance'];
                }
                $avg_miss_distance = $sum_miss_distance/$element_count;

            }

            ksort($dates_arr);
        
        return response()->json(['graph_dates_arr'=>array_keys($dates_arr),'total_count_objs'=>array_values($dates_arr),'Fastest_Asteroid'=>$get_close_max_arr,'closest_asteroid'=>$get_close_min_arr,'avg_size_of_asteroid'=>$sum_miss_distance]);
        } else {
            return response()->json(['status'=>'false','message'=>"Please select between 7 days only"]);
        }
    }

    function getMin($array,$key)
    {
        $min = PHP_INT_MAX;
        foreach( $array as $k => $v )
        {
            $min = min($min, $v[$key]);
        }
        return $min;
    }

    function getMax($array,$key)
    {
        $max = -PHP_INT_MAX;
        foreach( $array as $k => $v )
        {
            $max = max($max, $v[$key]);
        }
        return $max;
    }
}
