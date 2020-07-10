<?php
/** @var $dataProvider \yii\data\ActiveDataProvider */
?>
<h1>My history</h1>
<?php echo \yii\widgets\ListView::widget([
  'dataProvider' => $dataProvider,
  'itemView' => '_item',
  'layout' => '<div class="d-flex flex-wrap">{items}</div>{pager}',
  'itemOptions' => [
    'tag' => false
  ]
]) ?>