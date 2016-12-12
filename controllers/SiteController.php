<?php

namespace app\controllers;

use app\models\Contacts;
use app\models\Functions;
use app\models\PerepiskaQueries;
use budyaga\users\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionCustom(){
        User::updateAll(['employment' => 0]);
        PerepiskaQueries::deleteAll();
    }
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest){
            return $this->redirect('/guest');
        }

        if (!Yii::$app->user->isGuest){
            Functions::clearPickup(Yii::$app->user->id);
        }
        $contacts = Contacts::find()
            ->joinWith([
                'messages' => function ($query) {
                    $query->onCondition(['status' =>0]);
                },])
            ->with('user')
            ->where('user_1 = ' . Yii::$app->user->id . '')
            ->orderBy('messages.reg_date DESC')
            ->all();
        $default_users = implode(',',[22,23,24,25]);
        $users = User::find()
            ->innerJoinWith('authUsers')
            ->where('(user.id IN ('.$default_users.') OR  (NOW() - ( user.last_activity + INTERVAL 1 HOUR) )<=10) AND status = 2 and auth_assignment.item_name="user" AND id!='.Yii::$app->user->id.'')
            //->andWhere('status = 2 and auth_assignment.item_name="user" AND id!='.Yii::$app->user->id.'')
            ->all();
        for ($i=1;$i<=5;$i++){
            shuffle($users);
            $users_mas[$i] = $users;
        }
        return $this->render('index',[
            'users_mas'=>$users_mas,
            'contacts'=>$contacts
        ]);
    }

    public function actionGuest(){
        if (!Yii::$app->user->isGuest){
            return $this->redirect('/');
        }
        $this->layout = 'guest_layout';
        return $this->render('guest');
    }

    /**3
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $user = User::findOne(29);
        echo '<pre>';
        print_r(strtotime(date('Y-m-d H:i:s').' +1 hours') - strtotime($user->last_activity.' +1 hours'));
        echo '<p></p>';
        //print_r();
        echo '</pre>';die;
        echo '<pre>';
        print_r(strtotime(date('Y-m-d H:i:s'),strtotime('+1 hours'))-strtotime($user->last_activity.' +1 hours'));
        echo '</pre>';die;
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
