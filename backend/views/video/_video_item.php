<?php
  // @var $model = \common\models\Video
  use yii\helpers\StringHelper;
  use yii\helpers\Url;
?>

<div class="media">
  <div class="embed-responsive embed-responsive-16by9 mr-2" style="width: 120px;">
    <a href="<?php echo Url::to(['/video/update', 'id' => $model->video_id]) ?>">
      <video 
        class="embed-responsive-item"
        poster="<?php echo $model->getThumbnailLink() ?>"
        src="<?php echo $model->getVideoLink() ?>"
      ></video>
    </a>
  </div>
  <div class="media-body">
    <h6 class="mt-0"><?php echo $model->title ?></h6>
    <?php echo StringHelper::truncateWords($model->description, 10, $suffix = '...', $asHtml = true) ?>
  </div>
</div>