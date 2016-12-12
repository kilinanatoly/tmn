<?php
$this->title = 'Главная страница';
?>
<div class="container main_container">
    <div class="row main_row">
        <div class="sidebar1">
            <div class="sidebar1__logo">
                <img src="/images/sidebar1__logo.png" alt="Логотип">
            </div>
            <div class="sidebar1__contacts">
                <a href="/profile" target="_blank">
                    <img src="/images/sidebar1__contacts.png" alt="Контакты">
                </a>
            </div>
            <div class="sidebar1__messages">
                <a href="#">
                    <img src="/images/sidebar1__messages.png" alt="">
                </a>
            </div>
            <div class="sidebar1__likes">
                <a href="#">
                    <img src="/images/sidebar1__likes.png" alt="">
                </a>
            </div>
        </div>
        <div class="main_content inline_block">
            <?php
            //если есть пользователи выводим им
            if ($users_mas){
                foreach ($users_mas as $key => $value) {
                    $html = '';
                    $html.= '<div class="tmn inline_block"><div class="tmn_wrap line'.$key.'">';
                    foreach ($value as $key2 => $value2) {
                        $html.='<div data-id='.$value2->id.' class="real item '.($key2==2 ? 'active' : '').'">
                                    <div style="background-image:url('.$value2->photo.')"  class="tmn1__item_img">
                                    </div>
                                    <div class="tmn1__item_dialog">
                                        <img src="/images/tmn__item_dialog1.png">
                                    </div>
                                    <div class="tmn1__item_text">
                                        <p>'.$value2->username.', 19 лет</p>
                                        <p>'.Yii::$app->params['cities'][$value2->city].'</p>
                                    </div>
                               </div>';
                    }
                    foreach ($value as $key2 => $value2) {
                        $html.='<span data-id='.$value2->id.' class="cloned item ">
                                    <div style="background-image:url('.$value2->photo.')"  class="tmn1__item_img">
                                    </div>
                                    <div class="tmn1__item_dialog">
                                        <img src="/images/tmn__item_dialog1.png">
                                    </div>
                                    <div class="tmn1__item_text">
                                        <p>'.$value2->username.', 19 лет</p>
                                        <p>'.Yii::$app->params['cities'][$value2->city].'</p>
                                    </div>
                               </span>';
                    }
                    $html.= '</div></div>';
                    echo $html;
                }
            }
            ?>
        </div>

            <?php
                 /*echo '<h3>Контакты</h3>';
                 echo '<div class="contacts_wrap">';
                    //если есть контакты их выводим
                    if ($contacts){
                        echo '<ul class="contacts">';
                        foreach ($contacts as $key => $value) {
                           echo '<li><a href="#" data-id="'.$value->user->id.'">'.$value->user->username.' <span class="badge">'.count($value->messages).'</span></a></li>';
                        }
                        echo '</ul>';
                    }else{
                        echo '<p>Список контактов пуст</p>';
                    }
                 echo '</div>';*/
            ?>
    </div>
</div>
