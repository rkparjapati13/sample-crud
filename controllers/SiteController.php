<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Todo;
use app\models\Category;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $todo = new Todo;
        $categories = Category::find()->all();
        $categoryList = ArrayHelper::map($categories,'id','name');
        $todos = Todo::find()->with('category')->all();
        return $this->render('ajax', ['todos' => $todos, 'todo' => $todo, 'categoryList' => $categoryList]);
    }


    public function actionStore()
    {
        $todo = new Todo();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $todo->load($request->post()) && $request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if($todo->save()) {
              $todos = Todo::find()->where(['id' => $todo->id])->select('name,category_id,timestamp,id')->with('category')->asArray()->one();
              $responseData = [
                'status' => true,
                'message' => 'Data saved',
                'data' => $todos
              ];
              // \Yii::$app->session->setFlash('success', "To do saved.");
              return json_encode($responseData);
            }
        }

        $responseData = [
          'status' => false,
          'message' => 'Data not saved'
        ];
        return json_encode($responseData);
    }

    public function actionDelete()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $p = Yii::$app->request->post();

        $id=$p["id"];

        if(\app\models\Todo::find()->where(['id' => $id])->one()->delete())
        {
            return [
               "status" => "success"
           ];
        }
        else
        {
             return [
               "status" => "failure"
           ];
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
