<?php

namespace app\controllers;

use app\models\Contacts;
use app\models\Functions;
use app\models\Messages;
use app\models\PerepiskaQueries;
use budyaga\users\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;

//0 - новое уведомление
//1 - аппрув получателя
//2 - отмена получателя
//3 - отмена отправителя
//4 - пользователь окончил чат
//5 - отмена дружбы сендера
//6 - отмена дружбы получателя
//7 - обрыв связи
//8 - не поднял трубку
//9 - не принял дружбу, время истекло




class AjaxController extends Controller
{
    public function actionGetuser1()
    {
        if (Yii::$app->request->post('user_id')) {
            $user_id = Yii::$app->request->post('user_id');
            if ($user = User::findOne($user_id)) {
                $html = '
                    <div class="new-pickup1">
                        <div class="new-pickup1-block1 ">
                            <div class="photo">
                                <img class="img-responsive" src="' . $user->photo . '">
                            </div>
                        </div>
                        <div class="new-pickup1-block2">
                            <div class="new-pickup1-block2__name">' . $user->username . '</div>
                            <div class="new-pickup1-block2__regdate">Дата регистрации: ' . Functions::get_ad_date(date('d.m.Y', $user->created_at)) . '</div>
                            <div class="new-pickup1-block2__age">Возраст: '.(Functions::calculate_age($user->birthday)).'</div>
                            <div class="new-pickup1-block2__city">Город: '.Yii::$app->params['cities'][$user->city].'</div>
                            <hr>
                            <input type="hidden" name="user_id" value="' . $user->id . '">
                            <p class="status new-pickup1-block2__status"></p>
                            <button type="submit" class="btn submit new-pickup1-block2__btn1" data-loading-text="Подождите...">Познакомиться</button>
                            <button type="submit" class="btn canceled new-pickup1-block2__btn2" data-dismiss="modal" aria-hidden="true">Закрыть</button>
                        </div>
                    </div>
                ';
                return $html;
            }
        }

        return 'fail';
    }

    public function actionPickup(){
        if (Yii::$app->request->post('user_id')){
            if ($user = User::findOne(Yii::$app->request->post('user_id'))){
                if ($user->employment == 0){
                    $model = new PerepiskaQueries();
                    $model->sender_id = Yii::$app->user->id;
                    $model->recipient_id = $user->id;
                    $model->status = 0;
                    if ($model->save()){
                        $user->employment = 1;
                        $user->save();
                        $sender_user = User::findOne(Yii::$app->user->id);
                        $sender_user->scenario = 'sc1';
                        $sender_user->employment = 1;
                        $sender_user->save();

                        $res['status'] =  'success';
                    }else{
                        $res['status'] =  'fail';
                    }
                    return Json::encode($res);

                }
                $res['status'] = 'employment';
                $res['message'] = 'Пользователь занят, попробуйте позже';
                return Json::encode($res);
                }
        }
        $res['status'] = 'fail';
        return Json::encode($res);

    }

