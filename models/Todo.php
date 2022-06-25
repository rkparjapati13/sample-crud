<?php
    namespace app\models;
    use yii\db\ActiveRecord;

    /**
   * Post model
   * @property integer $id
   * @property integer $category_id FK to the category's id
   * @property string $name the title of the todo
   */
    class Todo extends ActiveRecord
    {
        private $name;
        private $category_id;

        public function rules()
        {
            return[
                [['name', 'category_id'], 'required']
            ];
        }
        /**
        * @return \yii\db\ActiveQuery
        */
       public function getCategory()
       {
           return $this->hasOne(Category::className(), ['id' => 'category_id']);
       }
    }
?>
