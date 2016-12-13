<?php
namespace budyaga\users\models\forms;

use budyaga\users\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $sex;
    public $photo;
    public $city;
    public $birthday;
    public $first_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['first_name', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => '\budyaga\users\models\User', 'message' => Yii::t('users', 'THIS_USERNAME_ALREADY_TAKEN')],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['city', 'integer'],
            ['city', 'required'],
            ['birthday', 'string'],
            ['birthday', 'required'],
            ['email', 'unique', 'targetClass' => '\budyaga\users\models\User', 'message' => Yii::t('users', 'THIS_EMAIL_ALREADY_TAKEN')],
            ['sex', 'in', 'range' => [User::SEX_MALE, User::SEX_FEMALE]],
            ['photo', 'required','message'=>'Пожалуйста, выберите фото и нажмите на "Обрезать"'],

            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'username' => Yii::t('users', 'USERNAME'),
            'email' => Yii::t('users', 'EMAIL'),
            'sex' => Yii::t('users', 'SEX'),
            'password' => Yii::t('users', 'PASSWORD'),
            'password_repeat' => Yii::t('users', 'PASSWORD_REPEAT'),
            'photo' => Yii::t('users', 'PHOTO'),
            'city' => 'Город',
            'birthday' => 'Дата рождения',
            'first_name' => 'Имя',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->attributes = $this->attributes;
            $user->birthday = date('Y-m-d',strtotime($user->attributes['birthday']));
            $user->status = User::STATUS_NEW;
            $user->username = $user->attributes['email'];
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                $userRole = Yii::$app->authManager->getRole('user');
                Yii::$app->authManager->assign($userRole, $user->getId());
                return $user;
            }
        }

        return null;
    }

}
