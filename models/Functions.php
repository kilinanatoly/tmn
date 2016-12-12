<?php


namespace app\models;


use budyaga\users\models\User;
use Yii;

use yii\base\Model;
use yii\web\NotFoundHttpException;


/**
 * LoginForm is the model behind the login form.
 */
class Functions extends Model
{
    public function ad_name($value)
    {
        return strtolower($this->translit(trim($value['name']))) . '-' . $value['id'];
    }


    public function translit($str)
    {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', ',', '"', '_');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', '', 'Y', 'E', 'E', 'Ju', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'ju', 'ya', '-', '-', '', '', '');
        return str_replace($rus, $lat, $str);
    }

    public function str2url($str)
    {
        // переводим в транслит
        $str = $this->translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        //~[^-a-z0-9_]+~u (было)
        $str = preg_replace('~[^-a-z0-9_]+~i', '-', $str);
        $str = preg_replace('~([-])\1+~i', '\\1', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        //удаляем начинания цифр
        return $str;
    }

    public static function get_ad_date($date)
    {
        $date = explode('.', $date);
        switch ($date[1]) {
            case '01':
                $month = 'января';
                break;
            case '02':
                $month = 'февраля';
                break;
            case '03':
                $month = 'марта';
                break;
            case '04':
                $month = 'апреля';
                break;
            case '05':
                $month = 'мая';
                break;
            case '06':
                $month = 'июня';
                break;
            case '07':
                $month = 'июля';
                break;
            case '08':
                $month = 'августа';
                break;
            case '09':
                $month = 'сентября';
                break;
            case '10':
                $month = 'октября';
                break;
            case '11':
                $month = 'ноября';
                break;
            case '12':
                $month = 'декабря';
                break;
        }
        return $date = $date[0] . ' ' . $month . ' ' . $date[2];
    }

    public static function clearPickup($user_id){
        $myPuckups = PerepiskaQueries::findOne(['sender_id'=>$user_id]);
        if ($myPuckups){
            User::updateAll(['employment'=>0],'id = '.$myPuckups->sender_id.' OR id='.$myPuckups->recipient_id.'');
        }
    }
    public static function clearPickupCurrentUser(){
        $current_user = Yii::$app->user->id;
        $myPuckups = PerepiskaQueries::find()->where('sender_id='.$current_user.' OR recipient_id='.$current_user.'')->orderBy('id DESC')->limit(1)->one();
        $m = new Tmp();
        $m->text = $myPuckups->sender_id.','.$myPuckups->recipient_id;
        $m->save();
        if ($myPuckups){
            User::updateAll(['employment'=>0],'id = '.$myPuckups->sender_id.' OR id='.$myPuckups->recipient_id.'');
        }
    }
    public static function clearPickupID($query_id){
        $myPuckups = PerepiskaQueries::findOne($query_id);
        if ($myPuckups){
            $myPuckups->delete();
            User::updateAll(['employment'=>0],'id = '.$myPuckups->sender_id.' OR id='.$myPuckups->recipient_id.'');
        }
    }
    public static function calculate_age($birthday) {
        $birthday_timestamp = strtotime($birthday);
        $age = date('Y') - date('Y', $birthday_timestamp);
        if (date('md', $birthday_timestamp) > date('md')) {
            $age--;
        }
        return $age;
    }

}