    public function actionCheckPickup(){
        User::updateAll(['last_activity'=>date('Y-m-d H:i:s')],'id = '.Yii::$app->user->id.'');
        $model = PerepiskaQueries::find()
            ->with('sender')
            ->with('recipient')
            ->with('messages')
            ->where('perepiska_queries.sender_id = '.Yii::$app->user->id.' OR perepiska_queries.recipient_id='.Yii::$app->user->id.'')
            ->limit(1)
            ->orderBy('perepiska_queries.id DESC')
            ->one();
        if (!$model) return false;

        //проверка на истечение времени
        if ($model->status == 8){
            $res['status'] = 'pickup_time_end';
            $res['message'] = 'Время ожидания истекло';
            $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
            Functions::clearPickupCurrentUser();
            return Json::encode($res);
        }
        //проверка на истечение времени при аппруве одного из собеседников
        if ($model->status == 9){
            $res['status'] = 'approve_friend_time_end';
            $res['message'] = 'Время ожидания истекло';
            $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
            Functions::clearPickupCurrentUser();
            return Json::encode($res);
        }
        //проверка на обрыв связи
        if (($model->recipient->employment==0 || $model->sender->employment==0) || ((strtotime(date('Y-m-d H:i:s').' +1 hours') - strtotime($model->recipient->last_activity.' +1 hours'))>20 || (strtotime(date('Y-m-d H:i:s').' +1 hours')-strtotime($model->sender->last_activity.' +1 hours'))>20)){
            if ($model->status==1){
                $model->status = 7;
                $model->save();
            }
        }
        if ($model->recipient_id==Yii::$app->user->id){
            //для получателя
            if ($model->status == 5){
                $res['status'] = 'cancel_friend';
                $res['message'] = 'Пользователь не принял дружбу';
                Functions::clearPickupCurrentUser();
                $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
                return Json::encode($res);
            }
            if ($model->status==0 && $model->pokaz==0){
                $res['status'] = 'new_pickup';
                $res['html'] = '
                <div class="new-pickup1">
                    <div class="block1 inline_block">
                            <div class="photo">
                                <img class="img-responsive" src="' . $model->sender->photo . '">
                            </div>
                        </div>
                        <div class="block2 inline_block">
                            <h2>' . $model->sender->username . '</h2>
                            <p>Дата регистрации:' . date('d.m.Y', $model->sender->created_at) . '</p>
                            <hr>
                            <input type="hidden" name="user_id" value="' . $model->sender->id . '">
                            <button type="submit" class="btn btn-success approve" data-loading-text="Подождите...">Ответить</button>
                            <button type="submit" class="btn btn-danger canceled" data-dismiss="modal" aria-hidden="true">Отклонить</button>
                            <p class="status"></p>
                     </div>
                </div>
                ';
                $model->pokaz = 1;
                $model->save();
                return Json::encode($res);
            }
            if ($model->status == 1 && $model->pokaz==1){
                $res['status'] = 'approve_pickup_recipient';
                $res['html'] = '
                <div class="block1 inline_block">
                        <div class="photo">
                            <img class="img-responsive" src="' . $model->sender->photo . '">
                        </div>
                        <div class="three_minutes_timer">
                            <p>3:00</p>
                        </div>
                    </div>
                    <div class="block2 inline_block">
                        <h2>' . $model->sender->username . '</h2>
                        <hr>
                        <div class="message_block"></div>
                        <hr>
                        <div class="input_block inline_block">
                            <input class="form-control" type="text" placeholder="Введите сообщение..." id="message_input">
                        </div>
                        <div class="buttons_block inline_block">
                            <button type="submit" class="btn btn-success submit_message">Отправить</button>
                            <button type="submit" class="btn btn-danger destroy_dialog" data-dismiss="modal" aria-hidden="true">Окончить диалог</button>
                        </div>
                        
                        <p class="status"></p>
                 </div>
                ';
                $model->pokaz = 2;
                $model->save();
                return Json::encode($res);

            }
            if ($model->status==3){
                $res['status'] = 'canceled_sender';
                $res['message'] = 'Пользователь отменил вызов';
                return Json::encode($res);
            }
        }else{
            //для отправителя
            if ($model->status == 6){
                $res['status'] = 'cancel_friend';
                $res['message'] = 'Пользователь отклонил дружбу';
                Functions::clearPickupCurrentUser();
                $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
                return Json::encode($res);
            }
            //если апрув
            if ($model->status==1 && $model->pokaz==2){
                $res['status'] = 'approve_pickup_sender';
                $res['html'] = '
                <div class="block1 inline_block">
                        <div class="photo">
                            <img class="img-responsive" src="' . $model->recipient->photo . '">
                        </div>
                        <div class="three_minutes_timer">
                            <p>3:00</p>
                        </div>
                    </div>
                    <div class="block2 inline_block">
                        <h2>' . $model->recipient->username . '</h2>
                        <hr>
                        <div class="message_block"></div>
                        <hr>
                        <div class="input_block inline_block">
                            <input class="form-control" type="text" placeholder="Введите сообщение..." id="message_input">
                        </div>
                        <div class="buttons_block inline_block">
                            <button type="submit" class="btn btn-success submit_message">Отправить</button>
                            <button type="submit"  class="btn btn-danger destroy_dialog" data-dismiss="modal" aria-hidden="true">Окончить диалог</button>
                        </div>
                        
                        <p class="status"></p>
                 </div>
                ';
                $model->pokaz=3;
                $model->save();
                return Json::encode($res);
            }
            //если отклонил
            if ($model->status==2){
                $res['status'] = 'canceled_pickup';
                $res['message'] = 'Пользователь отклонил Ваше предложение';
                return Json::encode($res);
            }
        }
        //проверка на новые сообщения
        if ($model->messages){
            $html = '';
            foreach ($model->messages as $key => $value) {
                $html.='<div class="sender_message_wrap">
                            <div class="sender_message">
                                '.$value->message.'
                            </div>
                        </div>';
            }
            Messages::updateAll(['status'=>1], 'query_id = '.$model->id.' AND recipient_id = ' . Yii::$app->user->id . '');
            $res['status'] = 'new_message_sender';
            $res['html'] = $html;
            return Json::encode($res);
        }
        //проверка на отбой
        if ($model->status == 4){
            $res['status'] = 'chat_end';
            $res['message'] = 'Пользователь закончил чат и покинул Вас';
            return Json::encode($res);
        }
        if ($model->approve_friend_sender_check==1 && $model->approve_friend_recipient_check==1){
            Functions::clearPickupCurrentUser();
        }
        //проверка на оконнчание времени
        if ($model->status==1 && strtotime($model->end_chat_date)<=strtotime(date('Y-m-d H:i:s'))){
            if ($model->sender_id == Yii::$app->user->id){
                if ($model->approve_friend_sender!=1){
                    $res['status'] = 'time_end';
                    $res['message'] = 'Время чата закончилось, самое время сделать выбор';
                    $res['buttons'] = '<button type="submit" class="btn btn-success approve_friend" data-loading-text="Ожидание ответа">Добавить в контакты</button>
                                       <button type="submit" class="btn btn-danger cancel_friend" >Отклонить дружбу</button>';
                    return Json::encode($res);
                }
            }
            if ($model->recipient_id == Yii::$app->user->id){
                if ($model->approve_friend_recipient!=1){
                    $res['status'] = 'time_end';
                    $res['message'] = 'Время чата закончилось, самое время сделать выбор';
                    $res['buttons'] = '<button type="submit" class="btn btn-success approve_friend" data-loading-text="Ожидание ответа">Добавить в контакты</button>
                                       <button type="submit" class="btn btn-danger cancel_friend" >Отклонить дружбу</button>';
                    return Json::encode($res);
                }
            }

        }

        //проерка на аппрув дружбы
        if ($model->status ==1 && $model->approve_friend_sender && $model->approve_friend_recipient){

            if ($model->sender_id == Yii::$app->user->id){
                if ($model->approve_friend_sender_check==0){
                    $contacts = new Contacts();
                    $contacts->user_1 = $model->sender_id;
                    $contacts->user_2 = $model->recipient_id;
                    $contacts->save();
                }
                $model->approve_friend_sender_check = 1;
            }else{
                if ($model->approve_friend_recipient_check==0){
                    $contacts = new Contacts();
                    $contacts->user_2 = $model->sender_id;
                    $contacts->user_1 = $model->recipient_id;
                    $contacts->save();
                }
                $model->approve_friend_recipient_check = 1;
            }
            $model->save();
            $contacts = Contacts::find()
            ->where('user_1 = ' . Yii::$app->user->id . '')
            ->orderBy('reg_date DESC')
            ->with('user')
            ->all();
            $html = '<ul class="contacts">';
            foreach ($contacts as $key => $value) {
                $html.= '<li><a href="#" data-id="'.$value->user->id.'">'.$value->user->username.'</a></li>';
            }
            $html.='</ul>';
            $res['status'] = 'approve_friend';
            $res['message'] = 'Пользователь добавил Вас в контакты';
            $res['contacts'] = $html;
            $res['buttons'] = '<button type="submit" class="btn btn-danger close_chat_modal" data-dismiss="modal" aria-hidden="true">Закрыть</button>';
            return Json::encode($res);
        }

        //проверка на обрыв связи
        if ($model->status==7){
            $res['status'] = 'obryv';
            $res['message'] = 'Обрыв связи';
            $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
            Functions::clearPickupCurrentUser();
            return Json::encode($res);
        }

        $res['status'] = 'Ожидание';
        return Json::encode($res);

    }

