<?php
namespace grozzzny\catalog\controllers;

use grozzzny\admin\helpers\Image;
use grozzzny\admin\helpers\Upload;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Data;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use Yii;
use yii\base\ActionEvent;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;



class PropertiesController extends BaseController
{
    public $defaultAction = 'fields';

    const RESPONSE_SUCCESS = 'success';
    const RESPONSE_ERROR = 'error';

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
    public function actionFields($slug, $category_id = null, $id)
    {
        /**
         * @var Category $model
         */
        $model = Yii::createObject(['class' => Category::class]);
        $model = $model::findOne($id);

        $currentCategory = empty($category_id) ? null : Category::findOne(['id' => $category_id]);

        if(!($model)){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        $title = Yii::t('catalog', 'Edit properties Category <b>«{0}»</b>', [$model->title]);

        return $this->render('fields', [
            'model' => $model,
            'currentCategory' => $currentCategory,
            'title' => $title
        ]);
    }

    public function actionSave()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $data = json_decode(Yii::$app->request->post('data'), true);

            $propertiesModel = Yii::createObject(['class' => Properties::class]);

            foreach ($data as $item){

                $property = $propertiesModel::findOne($item['id']);
                if(!$property) $property = Yii::createObject($propertiesModel::className());

                $property->setAttributes($item);
                $property->validate();

                if($errors = $property->getErrors()){
                    return json_encode(self::response(self::RESPONSE_ERROR, ['slug' => $property->slug, 'errors' => $errors]), JSON_UNESCAPED_UNICODE);
                }

                $property->save();
            }

            return json_encode(self::response(self::RESPONSE_SUCCESS, ['message' => Yii::t('catalog', 'Properties save')]), JSON_UNESCAPED_UNICODE);

        }
    }

    public function actionRemoveProperty()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {

            $get = Yii::$app->request->get();

            /* @var Category $categoryModel */
            $categoryModel = Yii::createObject(['class' => Category::class]);

            $propertiesModel = Yii::createObject(['class' => Properties::class]);

            $property = $propertiesModel::find()
                ->joinWith('categories')
                ->where([
                    $propertiesModel::tableName() . '.id' => $get['id'],
                    'category_id' => $get['category_id']
                ])
            ->one();

            if(!$property) {
                throw new NotFoundHttpException();
            }

            $categoryModel::findOne($get['category_id'])->unlink('properties', $property, true);

            if(empty($propertiesModel::findOne($get['id'])->categories)) $property->delete();

            return json_encode(self::response(self::RESPONSE_SUCCESS, ['message' => Yii::t('catalog', 'Property remove')]), JSON_UNESCAPED_UNICODE);

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

            /**
             * @var Category $model_category
             */
            $model_category = Yii::createObject(['class' => Category::class]);

            $query = $model_category::find();

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
            /**
             * @var Category $model_category
             */
            $model_category = Yii::createObject(['class' => Category::class]);

            $query = $model_category::find();

            $query->filterWhere(['LIKE', 'title', Yii::$app->request->get('q')]);

            $identifier = 'id';

            $fullTitle = Yii::$app->request->get('fullTitle', 'on');

            if(!empty($_GET['on'])){
                $query->andWhere(['status' => Category::STATUS_ON]);
            }

            if(!empty($_GET['category_id'])){
                $query->andWhere(['!=', 'FIND_IN_SET(\''.$_GET['category_id'].'\', parents)', '0']);
            }

            if(!empty($_GET['category_slug'])){
                $identifier = 'slug';
                $query->andWhere(['!=', 'FIND_IN_SET(\''.Category::findOne(['slug' => $_GET['category_slug']])->id.'\', parents)', '0']);
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

            /**
             * @var Category $model_category
             * @var Item $model_item
             */
            $model_item = Yii::createObject(['class' => Item::class]);
            $model_category = Yii::createObject(['class' => Category::class]);

            $query = $model_item::find();

            $query->joinWith('categories');

            $query->filterWhere(['LIKE', $model_item::tableName().'.title', Yii::$app->request->get('q')]);

            $query->andFilterWhere([$model_category::tableName().'.id' => Yii::$app->request->get('category_id')]);

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

            /**
             * @var Category $model_category
             */
            $model_category = Yii::createObject(['class' => Category::class]);

            $category = $model_category::findOne(Yii::$app->request->get('id'));
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

            $propertiesModel = Yii::createObject(['class' => Properties::class]);

            $query = $propertiesModel::find();

            $q = Yii::$app->request->get('q');

            if(!empty($q)) $query->where(['LIKE','title',Yii::$app->request->get('q')]);

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
            $get_properties = Yii::$app->request->get('properties');
            $get_term = Yii::$app->request->get('term');

            $properties = empty($get_properties) ? [] : Yii::$app->request->get('properties');

            $propertiesModel = Yii::createObject(['class' => Properties::class]);

            $query = $propertiesModel::find();

            if(!empty($get_term)) {
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

            $propertiesModel = Yii::createObject(['class' => Properties::class]);

            $property = $propertiesModel::findOne(['slug' => Yii::$app->request->get('slug')]);
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

            if(empty($file)) return json_encode([], JSON_UNESCAPED_UNICODE);

            $image_data = @getimagesize($file->tempName);

            if($image_data){
                $this->orientation($file, $image_data[2]);
                $path = Image::upload($file, $post['attribute']);
            }else{
                $path = Upload::file($file, $post['attribute']);
            }

            return json_encode([
                'initialPreview' => !empty($image_data) ? [$path] : false,
                'initialPreviewConfig' => [
                    ['caption' => basename($path), 'size' => filesize(Yii::getAlias('@webroot').$path), 'width' => '120px', 'url' => $deleteUrl, 'key' => $path],
                ],
                'append' => $append == 'true'
            ], JSON_UNESCAPED_UNICODE);

        }
    }

    protected function orientation(&$file, $image_type)
    {
        if($image_type != IMAGETYPE_JPEG ) return;

        $resource = imagecreatefromjpeg($file->tempName);

        try {
            $exif = exif_read_data($file->tempName, 0, true);
        } catch (\Exception $ex) {
            return;
        }

        if( false === empty($exif['IFD0']['Orientation'] ) ) {
            switch( $exif['IFD0']['Orientation'] ) {
                case 8:
                    $resource = imagerotate($resource, 90, 0 );
                    break;
                case 3:
                    $resource = imagerotate($resource,180,0);
                    break;
                case 6:
                    $resource = imagerotate($resource,-90,0);
                    break;
            }
        }

        imagejpeg($resource,$file->tempName);
    }


    public function actionFileDelete()
    {
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $image = $post['key'];

            /** @var Data $model_data */
            $model_data = Yii::createObject(['class' => Data::class]);

            $data = $model_data::find()->where(['value' => $image])->one();
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
