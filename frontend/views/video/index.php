<?php
/** @var $dataProvider \yii\data\ActiveDataProvider */
?>

<?php echo \yii\widgets\ListView::widget([
  'dataProvider' => $dataProvider,
  'itemView' => '_item',
  'layout' => '<div class="d-flex flex-wrap">{items}</div>{pager}',
  'itemOptions' => [
    'tag' => false
  ]
]) ?>