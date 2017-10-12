<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\models\Base;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Data;
use grozzzny\catalog\models\DataProperties;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;
use Yii;
use yii\base\ActionEvent;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\easyii\behaviors\SortableController;
use yii\easyii\helpers\Image;
use yii\easyii\helpers\Upload;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

use yii\easyii\components\Controller;


class PropertiesController extends Controller
{

    public $defaultAction = 'fields';

    const RESPONSE_SUCCESS = 'success';
    const RESPONSE_ERROR = 'error';

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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $event = new ActionEvent($action);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);

        if($event->isValid){
            if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null && !Yii::$app->getRequest()->validateCsrfToken()) {
                throw new BadRequestHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param null $slug
     * @return string
     */
    public function actionFields($slug, $id)
    {
        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if(!($current_model)){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('fields', [
            'current_model' => $current_model,
        ]);
    }

    public function actionSave()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $data = json_decode(Yii::$app->request->post('data'), true);

            foreach ($data as $item){

                $property = Properties::findOne($item['id']);
                if(!$property) $property = Yii::createObject(Properties::className());

                $property->setAttributes($item);
                $property->validate();

                if($errors = $property->getErrors()){
                    return json_encode(self::response(self::RESPONSE_ERROR, ['slug' => $property->slug, 'errors' => $errors]), JSON_UNESCAPED_UNICODE);
                }

                $property->save();
            }

            return json_encode(self::response(self::RESPONSE_SUCCESS, ['message' => Yii::t('gr', 'Properties save')]), JSON_UNESCAPED_UNICODE);

        }
    }

    public function actionRemoveProperty()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $get = Yii::$app->request->get();

            $property = Properties::find()
                ->joinWith('categories')
                ->where([
                    'gr_catalog_properties.id' => $get['id'],
                    'category_id' => $get['category_id']
                ])
            ->one();

            if(!$property) {
                throw new NotFoundHttpException();
            }

            Category::findOne($get['category_id'])->unlink('properties', $property, true);

            if(empty(Properties::findOne($get['id'])->categories)) $property->delete();

            return json_encode(self::response(self::RESPONSE_SUCCESS, ['message' => Yii::t('gr', 'Property remove')]), JSON_UNESCAPED_UNICODE);

        }
    }

    /**
     * Список категорий при получении ajax запросом
     * @return string
     */
    public function actionGetListCategories()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $query = Category::find();

            $query->filterWhere(['LIKE', 'title', Yii::$app->request->get('q')]);

            $query->andFilterWhere(['parent_id' => Yii::$app->request->get('category_id')]);

            $data = [];
            foreach($query->limit(10)->all() AS $category){
                $data['results'][] = [
                    'id' => $category->id,
                    'text' => $category->fullTitle
                ];
            }

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Список категорий при получении ajax запросом
     * @return string
     */
    public function actionGetListMulticategories()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $query = Category::find();

            $query->filterWhere(['LIKE', 'title', Yii::$app->request->get('q')]);

            $identifier = 'id';

            $fullTitle = Yii::$app->request->get('fullTitle', 'on');

            if(!empty(Yii::$app->request->get('category_id'))){
                $query->andWhere(['!=', 'FIND_IN_SET(\''.Yii::$app->request->get('category_id').'\', parents)', '0']);
            }

            if(!empty(Yii::$app->request->get('category_slug'))){
                $identifier = 'slug';
                $query->andWhere(['!=', 'FIND_IN_SET(\''.Category::findOne(['slug' => Yii::$app->request->get('category_slug')])->id.'\', parents)', '0']);
            }

            $data = [];
            foreach($query->limit(10)->all() AS $category){
                $data['results'][] = [
                    'id' => $category->{$identifier},
                    'text' => $fullTitle == 'on' ? $category->fullTitle : $category->title
                ];
            }

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Список категорий при получении ajax запросом
     * @return string
     */
    public function actionGetListItemsCategory()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $query = Item::find();

            $query->joinWith('categories');

            $query->filterWhere(['LIKE', 'title', Yii::$app->request->get('q')]);

            $query->andFilterWhere(['gr_catalog_categories.id' => Yii::$app->request->get('category_id')]);

            $data = [];
            foreach($query->limit(10)->all() AS $item){
                $data['results'][] = [
                    'id' => $item->id,
                    'text' => $item->title
                ];
            }

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Получение имени категории ajax запросом
     * @return string
     */
    public function actionGetTitleCategories()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {
            $category = Category::findOne(Yii::$app->request->get('id'));
            return $category->fullTitle;
        }
    }


    /**
     * Список свойств при получении ajax запросом
     * @return string
     */
    public function actionGetListProperties()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $query = Properties::find();

            if(!empty(Yii::$app->request->get('q'))) $query->where(['LIKE','title',Yii::$app->request->get('q')]);

            $data = [];
            foreach($query->limit(10)->all() AS $property){
                $data[] = [
                    'id' => $property->slug,
                    'text' => $property->title
                ];
            }

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * Список пользователей
     * @return string
     */
    public function actionGetListUsers()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            /**
             * @var ActiveRecord $model
             */
            $model = new Yii::$app->user->identityClass;

            $query = $model->find();

            if(!empty(Yii::$app->request->get('q'))) $query->where(['LIKE','email', Yii::$app->request->get('q')]);

            $data = [];
            foreach($query->limit(10)->all() AS $user){
                $data['results'][] = [
                    'id' => $user->id,
                    'text' => $user->email
                ];
            }
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * Список свойств при получении ajax запросом
     * @return string
     */
    public function actionGetDataProperties()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $properties = empty(Yii::$app->request->get('properties')) ? [] : Yii::$app->request->get('properties');

            $query = Properties::find();

            if(!empty(Yii::$app->request->get('term'))) {
                $query->andWhere([
                    'AND',
                    ['NOT IN', 'slug', $properties],
                    ['LIKE', 'title', Yii::$app->request->get('term')]
                ]);
            }

            $result = [];
            foreach($query->limit(10)->all() AS $property){
                $result[] = [
                    'id' => $property->id,
                    'label' => $property->title,
                    'slug' => $property->slug,
                    'type' => $property->type,
                    'settings' => $property->settings,
                    'validations' => $property->validations,
                    'options' => $property->options,
                ];
            }

            return json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * Получение имени категории ajax запросом
     * @return string
     */
    public function actionGetTitleProperty()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {
            $property = Properties::findOne(['slug' => Yii::$app->request->get('slug')]);
            return $property->title;
        }
    }


    /**
     * Получение имени категории ajax запросом
     * @return string
     */
    public function actionFileUpload()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $append = $post['append'];
            $deleteUrl = $post['deleteUrl'];

            $file = UploadedFile::getInstanceByName('file_input_'.$post['attribute']);

            $image_data = @getimagesize(Yii::getAlias('@webroot').$file);

            $path = !empty($image_data) ? Image::upload($file, $post['attribute']) : Upload::file($file, $post['attribute']);

            return json_encode([
                'initialPreview' => !empty($image_data) ? [$path] : false,
                'initialPreviewConfig' => [
                    ['caption' => basename($path), 'size' => filesize(Yii::getAlias('@webroot').$path), 'width' => '120px', 'url' => $deleteUrl, 'key' => $path],
                ],
                'append' => $append == 'true'
            ], JSON_UNESCAPED_UNICODE);

        }
    }


    public function actionFileDelete()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $image = $post['key'];

            $data = Data::find()->where(['value' => $image])->one();
            if (!empty($data)) {
                if ($data->item->created_by != Yii::$app->user->id && !Yii::$app->user->can('admin')) {
                    throw new ForbiddenHttpException();
                }
                $data->delete();
            }

            @unlink(Yii::getAlias('@webroot').$image);

            return json_encode([
                'image' => $image
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    private static function response($status, $response)
    {
        return [
            'status' => $status,
            'response' => $response
        ];
    }
}