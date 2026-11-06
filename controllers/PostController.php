<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['post/index'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['post/viewAll', 'post/viewOwn'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['post/create'],
                    ],
                    [
                        'actions' => ['update', 'submit-review'],
                        'allow' => true,
                        'roles' => ['post/updateOwn', 'post/submitReview'],
                    ],
                    [
                        'actions' => ['approve', 'reject'],
                        'allow' => true,
                        'roles' => ['post/approve', 'post/reject'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['post/delete'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'submit-review' => ['POST'],
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Post::find();

        if (!Yii::$app->user->can('post/viewAll')) {
            $query->where([
                'username' => Yii::$app->user->identity->username
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['date' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */ 
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (
            !Yii::$app->user->can('post/viewAll') &&
            $model->username !== Yii::$app->user->identity->username &&
            $model->status !== 'published'
        ) {
            throw new ForbiddenHttpException('You are not allowed to view this article.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {
            $model->date = date('Y-m-d H:i:s');
            $model->username = Yii::$app->user->identity->username;
            $model->status = 'draft';
            $model->rejection_reason = null;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->username !== Yii::$app->user->identity->username) {
            throw new ForbiddenHttpException('You are not allowed to edit this article.');
        }

        if (!in_array($model->status, ['draft', 'rejected'])) {
            throw new ForbiddenHttpException('This article cannot be edited in its current status.');
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->username = Yii::$app->user->identity->username;
            $model->date = date('Y-m-d H:i:s');

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSubmitReview($id)
    {
        $model = $this->findModel($id);

        if ($model->username !== Yii::$app->user->identity->username) {
            throw new ForbiddenHttpException('You are not allowed to submit this article.');
        }

        if (!in_array($model->status, ['draft', 'rejected'])) {
            throw new ForbiddenHttpException('This article cannot be submitted for review.');
        }

        $model->status = 'pending';
        $model->rejection_reason = null;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Article submitted for review.');
        }

        return $this->redirect(['view', 'id' => $model->idpost]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        if ($model->status !== 'pending') {
            throw new ForbiddenHttpException('Only pending articles can be approved.');
        }

        $model->status = 'published';
        $model->rejection_reason = null;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Article approved successfully.');
        }

        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $model = $this->findModel($id);

        if ($model->status !== 'pending') {
            throw new ForbiddenHttpException('Only pending articles can be rejected.');
        }

        $reason = trim(Yii::$app->request->post('rejection_reason', ''));

        if ($reason === '') {
            Yii::$app->session->setFlash('error', 'Rejection reason is required.');
            return $this->redirect(['view', 'id' => $model->idpost]);
        }

        $model->status = 'rejected';
        $model->rejection_reason = $reason;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Article rejected successfully.');
        }

        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    {
        if ($id > 2) {
            $this->findModel($id)->delete();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
