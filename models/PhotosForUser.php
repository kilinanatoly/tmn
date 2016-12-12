<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "photos_for_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $photo_src
 */
class PhotosForUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'photos_for_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[  'user_id', 'photo_src'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['photo_src'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'photo_src' => 'Photo Src',
        ];
    }
}
