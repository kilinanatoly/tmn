<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use budyaga\cropper\Widget;
use budyaga\users\models\User;
use budyaga\users\components\AuthKeysManager;
use budyaga\users\UsersAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \budyaga\users\models\User */

$this->title = Yii::t('users', 'PROFILE');
$this->params['breadcrumbs'][] = $this->title;
$assets = UsersAsset::register($this);
?>
<div class="site-profile container">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-xs-12 col-md-7">
            <?php
            if ($err = $model->getErrors('photos')){
                foreach ($err as $key=>$value) {
                    echo '<div class="alert alert-danger">'.$value.'</div>';
                }
            }
            ?>
            <div class="panel panel-default">
                <div class="panel-heading"><?= Yii::t('users', 'PERSONAL_INFO')?></div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'form-profile','options' => ['enctype' => 'multipart/form-data']]); ?>
                    <?= $form->field($model, 'first_name') ?>
                    <?= $form->field($model, 'sex')->dropDownList(User::getSexArray()); ?>
                    <?= $form->field($model, 'city')->dropDownList(Yii::$app->params['cities'])?>
                    <?php
                        if ($model->photoss){
                            echo '<div class="profile1-photos">
                            <h4>Загруженные фотографии:</h4>
                            <p>Максимально число фотографий для Вашего аккаунта: 5.</p>';
                            foreach ($model->photoss as $key=>$value) {
                                echo '<div class="profile1-photos__item">
                                        <img src="/images/users/'.$model->id.'/'.$value->photo_src.'">
                                        <label><input type="checkbox" value="'.$value->id.'" name="delete-profile-photo[]">Удалить</label>
                                      </div>';
                            }
                            echo $form->field($model, 'photos[]')->fileInput(['multiple' => true, 'accept' => 'image/*']);
                            echo '</div>';
                        }
                    ?>
                    <?= $form->field($model, 'photo')->widget(Widget::className(), [
                        'uploadUrl' => Url::toRoute('/user/user/uploadphoto'),
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('users', 'SAVE'), ['class' => 'btn btn-primary', 'name' => 'profile-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Yii::t('users', 'CHANGE_PASSWORD')?></div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'form-password']); ?>
                    <?php if ($model->password_hash != '') : ?>
                        <?= $form->field($changePasswordForm, 'old_password')->passwordInput(); ?>
                    <?php endif;?>
                    <?= $form->field($changePasswordForm, 'new_password')->passwordInput(); ?>
                    <?= $form->field($changePasswordForm, 'new_password_repeat')->passwordInput(); ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('users', 'SAVE'), ['class' => 'btn btn-primary', 'name' => 'password-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <?php
            /*<div class="panel panel-default">
                <div class="panel-heading"><?= Yii::t('users', 'CHANGE_EMAIL')?></div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'form-email']); ?>
                    <?= $form->field($changeEmailForm, 'new_email')->input('email'); ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('users', 'SAVE'), ['class' => 'btn btn-primary', 'name' => 'email-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>*/
            ?>

        </div>
    </div>
</div>
