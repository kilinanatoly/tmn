<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use budyaga\users\models\User;
use budyaga\cropper\Widget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \budyaga\users\models\SignupForm */

$this->title = Yii::t('users', 'SIGNUP');
$this->params['breadcrumbs'][] = $this->title;
if (!$model->birthday) $model->birthday = '01.01.1993';
if (Yii::$app->request->post('reg_name')){
    $model->first_name = Yii::$app->request->post('reg_name');
}
?>
<div class="site-signup container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'form-profile']); ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $form->field($model, 'photo')->widget(Widget::className(), [
                        'uploadUrl' => Url::toRoute('/user/user/uploadphoto'),
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div><?= $form->field($model, 'first_name')->textInput() ?></div>
            <div><?= $form->field($model, 'email')->input('email') ?></div>
            <div><?= $form->field($model, 'password')->passwordInput() ?></div>
            <div><?= $form->field($model, 'password_repeat')->passwordInput() ?></div>
            <div><?= $form->field($model, 'sex')->dropDownList(User::getSexArray())?></div>
            <div><?= $form->field($model, 'city')->dropDownList(Yii::$app->params['cities'])?></div>
            <div><?= $form->field($model, 'birthday')->widget(\kartik\date\DatePicker::classname(), [
                    'options' => ['placeholder' => 'Кликните для выбора'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'endDate' => '01.01.2004'
                    ]])?></div>


        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('users', 'SIGNUP'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
