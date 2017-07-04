<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\api\DataBehavior;
use grozzzny\catalog\models\Base;
use grozzzny\catalog\models\Properties;
use Yii;
use yii\data\ActiveDataProvider;
use yii\easyii\behaviors\SortableController;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use yii\easyii\components\Controller;


class AController extends Controller
{

    use TraitController;

    public function behaviors()
    {
        return [
            [
                'class' => SortableController::className(),
                'model' => Base::getModel(Yii::$app->request->get('slug'))
            ],
        ];
    }


    /**
     * @param null $slug
     * @return string
     */
    public function actionIndex($slug = null)
    {
        $current_model = Base::getModel($slug);

        $query = $current_model->find();

        $data = new ActiveDataProvider(['query' => $query]);

        $current_model->querySort($data);

        $current_model->queryFilter($query, Yii::$app->request->get());

        Url::remember();

        return $this->render('index', [
            'data' => $data,
            'current_model' => $current_model
        ]);
    }


    /**
     * Создать
     * @param $slug
     * @return array|string|\yii\web\Response
     */
    public function actionCreate($slug)
    {
        $current_model = Base::getModel($slug);

        if ($current_model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($current_model);
            }
            else{
                if(isset($_FILES)){
                    $this->saveFiles($current_model);
                }

                if($current_model->save()){
                    $this->flash('success', Yii::t('gr', 'Post created'));
                    return $this->redirect([Url::previous()]);
                }
                else{
                    $this->flash('error', Yii::t('gr', 'Error'));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('create', [
                'current_model' => $current_model
            ]);
        }
    }


    /**
     * Редактировать
     * @param $id
     * @return array|string|\yii\web\Response
     */
    public function actionEdit($slug, $id)
    {
        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if($current_model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        if ($current_model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($current_model);
            }
            else{
                if(isset($_FILES)){
                    $this->saveFiles($current_model);
                }

                if($current_model->save()){
                    $this->flash('success', Yii::t('gr', 'Post updated'));
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $current_model->formatErrors()));
                }
                return $this->redirect([Url::previous()]);
            }
        }
        else {
            return $this->render('edit', [
                'current_model' => $current_model
            ]);
        }
    }


    public function actionPhotos($slug, $id)
    {
        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if(!($current_model)){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos', [
            'current_model' => $current_model,
        ]);
    }

    public function actionFiles($slug, $id)
    {
        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if(!($current_model)){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        $files_model = Yii::createObject(Files::className());

        return $this->render('files', [
            'current_model' => $current_model,
            'files_model' => $files_model
        ]);
    }


    public function actionUpload($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = Yii::createObject(Files::className());

            $model->event_id = $id;

            if(isset($_FILES)){
                $this->saveFiles($model);
                $model->save(false);

                return [
                    'result' => 'success'
                ];
            }

        }
    }

    public function actionFileDelete($id)
    {
        $model = Files::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
        }else{
            $url = $model->file;
            if($model->delete()){
                @unlink(Yii::getAlias('@webroot').$url);
                $this->flash('success', Yii::t('easyii', 'File cleared'));
            } else {
                $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
            }
        }
        return $this->back();
    }

    /**
     * Удалить
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionDelete($slug, $id)
    {
        $current_model = Base::getModel($slug);

        if(($current_model = $current_model::findOne($id))){
            $current_model->delete();
        } else {
            $this->error =  Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('gr', 'Post deleted'));
    }


    /**
     * Удалить изображение
     * @param $attribute
     * @param $slug
     * @param $id
     * @return \yii\web\Response
     */
    public function actionClearFile($attribute, $slug, $id)
    {
        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if($current_model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
        }else{
            $url = $current_model->$attribute;
            $current_model->$attribute = '';
            if($current_model->update()){
                @unlink(Yii::getAlias('@webroot').$url);
                $this->flash('success', Yii::t('easyii', 'File cleared'));
            } else {
                $this->flash('error', Yii::t('easyii', 'Update error. {0}', $current_model->formatErrors()));
            }
        }
        return $this->back();
    }


    /**
     * Активировать
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionOn($slug, $id)
    {
        return $this->changeStatus($slug, $id, Base::STATUS_ON);
    }


    /**
     * Деактивировать
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionOff($slug, $id)
    {
        return $this->changeStatus($slug, $id, Base::STATUS_OFF);
    }

    /**
     * Изменить статус
     * @param $slug
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeStatus($slug, $id, $status)
    {
        $current_model = Base::getModel($slug);

        if($current_model = $current_model::findOne($id)){
            $current_model->status = $status;
            $current_model->update();
        }else{
            $this->error = Yii::t('easyii', 'Not found');
        }

        return $this->formatResponse(Yii::t('easyii', 'Status successfully changed'));
    }


    public function actionUp($id)
    {
        return $this->move($id, 'up');
    }

    public function actionDown($id)
    {
        return $this->move($id, 'down');
    }
}