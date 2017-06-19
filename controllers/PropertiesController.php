<?php
namespace grozzzny\catalog\controllers;

use grozzzny\catalog\models\Base;
use Yii;
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