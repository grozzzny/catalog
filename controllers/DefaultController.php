<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * Class DefaultController
 * @package grozzzny\catalog\controllers
 */
class DefaultController extends BaseController
{
    const EVENT_AFTER_SAVE = 'after_save';

    /**
     * @return Item
     * @throws \yii\base\InvalidConfigException
     */
    protected function getItemModel()
    {
        return Yii::createObject(['class' => Item::class]);
    }

    /**
     * @return Category
     * @throws \yii\base\InvalidConfigException
     */
    protected function getCategoryModel()
    {
        return Yii::createObject(['class' => Category::class]);
    }

    public function actionIndex($category_id = null)
    {

        $category = $this->getCategoryModel();
        $item = $this->getItemModel();

        $currentCategory = empty($category_id) ? null : $category::findOne(['id' => $category_id]);

        $queryCategory = $category->find();
        $queryItem = $item->find();

        if(!empty($currentCategory)) $queryItem->category($currentCategory);

        $dataCategory = new ActiveDataProvider([
            'query' => $queryCategory,
            'pagination' => [
                'defaultPageSize' => $category_id == null ? 30 : 5
            ]
        ]);
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
        $model = $slug == Category::SLUG ? $this->getCategoryModel() : $this->getItemModel($category_id);
        $modelCategory = $this->getCategoryModel();
        $currentCategory = empty($category_id) ? null : $modelCategory::findOne(['id' => $category_id]);

        $this->scenarios($model);

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->trigger(self::EVENT_AFTER_SAVE, new EventController(['model' => $model]));
                    Yii::$app->session->setFlash('success', Yii::t('catalog', 'Post created'));
                    return $this->redirect(Url::previous());
                }
                else{
                    Yii::$app->session->setFlash('error', Yii::t('catalog', 'Error'));
                    return $this->refresh();
                }
            }
        }
        else {
            if($slug == Category::SLUG){
                $title = empty($currentCategory) ? Yii::t('catalog', 'Create subcategory in the top-level category') : Yii::t('catalog', 'Create Subcategory in Category <b>«{0}»</b>', [$currentCategory->title]);
            }else{
                $title = empty($currentCategory) ? Yii::t('catalog', 'Create an item in the top-level category') : Yii::t('catalog', 'Create Item in Category <b>«{0}»</b>', [$currentCategory->title]);
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
        $model = $slug == Category::SLUG ? $this->getCategoryModel() : $this->getItemModel($category_id);
        $modelCategory = $this->getCategoryModel();
        $currentCategory = empty($category_id) ? null : $modelCategory::findOne(['id' => $category_id]);

        $model = $model::findOne($id);

        $this->scenarios($model);

        if($model === null){
            Yii::$app->session->setFlash('error', Yii::t('catalog', 'Not found'));
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->trigger(self::EVENT_AFTER_SAVE, new EventController(['model' => $model]));

                    Yii::$app->session->setFlash('success', Yii::t('catalog', 'Post updated'));
                }
                else{
                    Yii::$app->session->setFlash('error', Yii::t('catalog', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->redirect(Url::previous());
            }
        }
        else {
            if($slug == Category::SLUG){
                $title = empty($currentCategory) ? Yii::t('catalog', 'Edit subcategory in the top-level category') : Yii::t('catalog', 'Edit Subcategory in Category <b>«{0}»</b>', [$currentCategory->title]);
            }else{
                $title = empty($currentCategory) ? Yii::t('catalog', 'Edit an item in the top-level category') : Yii::t('catalog', 'Edit Item in Category <b>«{0}»</b>', [$currentCategory->title]);
            }

            return $this->render('create', [
                'model' => $model,
                'currentCategory' => $currentCategory,
                'title' => $title
            ]);
        }
    }

    protected function scenarios(&$model)
    {

    }

    /**
     * Удалить
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionDelete($slug, $id)
    {
        $current_model = static::getModel($slug);

        if(($current_model = $current_model::findOne($id))){
            $current_model->delete();
        } else {
            $this->error =  Yii::t('catalog', 'Not found');
        }
        return $this->formatResponse(Yii::t('catalog', 'Post deleted'));
    }


    /**
     * Активировать
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionOn($slug, $id)
    {
        return $this->changeStatus($slug, $id, true);
    }


    /**
     * Деактивировать
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function actionOff($slug, $id)
    {
        return $this->changeStatus($slug, $id, false);
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
        $current_model = static::getModel($slug);

        if($current_model = $current_model::findOne($id)){
            $current_model->status = $status;
            $current_model->save();
        }else{
            $this->error = Yii::t('catalog', 'Not found');
        }

        return $this->formatResponse(Yii::t('catalog', 'Status successfully changed'));
    }

    public function getModel($slug)
    {
        if($slug == Item::SLUG){
            return $this->getItemModel();
        } else {
            return $this->getCategoryModel();
        }
    }
}
