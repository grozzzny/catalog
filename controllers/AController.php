<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\api\DataBehavior;
use grozzzny\catalog\models\Base;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use Yii;
use yii\data\ActiveDataProvider;
use yii\easyii2\behaviors\SortableController;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use yii\easyii2\components\Controller;


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


    public function actionIndex($category_id = null)
    {
        /**
         * @var Category $category
         */
        $category = Base::getModel(Category::SLUG);
        /**
         * @var Item $item
         */
        $item = Base::getModel(Item::SLUG);

        $currentCategory = empty($category_id) ? null : $category::findOne(['id' => $category_id]);

        $queryCategory = $category->find();
        $queryItem = $item->find();
        if(!empty($currentCategory)) $queryItem->category($currentCategory);

        $dataCategory = new ActiveDataProvider(['query' => $queryCategory, 'pagination' => ['defaultPageSize' => 5]]);
        $dataItem = new ActiveDataProvider(['query' => $queryItem]);

        $category->querySort($dataCategory);
        $item->querySort($dataItem);

        $category::queryFilter($queryCategory, Yii::$app->request->get());
        $item::queryFilter($queryItem, Yii::$app->request->get());

        Url::remember();

        return $this->render('index', [
            'dataCategory' => $dataCategory,
            'dataItem' => $dataItem,
            'currentCategory' => $currentCategory
        ]);
    }


    /**
     * Создать
     * @param $slug
     * @return array|string|\yii\web\Response
     */
    public function actionCreate($slug, $category_id = null)
    {
        /**
         * @var Item|Category $model
         */
        $model = Base::getModel($slug);
        $currentCategory = empty($category_id) ? null : Category::findOne(['id' => $category_id]);

        if(!empty($currentCategory) && $slug == Item::SLUG) $model->categories = [$category_id];
        if(!empty($currentCategory) && $slug == Category::SLUG) $model->parent_id = $currentCategory->id;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if(isset($_FILES)){
                    $this->saveFiles($model);
                }

                if($model->save()){
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
            if($slug == Category::SLUG){
                $title = empty($currentCategory) ? Yii::t('gr', 'Create subcategory in the top-level category') : Yii::t('gr', 'Create Subcategory in Category <b>«{0}»</b>', [$currentCategory->title]);
            }else{
                $title = empty($currentCategory) ? Yii::t('gr', 'Create an item in the top-level category') : Yii::t('gr', 'Create Item in Category <b>«{0}»</b>', [$currentCategory->title]);
            }

            return $this->render('create', [
                'model' => $model,
                'currentCategory' => $currentCategory,
                'title' => $title
            ]);
        }
    }


    /**
     * Редактировать
     * @param $id
     * @return array|string|\yii\web\Response
     */
    public function actionEdit($slug, $category_id = null, $id)
    {
        $model = Base::getModel($slug);
        $currentCategory = empty($category_id) ? null : Category::findOne(['id' => $category_id]);
        $model = $model::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('easyii2', 'Not found'));
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if(isset($_FILES)){
                    $this->saveFiles($model);
                }

                if($model->save()){
                    $this->flash('success', Yii::t('gr', 'Post updated'));
                }
                else{
                    $this->flash('error', Yii::t('easyii2', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->redirect([Url::previous()]);
            }
        }
        else {
            if($slug == Category::SLUG){
                $title = empty($currentCategory) ? Yii::t('gr', 'Edit subcategory in the top-level category') : Yii::t('gr', 'Edit Subcategory in Category <b>«{0}»</b>', [$currentCategory->title]);
            }else{
                $title = empty($currentCategory) ? Yii::t('gr', 'Edit an item in the top-level category') : Yii::t('gr', 'Edit Item in Category <b>«{0}»</b>', [$currentCategory->title]);
            }

            return $this->render('create', [
                'model' => $model,
                'currentCategory' => $currentCategory,
                'title' => $title
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
            $this->flash('error', Yii::t('easyii2', 'Not found'));
        }else{
            $url = $model->file;
            if($model->delete()){
                @unlink(Yii::getAlias('@webroot').$url);
                $this->flash('success', Yii::t('easyii2', 'File cleared'));
            } else {
                $this->flash('error', Yii::t('easyii2', 'Update error. {0}', $model->formatErrors()));
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
            $this->error =  Yii::t('easyii2', 'Not found');
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
            $this->flash('error', Yii::t('easyii2', 'Not found'));
        }else{
            $url = $current_model->$attribute;
            $current_model->$attribute = '';
            if($current_model->update()){
                @unlink(Yii::getAlias('@webroot').$url);
                $this->flash('success', Yii::t('easyii2', 'File cleared'));
            } else {
                $this->flash('error', Yii::t('easyii2', 'Update error. {0}', $current_model->formatErrors()));
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
            $current_model->save();
        }else{
            $this->error = Yii::t('easyii2', 'Not found');
        }

        return $this->formatResponse(Yii::t('easyii2', 'Status successfully changed'));
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