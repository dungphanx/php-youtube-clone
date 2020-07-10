<?php
namespace frontend\controllers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Video;
use common\models\Subscriber;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class ChannelController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['like', 'dislike'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionView($username)
    {
        $channel = $this->findChannel($username);

        $dataProvider = new ActiveDataProvider([
            'query' => Video::find()->creator($channel->id)->published()
        ]);

        return $this->render('view', [
            'channel' => $channel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionSubscribe($username)
    {
        $channel = $this->findChannel($username);
        $userId = \Yii::$app->user->id;
        $subscribe = $channel->isSubscribed($userId);
        if (!$subscribe) {
            $subscribe = new Subscriber();
            $subscribe->channel_id = $channel->id;
            $subscribe->user_id = $userId;
            $subscribe->created_at = time();
            $subscribe->save();
            \Yii::$app->mailer->compose([
                'html' => 'subscriber-html',
                'text' => 'subscriber-text'
            ], [
                'channel' => $channel,
                'user' => \Yii::$app->user->identity
            ])
                ->setFrom(\Yii::$app->params['senderEmail'])
                ->setTo($channel->email)
                ->setSubject('You have new subscriber')
                ->send();
        } else {
            $subscribe->delete();
        }
        
        return $this->renderAjax('_subscribe', ['channel' => $channel]);
    }

    protected function findChannel($username)
    {
        $channel = User::findByUsername($username);

        if (!$channel)
        {
            throw new NotFoundHttpException('Channel does not exist');
        }

        return $channel;
    }
}