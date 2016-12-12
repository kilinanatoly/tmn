<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $sender_id
 * @property integer $recipient_id
 * @property string $reg_date
 * @property integer $status
 * @property integer $message
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'recipient_id', 'status', 'message'], 'required'],
            [['sender_id', 'recipient_id', 'status','query_id'], 'integer'],
            [['reg_date'], 'safe'],
            [['message'], 'string'],
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
            'message' => 'Message',
        ];
    }
}