    public function actionApprove(){
        $current_user = Yii::$app->user->id;
        if ($model = PerepiskaQueries::find()->where('recipient_id = '.$current_user.'')->orderBy('id DESC')->limit(1)->one()){
            $model->status = 1;
            $model->end_chat_date = date('Y-m-d H:i:s',strtotime('+10 seconds'));
            $model->save();
            return 'success';
        }
        return 'fail';
    }
    public function actionApprove_friend(){
        $current_user = Yii::$app->user->id;
        $model = PerepiskaQueries::find()
            ->where('recipient_id=' . $current_user . ' OR sender_id = '.$current_user.'')
            ->limit(1)
            ->orderBy('id DESC')
            ->one();
        if ($model){
            if ($model->sender_id == $current_user){
                $model->approve_friend_sender = 1;
            }else{
                $model->approve_friend_recipient = 1;
            }
            $model->save();
        }
    }
    public function actionCanceled_recipient(){
        $current_user = Yii::$app->user->id;
        if ($model = PerepiskaQueries::find()->where('recipient_id = '.$current_user.'')->orderBy('id DESC')->limit(1)->one()){
            $model->status = 2;
            $model->save();
            Functions::clearPickupCurrentUser();
            return 'success';
        }
        return 'fail';
    }
    public function actionCanceled_sender(){
        $current_user = Yii::$app->user->id;
        if ($model = PerepiskaQueries::find()->where('sender_id = '.$current_user.'')->orderBy('id DESC')->limit(1)->one()){
            $model->status = 3;
            $model->save();
            Functions::clearPickupCurrentUser();
            return 'success';
        }

        return 'fail';
    }

