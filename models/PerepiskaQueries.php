<?php

namespace app\models;

use budyaga\users\models\User;
use Yii;

/**
 * This is the model class for table "perepiska_queries".
 *
 * @property integer $id
 * @property integer $sender_id
 * @property integer $recipient_id
 * @property string $reg_date
 * @property integer $status
 */
class PerepiskaQueries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'perepiska_queries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'recipient_id'], 'required'],
            [['sender_id', 'recipient_id', 'status','pokaz','approve_friend_sender','approve_friend_recipient','approve_friend_sender_check','approve_friend_recipient_check'], 'integer'],
            [['reg_date','end_chat_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'recipient_id' => 'Recipient ID',
            'reg_date' => 'Reg Date',
            'status' => 'Status',
        ];
    }
    public function getSender(){
        return $this->hasOne(User::className(),['id'=>'sender_id'] );
    }

    public function getRecipient(){
        return $this->hasOne(User::className(),['id'=>'recipient_id'] );
    }

    public function getMessages(){
        return $this->hasMany(Messages::className(),['query_id'=>'id'])->where('messages.status = 0 AND recipient_id = '.Yii::$app->user->id.'');
    }
}
