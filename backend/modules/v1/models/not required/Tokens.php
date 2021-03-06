<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tokens".
 *
 * @property int $tokenId
 * @property string $token
 * @property int $userId
 * @property string $expiry
 * @property string $createdOn
 * @property string $modifiedOn
 *
 * @property Users $user
 */
class Tokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'userId', 'expiry', 'createdOn', 'modifiedOn'], 'required'],
            [['userId'], 'integer'],
            [['expiry', 'createdOn', 'modifiedOn'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['userId' => 'userId']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tokenId' => 'Token ID',
            'token' => 'Token',
            'userId' => 'User ID',
            'expiry' => 'Expiry',
            'createdOn' => 'Created On',
            'modifiedOn' => 'Modified On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['userId' => 'userId']);
    }
}