    public function actionSubmit_message(){
        if (Yii::$app->request->post('message')){
            $current_user = Yii::$app->user->id;
            $model = PerepiskaQueries::find()->where('sender_id='.$current_user.' OR recipient_id='.$current_user.'')->limit(1)->orderBy('id DESC')->one();

            $perepiska = new Messages();
            $perepiska->sender_id = $current_user;
            $perepiska->recipient_id = ($model->recipient_id == $current_user ? $model->sender_id : $model->recipient_id);
            $perepiska->message = Html::encode(Yii::$app->request->post('message'));
            $perepiska->query_id = $model->id;
            $perepiska->status = 0;
            $perepiska->save();
            return 'success';
        }
        return 'fail';
    }

    public function actionSubmit_message2(){
        if (Yii::$app->request->post()){
            $current_user = Yii::$app->user->id;

            $perepiska = new Messages();
            $perepiska->sender_id = $current_user;
            $perepiska->recipient_id = Yii::$app->request->post('recipient_id');
            $perepiska->message = Html::encode(Yii::$app->request->post('message'));
            $perepiska->query_id = 0;
            $perepiska->status = 0;
            $perepiska->save();
            return 'success';
        }
        return 'fail';
    }

    public function actionDestroy_dialog(){
        $model = PerepiskaQueries::find()->where('status = 1 and (recipient_id=' . Yii::$app->user->id . ' OR sender_id = '.Yii::$app->user->id.')')->limit(1)->orderBy('id DESC')->one();
        if ($model){
            $model->status = 4;
            $model->save();
            Functions::clearPickupCurrentUser();
        }
    }

    public function actionModal3_chat_end(){
        Functions::clearPickupCurrentUser();
    }

    public function actionCancel_friend(){
        $current_user = Yii::$app->user->id;
        $model = PerepiskaQueries::find()->where('status = 1 and (recipient_id=' . $current_user . ' OR sender_id = '.$current_user.')')->orderBy('id DESC')->limit(1)->one();
        if ($model){
            if ($model->sender_id == $current_user){
                $model->status = 5;
                $model->save();
            }else{
                $model->status = 6;
                $model->save();
            }
            $res['status'] = 'cancel_friend';
            $res['message'] = 'Вы отклонили дружбу';
            $res['buttons'] = '<button type="submit" class="btn btn-danger " data-dismiss="modal" aria-hidden="true">Закрыть</button>';
            Functions::clearPickupCurrentUser();
            return Json::encode($res);
        }
    }

    public function actionGet_contact(){
        if ($id=Yii::$app->request->post('id')){
            $user = User::findOne($id);
            $res['photo'] = $user->photo;
            $res['username'] = $user->username;

            $messages = Messages::find()
                ->where('(sender_id=' . $id . ' OR sender_id='.Yii::$app->user->id.') AND (recipient_id=' . $id . ' OR recipient_id='.Yii::$app->user->id.')')
                ->orderBy('reg_date ASC')
                ->all();
            Messages::updateAll(['status' => 1], '(sender_id=' . $id . ' OR sender_id=' . Yii::$app->user->id . ') AND (recipient_id=' . $id . ' OR recipient_id=' . Yii::$app->user->id . ')');
            $html = '';
            foreach ($messages as $key => $value) {
                if ($value->recipient_id==Yii::$app->user->id){
                    $html.='<div class="sender_message_wrap">
                                <div class="sender_message">
                                     '.$value->message.'
                                </div>
                            </div>';
                }else{
                    $html.='<div class="my_message_wrap">
                                <div class="my_message">
                                     '.$value->message.'
                                </div>
                            </div>';
                }
            }
            $res['messages'] = $html;
            return Json::encode($res);
        }
    }

