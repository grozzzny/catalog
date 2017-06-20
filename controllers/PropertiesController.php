<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\models\Base;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\easyii\behaviors\SortableController;
use yii\widgets\ActiveForm;

use yii\easyii\components\Controller;


class PropertiesController extends Controller
{

    public $defaultAction = 'fields';

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
    public function actionFields($slug, $id)
    {


        // объявляем экземпляр класса
        $model = new DynamicModel(['name','price_1','price_2', 'phone']);
        $model->name = ' Kisa';
        $model->price_1 = 3000;
        $model->price_2 = 4000;

        //$model->addRule(['name'], 'string', ['min' =>2, 'max' => 3]);
        //$model->addRule(['name'], 'string', ['length' => [2, 5]]);
//        $model->addRule(['name'], 'filter', ['filter'=>'strtolower']);
//        $model->addRule(['name'], 'filter', ['filter'=>'trim']);
//        $model->addRule(['price_1'], 'compare', ['compareValue' => 2000, 'operator' => '>=']);
//        $model->addRule(['price_1'], 'compare', ['compareValue' => 2000, 'operator' => '>=']);
        $model->addRule(['price_1'], 'compare', ['compareAttribute' => 'price_2', 'operator' => '>=']);
        $model->addRule(['phone'], 'default', ['value' => '888888']);



        if ($model->validate()) {
            echo $model->name;
            echo '<br>';
            echo $model->phone;
            echo '<br>ok';
        } else {
            // данные не корректны: $errors - массив содержащий сообщения об ошибках
            print_r($model->errors);
        }

        exit();







        $current_model = Base::getModel($slug);

        $current_model = $current_model::findOne($id);

        if(!($current_model)){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('fields', [
            'current_model' => $current_model,
        ]);
    }

}