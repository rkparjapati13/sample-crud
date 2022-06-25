<?php
    namespace app\models;
    use yii\db\ActiveRecord;
    use app\models\Todo;

    /**
   * Author model
   * @property integer $id
   * @property string $name the category's name
   */
    class Category extends ActiveRecord
    {
        private $name;

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getTodo()
        {
            return $this->hasMany(Todo::className(), ['category_id' => 'id']);
        }

    }
?>
