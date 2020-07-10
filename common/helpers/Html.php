<?php
namespace common\helpers;

class Html
{
  public static function channelLink($user, $schema = false)
  {
    return \yii\helpers\Html::a(
      $user->username, 
      \yii\helpers\Url::to(['/channel/view', 'username' => $user->username], $schema), 
      ['class' => 'text-dark']
    );
  }
}