    public function actionCheckMessagesFriend(){
        if (!$id = Yii::$app->request->post('recipient_id'))  $res['status'] = 'сообщений нет';
        $messages = Messages::find()
            ->where('(sender_id=' . $id . ' AND recipient_id='.Yii::$app->user->id.') AND status = 0 ')
            ->orderBy('reg_date ASC')
            ->all();
        if ($messages){

            $html = '';
            foreach ($messages as $key => $value) {
                $html.='<div class="sender_message_wrap">
                            <div class="sender_message">
                                '.$value->message.'
                            </div>
                        </div>';
            }
            Messages::updateAll(['status'=>1],'(sender_id=' . $id . ' AND recipient_id='.Yii::$app->user->id.') AND status = 0 ');
            $res['status'] = 'new_message_sender';
            $res['html'] = $html;
        }else{
            $res['status'] = 'сообщений нет';
        }
        return Json::encode($res);
    }
    
    public function actionUpdateContactList(){
        $contacts = Contacts::find()
            ->joinWith([
                'messages' => function ($query) {
                    $query->onCondition(['status' =>0]);
                },])
            ->with('user')
            ->where('user_1 = ' . Yii::$app->user->id . '')
            ->orderBy('messages.reg_date DESC')
            ->all();
            $html = '<ul class="contacts">';
            foreach ($contacts as $key => $value) {
                $html.= '<li><a href="#" data-id="'.$value->user->id.'">'.$value->user->username.' <span class="badge">'.count($value->messages).'</span></a></li>';
            }
            $html.='</ul>';
        $res['html'] = $html;
        $res['status'] = 'Обновляю список контактов';
        return Json::encode($res);
        }

    public function actionPickup_time_end(){
        $current_user = Yii::$app->user->id;
        $model = PerepiskaQueries::find()->where('status = 0 and (recipient_id=' . $current_user . ' OR sender_id = '.$current_user.')')->orderBy('id DESC')->limit(1)->one();
        if ($model){
            $model->status = 8;
            $model->save();
        }
    }
    public function actionApprove_friend_time_end(){
        $current_user = Yii::$app->user->id;
        $model = PerepiskaQueries::find()->where('status = 1 and (recipient_id=' . $current_user . ' OR sender_id = '.$current_user.')')->orderBy('id DESC')->limit(1)->one();
        if ($model){
            $model->status = 9;
            $model->save();
        }
    }
    public function actionUpdate_user_list(){
        $default_users = implode(',',[22,23,24,25]);
        $users = User::find()
            ->innerJoinWith('authUsers')
            ->where('(user.id IN ('.$default_users.') OR  (NOW() - ( user.last_activity + INTERVAL 1 HOUR) )<=10) AND status = 2 and auth_assignment.item_name="user" AND id!='.Yii::$app->user->id.'')
            //->andWhere('status = 2 and auth_assignment.item_name="user" AND id!='.Yii::$app->user->id.'')
            ->all();
        $html='';
        $html2='';
        foreach ($users as $key2 => $value2) {
            $html.='<div  data-id='.$value2->id.' class="real item update_user_item1">
                        <div style="background-image:url('.$value2->photo.')"  class="tmn1__item_img">
                        </div>
                        <div class="tmn1__item_dialog">
                            <img src="/images/tmn__item_dialog1.png">
                        </div>
                        <div class="tmn1__item_text">
                            <p>'.$value2->username.', '.(\app\models\Functions::calculate_age($value2->birthday)).' лет</p>
                            <p>'.Yii::$app->params['cities'][$value2->city].'</p>
                        </div>
                   </div>';
        }
        foreach ($users as $key2 => $value2) {
            $html2.='<span  data-id='.$value2->id.' class="cloned item update_user_item1">
                        <div style="background-image:url('.$value2->photo.')"  class="tmn1__item_img">
                        </div>
                        <div class="tmn1__item_dialog">
                            <img src="/images/tmn__item_dialog1.png">
                        </div>
                        <div class="tmn1__item_text">
                            <p>Анастасия, 19 лет</p>
                            <p>Набережные Челны</p>
                        </div>
                     </span>';
        }
        $res['html1'] = $html;
        $res['html2'] = $html2;
        return Json::encode($res);
    }
}
