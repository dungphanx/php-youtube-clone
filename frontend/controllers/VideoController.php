<?php

namespace frontend\controllers;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\models\Video;
use common\models\VideoView;
use common\models\VideoLike;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class VideoController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['like', 'dislike', 'history'],
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@']
          ]
        ]
      ],
      'verb' => [
        'class' => VerbFilter::class,
        'actions' => [
          'like' => ['post'],
          'dislike' => ['post']
        ],
      ]
    ];
  }

  public function actionIndex()
  {
    $dataProvider = new ActiveDataProvider([
      'query' => Video::find()->published()->latest()
    ]);
    return $this->render('index', [
      'dataProvider' => $dataProvider
    ]);
  }

  
  public function actionSearch($keyword)
  {
    $query = Video::find()
      ->published()
      ->latest();
    
      if ($keyword) {
        $query
          ->byKeyword($keyword);
          // ->orderBy("MATCH(title, description, tags) AGAINST ('$keyword') DESC");
      }

    $dataProvider = new ActiveDataProvider([
      'query' => $query
  ]);

    return $this->render('search', [
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionView($id)
  {
    $this->layout = 'auth';
    $video = $this->findVideo($id);

    $videoView = new VideoView();
    $videoView->video_id = $id;
    $videoView->user_id = \Yii::$app->user->id;
    $videoView->created_at = time();
    $videoView->save();

    $similarVideos = Video::find()
      ->published()
      ->andWhere(['NOT', ['video_id' => $id]])
      ->byKeyword($video->title)
      ->limit(10)
      ->all();

    return $this->render('view', [
      'model' => $video,
      'similarVideos' => $similarVideos
    ]);
  }

  public function actionHistory()
  {
    $query = Video::find()
      ->alias('v')
      ->innerJoin('(select video_id, MAX(created_at) as max_date from video_view
      where user_id = :userId
      group by video_id) as vv', 'v.video_id = vv.video_id', ['userId' => \Yii::$app->user->id])
      ->orderBy('vv.max_date desc');

    $dataProvider = new ActiveDataProvider([
      'query' => $query
    ]);

    return $this->render('history', ['dataProvider' => $dataProvider]);
  }

  public function actionLike($id)
  {
    $video = $this->findVideo($id);
    $userId = \Yii::$app->user->id;

    $videoLike = VideoLike::find()
      ->andWhere([
        'video_id' => $id,
        'user_id' => $userId
      ])
      ->one();

    if (!$videoLike)
    {
      $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
    } else if ($videoLike->type == VideoLike::TYPE_LIKE) {
      $videoLike->delete();
    } else {
      $videoLike->delete();
      $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
    }

    return $this->renderAjax('_buttons', [
      'model' => $video
    ]);
  }

  public function actionDislike($id)
  {
    $video = $this->findVideo($id);
    $userId = \Yii::$app->user->id;

    $videoLike = VideoLike::find()
      ->userIdVideoId($userId, $id)
      ->one();

    if (!$videoLike)
    {
      $this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
    } else if ($videoLike->type == VideoLike::TYPE_DISLIKE) {
      $videoLike->delete();
    } else {
      $videoLike->delete();
      $this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
    }

    return $this->renderAjax('_buttons', [
      'model' => $video
    ]);
  }

  protected function findVideo($id) {
    $video = Video::findOne($id);
    if (!$video)
    {
      throw new NotFoundHttpException("Video does not exists.");
    }
    return $video;
  }

  protected function saveLikeDislike($videoId, $userId, $type)
  {
    $videoLike = new VideoLike();
    $videoLike->video_id = $videoId;
    $videoLike->user_id = $userId;
    $videoLike->type = $type;
    $videoLike->created_at = time();
    $videoLike->save();
  }
}
