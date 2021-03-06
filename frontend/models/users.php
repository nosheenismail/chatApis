<?php

namespace backend\modules\v1\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\data\ActiveDataProvider;


/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $user_name
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property string $phone_number
 * @property string $dob
 * @property string $status
 * @property string $reset_token
 * @property string $reset_expiry
 * @property string $profile_pic_url
 * @property string $last_login
 * @property int $role_id
 * @property string $created_on
 * @property string $modified_on
 *
 * @property Tokens[] $tokens
 * @property Roles $role
 */
class users extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'user_email', 'user_password', 'status', 'role_id'], 'required'],
            [['phone_number'], 'string'],
            [['dob', 'reset_expiry', 'last_login', 'created_on', 'modified_on'], 'safe'],
            [['role_id'], 'integer'],
            [['user_name', 'status'], 'string', 'max' => 25],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            [['full_name', 'user_email', 'user_password'], 'string', 'max' => 100],
            [['reset_token'], 'string', 'max' => 250],
            [['profile_pic_url'], 'string', 'max' => 300],
            [['user_name'], 'unique'],
            [['user_email'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_name' => 'Full Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone_number' => 'Phone Number',
            'dob' => 'Dob',
            'status' => 'Status',
            'reset_token' => 'Reset Token',
            'reset_expiry' => 'Reset Expiry',
            'profile_pic_url' => 'Profile Pic Url',
            'last_login' => 'Last Login',
            'role_id' => 'Role ID',
            'created_on' => 'Created On',
            'modified_on' => 'Modified On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Tokens::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::className(), ['id' => 'role_id']);
    }


     public function getUserByEmail($user_email)
    {
        $connection = Yii::$app->db;
        $sql  = "SELECT * FROM users WHERE user_email = :email";
        $command = $connection->createCommand($sql);
        $command->bindValue(':email' , $user_email);
        $rows_email = $command->queryOne();
        if ($rows_email) {
            return $rows_email; 
        } else {
            return false;
        }
    }
    
    public function getUserByUsername($user_name)
    {
        $connection = Yii::$app->db;
        $sqlname  = "SELECT * FROM users WHERE user_name= :username";
        $command = $connection->createCommand($sqlname);
        $command->bindValue(':username' , $user_name);
        $rows_username = $command->queryOne();
        if ($rows_username) {
            return true; 
        } else {
            return false;
        }
    }

    public function createUser($user_name,$user_first_name,$user_last_name,$user_full_name,$user_email,$user_pass,$role_id)
    {
        $model = new Users();
        $model->user_name        =   (empty($user_name)) ? "" : $user_name;
        $model->first_name   =   (empty($user_first_name)) ? "" : $user_first_name;
        $model->last_name    =   (empty($user_last_name)) ? "" : $user_last_name;
        $model->full_name    =   (empty($user_full_name)) ? "" : $user_full_name;
        $model->user_email=  $user_email;     
        $model->user_password = password_hash($user_pass, PASSWORD_DEFAULT);
        $model->reset_token = null;
        $model->reset_expiry = null;
        $model->last_login= null;
        //$model->login_status= 'normal';
        $model->created_on=date('Y-m-d H:i:s');
        $model->modified_on=date('Y-m-d H:i:s');
        $model->role_id = $role_id; // 2 for users 
        $model->status = 'Unverified';
        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserByStatus($user_email,$status)
    {
        $connection = Yii::$app->db;
        $sql  = "SELECT * FROM users WHERE user_email= :useremail AND status = :status";
        $command = $connection->createCommand($sql);
        $command->bindValue(':useremail' , $user_email);
        $command->bindValue(':status' , $status);
        $rows_user = $command->queryOne();
        if ($rows_user) {
            return $rows_user; 
        } else {
            return false;
        }
    }
    public function updateToken($user_email,$reset_token,$reset_expiry,$status)
    {
        $connection = Yii::$app->db;
        if ($status!=null) {
            $sql  = "UPDATE users SET reset_token = null, reset_expiry = null, status = :status, modified_on = NOW() WHERE user_email = :email";
            $command = $connection->createCommand($sql);
            $command->bindValue(':email' , $user_email);
            $command->bindValue(':status' , $status);
            $rows_email = $command->execute();
        } else {
            $sql  = "UPDATE users SET reset_token = :resettoken, reset_expiry = :resetexpiry, modified_on = NOW() WHERE user_email = :email";
            $command = $connection->createCommand($sql);
            $command->bindValue(':resettoken' , $reset_token);
            $command->bindValue(':resetexpiry' , $reset_expiry);
            $command->bindValue(':email' , $user_email);
            $rows_email = $command->execute();

        }
        if ($rows_email) {
            $model = $this->getUserByEmail($user_email);
            if($model) {
                return $model;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updateUserPassword($email,$password)
    {
        $connection = Yii::$app->db;
        $sql  = "UPDATE users SET user_password = :password, modified_on = NOW() WHERE user_email = :email";
        $command = $connection->createCommand($sql);
        $command->bindValue(':password' , $password);
        $command->bindValue(':email' , $email);
        $rows_email = $command->execute();
        if($rows_email) {
            return $rows_email;
        } else {
            return false;
        }
    }
    public function search($params)
    {
        $query = Users::find();
        $response = array();
        $i = 0;
        $rows = (new Query())
            ->select('*')
            ->from('users')
            ->where(['like', 'full_name', $params])
            ->orFilterWhere(['like', 'user_name', $params])
            ->all();
        foreach ($rows as $row) {
            $arraytemp['id'] = $row['id'];
            $arraytemp['user_name'] = $row['user_name'];
            $arraytemp['full_name'] = $row['full_name'];
            $response[$i] = $arraytemp;
            $i++;        
        }    
        return $response;
    }

}
