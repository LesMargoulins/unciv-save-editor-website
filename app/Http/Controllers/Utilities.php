<?php

namespace App\Http\Controllers;

use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Utilities extends Controller
{
    public $users_table_name;

    public $role_admin;
    public $mysql_date_format;
    public $date_format; //not datetime
    public $date_format_readable;
    public $default_id_delimiter;
    public $time_minute_len;
    public $time_hour_len;
    public $time_day_len;
    public $time_year_len;

    public function __construct()
    {
        $this->users_table_name = 'users';

        $this->role_admin = 'admin';
        $this->mysql_date_format = "Y-m-d H:i:s";
        $this->date_format = "Y-m-d";
        $this->date_format_readable = "d-m-Y";
        $this->default_id_delimiter = ';';
        $this->time_minute_len = 60;
        $this->time_hour_len = 3600;
        $this->time_day_len = 86400;
        $this->time_year_len = 31557600;
    }

    /*
     * DATABASE
     */
    public function db_delete_one($table, $ref_id, $id)
    {
        DB::table($table)
            ->where($ref_id, $id)
            ->delete();
    }

    public function db_get_one($table, $ref_id, $id)
    {
        $result = DB::table($table)
            ->where($ref_id, $id)
            ->get()
            ->toArray();
        return (isset($result[0]) and $result[0]) ? $result[0] : null;
    }

    public function db_get_one_only_fields($table, $ref_id, $id, ...$select_only)
    {
        $result = DB::table($table)
            ->select(...$select_only)
            ->where($ref_id, $id)
            ->get()
            ->toArray();
        return (isset($result[0]) and $result[0]) ? $result[0] : null;
    }

    public function db_get_array($table, $ref_id, $id)
    {
        $result = DB::table($table)
            ->where($ref_id, $id)
            ->get()
            ->toArray();
        return (isset($result) and $result) ? $result : null;
    }

    public function db_get_one_two_cond($table, $ref_id, $id, $ref_cond, $cond)
    {
        $result = DB::table($table)
            ->where($ref_id, $id)
            ->where($ref_cond, $cond)
            ->get()
            ->toArray();
        return (isset($result[0]) and $result[0]) ? $result[0] : null;
    }

    public function db_get_array_two_cond($table, $ref_id, $id, $ref_cond, $cond)
    {
        $result = DB::table($table)
            ->where($ref_id, $id)
            ->where($ref_cond, $cond)
            ->get()
            ->toArray();
        return (isset($result) and $result) ? $result : null;
    }

    public function db_add_one($table, $data)
    {
        $id = DB::table($table)->insertGetId($data);

        return (isset($id) and $id) ? $id : null;
    }

    public function db_update_one($table, $ref_id, $id, $data)
    {
        return DB::table($table)
            ->where($ref_id, $id)
            ->update($data);
    }

    public function db_get_between_dates($table, $ref, $ref_val, $ref_between, $ref_from_val, $ref_to_val)
    {
        $result = DB::table($table)
            ->where($ref, $ref_val)
            ->whereBetween($ref_between, [$ref_from_val, $ref_to_val])
            ->get()
            ->toArray();
        return isset($result[0]) ? $result : null;
    }

    public function db_get_array_from_today($table, $ref_id, $id, $ref_today)
    {
        $result = DB::table($table)
            ->where($ref_id, $id)
            ->where($ref_today, '<', $this->get_actual_date())
            ->get()
            ->toArray();
        return isset($result[0]) ? $result : null;
    }

    public function db_get_array_all_table($table)
    {
        $result = DB::table($table)
            ->get()
            ->toArray();
        return (isset($result) and $result) ? $result : null;
    }

    /*
     * ID MANAGEMENT
     */
    public function id_get_array($id_str)
    {
        return explode($this->default_id_delimiter, $id_str);
    }

    public function id_is_in_list($id_str, $id_ref)
    {
        if (!isset($id_str) || !$id_str)
        {
            return null;
        }
        $id_array = $this->id_get_array($id_str);
        return (isset($id_array) && in_array($id_ref, $id_array));
    }

    public function id_add_one($id_str, $id_new)
    {
        if (!isset($id_array) || !$id_str)
        {
            return (string)$id_new;
        }
        if ($this->id_is_in_list($id_str, $id_new))
        {
            return $id_str;
        }
        return $id_str . $this->default_id_delimiter . $id_new;
    }

    public function id_remove_one($id_str, $id_del)
    {
        if (!isset($id_str))
        {
            return null;
        }
        $id_list = $this->id_get_array($id_str);
        $i = 0;
        $first = true;
        $ret = "";
        while (isset($id_list[$i]))
        {
            if ($id_list[$i] != $id_del)
            {
                if (!$first)
                {
                    $ret .= $this->default_id_delimiter;
                }
                else
                {
                    $first = false;
                }
                $ret .= (string)$id_list[$i];
            }
            ++$i;
        }
        return (!$first) ? $ret : null;
    }

    public function id_get_amount($id_str)
    {
        $i = 0;
        $arr = explode($this->default_id_delimiter, $id_str);
        while (isset($arr[$i]))
        {
            ++$i;
        }
        return $i;
    }

    public function db_id_is_in_list($table, $id_ref_row, $id_row, $id_column_ref, $id_search)
    {
        $elem = $this->db_id_get_array($table, $id_ref_row, $id_row, $id_column_ref);
        return (isset($elem) && $this->id_is_in_list($elem, $id_search));
    }

    public function db_id_add_one_in_list($table, $id_ref_row, $id_row, $id_column_ref, $id_new)
    {
        $elem = $this->db_id_get_array($table, $id_ref_row, $id_row, $id_column_ref);
        if (!isset($elem))
        {
            return false;
        }
        if (!$elem)
        {
            return $this->db_update_one($table, $id_ref_row, $id_row, [ $id_column_ref => $this->id_add_one(null, $id_new) ]);
        }
        return $this->db_update_one($table, $id_ref_row, $id_row, [ $id_column_ref => $this->id_add_one($elem, $id_new) ]);
    }

    public function db_id_remove_one_in_list($table, $id_ref_row, $id_row, $id_column_ref, $id_removed)
    {
        $elem = $this->db_id_get_array($table, $id_ref_row, $id_row, $id_column_ref);
        if (!isset($elem) || !$elem)
        {
            return false;
        }
        return $this->db_update_one($table, $id_ref_row, $id_row, [ $id_column_ref => $this->id_remove_one($elem, $id_removed) ]);
    }

    public function db_id_get_array($table, $id_ref_row, $id_row, $id_column_ref)
    {
        $elem = $this->stdclass_to_array($this->db_get_one_only_fields($table, $id_ref_row, $id_row, $id_column_ref));
        if (!$elem)
        {
            return null;
        }
        return (!isset($elem[$id_column_ref]) ? false : $elem[$id_column_ref]);
    }

    public function db_id_get_amount($table, $id_ref_row, $id_row, $id_column_ref)
    {
        $elem = $this->stdclass_to_array($this->db_get_one_only_fields($table, $id_ref_row, $id_row, $id_column_ref));
        if (!$elem)
        {
            return null;
        }
        return (!isset($elem[$id_column_ref]) ? false : $this->id_get_amount($elem[$id_column_ref]));
    }

    /*
     * UPLOAD
     */
//    public function save_image_array($images, $user_id, $image_type, $ref_id)
//    {
//        if (!is_array($images))
//        {
//            $this->save_image($images, $user_id, $image_type, $ref_id);
//        }
//        else
//        {
//            foreach ($images as $image)
//            {
//                $this->save_image($image, $user_id, $image_type, $ref_id);
//            }
//        }
//    }
//
//    public function save_image($image, $user_id, $image_type, $ref_id)
//    {
//        $info = [];
//        $info['filetype'] = $image->getClientOriginalExtension();
//        $info['filename'] = time() . '_' . uniqid(rand(), true) . '.' . $info['filetype'];
//        $info['original_filename'] = $image->getClientOriginalName();
//        $info['image_type'] = $this->get_image_type($image_type)['id'];
//        $info['ref_id'] = $ref_id;
//        $destination = '/users/' . $user_id . '/' . $this->get_image_type($image_type)['name'];
//        $image->storeAs($destination, $info['filename']);
//        $this->db_add_one($this->images_table_name, $info);
//    }

    public function get_image_type($image_type)
    {
        if ($image_type == 'prescription' or $image_type == 'prescriptions')
        {
            return ['id' => 0, 'name' => 'prescriptions'];
        }
        return null;
    }

    public function response_handle($data = null, $code = 0, $err = null)
    {
        $ret = [];
        $ret['code'] = $code;
        if (isset($err))
        {
            $ret['reason'] = $err;
        }
        if (isset($data))
        {
            foreach ($data as $key => $value)
            {
                $ret[$key] = $value;
            }
        }
        return \Response::json($ret);
    }

    public function format_response($ret, $error = false, $error_code = 400)
    {
        if ((isset($ret) && $ret))
        {
            if (!$error)
            {
                return \Response::json(response()->json($ret, 200));
            }
            return \Response::json(response()->json(["error" => $ret], $error_code));
        }
        return \Response::json(response()->json("OK", 200));
    }

    public function is_one_elem_in_both_array($array_1, $array_2)
    {
        $x = 0;
        while (isset($array_1[$x]))
        {
            if (in_array($array_1[$x], $array_2))
            {
                return true;
            }
            ++$x;
        }
        return false;
    }

    /*
     * OTHER SINCE PHP IS SHIT
     */
    public function array_delete_key($arr, $key)
    {
        $last = sizeof($arr) - 1;
        $arr[$key] = $arr[$last];
        unset($arr[$last]);
        return $arr;
    }

    public function stdclass_to_array($object)
    {
        return json_decode(json_encode($object), true);
    }

    public function my_is_int($input)
    {
        return (filter_var($input, FILTER_VALIDATE_INT) !== false);
    }

    public function my_is_datetime($input)
    {
        return (date($this->mysql_date_format, strtotime($input)) == $input);
    }

    public function generate_random_string($length, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function my_merge_array($array1, $array2)
    {
        $ret = $array1;
        $i = sizeof($ret);
        $x = 0;
        while (isset($array2[$x]))
        {
            $ret[$i] = $array2[$x];
            ++$i;
            ++$x;
        }
        return $ret;
    }


    /*
     * DATE
     */
    public function date_interval_to_seconds($date)
    {
        return  $date->s +
            ($date->i * 60) +
            ($date->h * 3600) +
            ($date->d * 86400) +
            ($date->m * 2629800) +
            ($date->y * 31557600);
    }

    public function get_actual_date()
    {
        return date($this->mysql_date_format);
    }

    public function date_get()
    {
        return $this->get_actual_date();
    }

    public function date_sql_to_seconds($date_str)
    {
        $date = new DateTime($date_str);
        return $date->getTimestamp();
    }

    public function date_seconds_to_sql($timestamp)
    {
        return date($this->mysql_date_format, $timestamp);
    }

    public function date_compare_from($date_ref, $date)
    {
        return $this->date_sql_to_seconds($date) - $this->date_sql_to_seconds($date_ref);
    }

    public function date_compare_from_now($date)
    {
        return $this->date_compare_from($this->get_actual_date(), $date);
    }

    public function date_get_readable($date)
    {
        return date($this->date_format_readable, strtotime($date));
    }

    private function add_months($months, DateTime $dateObject)
    {
        $next = new DateTime($dateObject->format($this->mysql_date_format));
        $next->modify('last day of +' . $months . ' month');

        if ($dateObject->format('d') > $next->format('d'))
        {
            return $dateObject->diff($next);
        }
        else
        {
            return new DateInterval('P' . $months . 'M');
        }
    }

    private function endCycle($d1, $months)
    {
        $date = new DateTime($d1);

        $newDate = $date->add($this->add_months($months, $date));
//        $newDate->sub(new DateInterval('P1D'));
        $dateReturned = $newDate->format($this->mysql_date_format);
        return $dateReturned;
    }

    private function human_readable_delay_from_seconds_append($str, $unit, $unit_name)
    {
        if ($unit > 0)
        {
            if ($str != '')
            {
                $str .= ', ';
            }
            return $str . $unit . ' ' . $unit_name;
        }
        return $str;
    }

    public function human_readable_delay_from_seconds($seconds)
    {
        $years = intval($seconds / $this->time_year_len);
        $seconds %= $this->time_year_len;
        $days = intval($seconds / $this->time_day_len);
        $seconds %= $this->time_day_len;
        $hours = intval($seconds / $this->time_hour_len);
        $seconds %= $this->time_hour_len;
        $minutes = intval($seconds / $this->time_minute_len);
        $seconds %= $this->time_minute_len;

        //'years'
        //'days'
        //'hours'
        //'minutes'
        //'seconds'

        $ret = $this->human_readable_delay_from_seconds_append('', $years, 'annees');
        $ret = $this->human_readable_delay_from_seconds_append($ret, $days, 'jours');
        $ret = $this->human_readable_delay_from_seconds_append($ret, $hours, 'heures');
        $ret = $this->human_readable_delay_from_seconds_append($ret, $minutes, 'minutes');
        $ret = $this->human_readable_delay_from_seconds_append($ret, $seconds, 'secondes');

        return $ret;
    }

    public function date_add_time($date, $seconds = 0, $minutes = 0, $hours = 0, $days = 0, $months = 0, $years = 0)
    {
        if ($months > 0)
        {
            $date = $this->endCycle($date, $months);
            Log::debug('add ' . $months . ' months');
        }
        $seconds += $minutes * $this->time_minute_len;
        $seconds += $hours * $this->time_hour_len;
        $seconds += $days * $this->time_day_len;
        $seconds += $years * $this->time_year_len;
        if ($seconds > 0)
        {
            $seconds += $this->date_sql_to_seconds($date);
            $date = $this->date_seconds_to_sql($seconds);
        }
        return $date;
    }

    public function date_add_time_from_now($seconds = 0, $minutes = 0, $hours = 0, $days = 0, $months = 0, $years = 0)
    {
        return $this->date_add_time($this->date_get(), $seconds, $minutes, $hours, $days, $months, $years);
    }

    public function date_is_in_past($date)
    {
        return $this->date_compare_from_now($date) <= 0;
    }

    /*
     * RANDOM HASH
     */
    public function hash_collision_check($hash, $table, $column)
    {
        $ret = $this->db_get_one($table, $column, $hash);
        return isset($ret);
    }

    public function hash_generate_new($len, $table = false, $column = false)
    {
        $new_hash =$this->generate_random_string($len);
        if ($table && $column)
        {
            while ($this->hash_collision_check($new_hash, $table, $column))
            {
                $new_hash =$this->generate_random_string($len);
            }
        }
        return $new_hash;
    }

    public function fix_json_key($string)
    {
        $regex = '/(?<!")([a-zA-Z0-9_\-\ ]+)(?!")(?=:)/i';
        return preg_replace($regex, '"$1"', $string);
    }

    public function fix_json_string($string)
    {
        $regex = '/(?<=enemy |our )(\[)([\w\s\-\.]+)(\])(?=\"| has| was)/imU';
        $string = preg_replace($regex, '_NONREDONDANT_LMAO_OPEN_$2_NONREDONDANT_LMAO_CLOSE_', $string);
        $regex = '/(?<=:|\[|\,)([\w\s\-\.]+)(?=[\[,\]\{\}])/imU';
        $string = preg_replace($regex, '"$1"', $string);
        $regex = '/(_NONREDONDANT_LMAO_OPEN_)([\w\ ]*)(_NONREDONDANT_LMAO_CLOSE_)/imU';
        $string = preg_replace($regex, '[$2]', $string);
        return $string;
    }
}