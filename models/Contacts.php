<?php

namespace app\models;

use budyaga\users\models\User;
use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property integer $id
 * @property integer $user_1
 * @property integer $user_2
 * @property string $reg_date
 */
class Contacts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_1', 'user_2'], 'required'],
            [['user_1', 'user_2'], 'integer'],
            [['reg_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_1' => 'User 1',
            'user_2' => 'User 2',
            'reg_date' => 'Reg Date',
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_2']);
    }
    
    public function getMessages(){
        return $this->hasMany(Messages::className(),['sender_id'=>'user_2']);
    }
}
