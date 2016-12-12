<?php
use budyaga\users\components\AuthChoice;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>
<div class="guest_modal1">
    <div class="guest_modal1_rel">
        <div class="guest_modal1_left">
            <div class="guest_modal1_left_logoline">
                <div class="guest_modal1_left_logo">
                    <img src="/images/logo1.jpg" class="img-responsive" alt="Логотип">
                </div>
                <div class="guest_modal1_left_logo_text1">
                    Какой то текст
                </div>
                <div class="guest_modal1_left_text2">
                    Какой то текст
                </div>
                <div class="guest_modal1_left_video">
                    Тут будет видос
                </div>
            </div>
        </div>
        <div class="guest_modal1_right">
            <div class="guest_modal1_right_text1">
                <h2>Регистрация</h2>
            </div>
            <div class="guest_modal1_right_text2">
                Какой то текст
            </div>
            <div class="guest_modal1_right_text3">
                Через социальную сеть
            </div>
            <div class="guest_modal1_right_icons">
                <?= AuthChoice::widget([
                    'baseAuthUrl' => ['/user/auth/index'],
                    'clientCssClass' => 'guest_modal1_right_icons_item'
                ]) ?>
                <!--<a class="auth-link vkontakte" href="/oauth/vkontakte"  data-popup-width="900" data-popup-height="500">
                    <div class="guest_modal1_right_icons_item">
                        <img src="/images/vk_icon1.png" alt="VK">
                    </div>
                </a>
                <a href="#">
                    <div class="guest_modal1_right_icons_item">
                        <img src="/images/fb_icon1.png" alt="Fb">
                    </div>
                </a>
                <a href="#">
                    <div class="guest_modal1_right_icons_item">
                        <img src="/images/mail_icon1.png" alt="Mail">
                    </div>
                </a>-->
            </div>
            <div class="guest_modal1_right_text4">
                Или через форму на сайте
            </div>
            <div class="guest_modal1_right_form">
                <?php $form = ActiveForm::begin(['id' => 'form-profile','action'=>'/signup']); ?>

                    <input type="text" name="reg_name" placeholder="Ваше имя">
                    <button type="submit">Продолжить</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>