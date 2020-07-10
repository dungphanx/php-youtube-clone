<?php
use yii\widgets\Pjax;
/** @var $channel common\models\User */
?>

<div class="jumbotron">
  <h1 class="display-4"><?php echo $channel->username ?></h1>
  <hr class="my-4">
  <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
  <?php Pjax::begin() ?>
    <?php echo $this->render('_subscribe', ['channel' => $channel]) ?>
  <?php Pjax::end() ?>
</div>

<?php echo \yii\widgets\ListView::widget([
  'dataProvider' => $dataProvider,
  'itemView' => '@frontend/views/video/_item',
  'layout' => '<div class="d-flex flex-wrap">{items}</div>{pager}',
  'itemOptions' => [
    'tag' => false
  ]
]) ?>