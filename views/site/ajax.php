<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$this->title = "To-do Application";
?>
<div class="container">
  <div class="mb-5">
    <h2 class="text-center">To-do List Application</h2>
    <h6 class="text-center">Where to-do items are added/deleted and belong to categories</h6>
  </div>
  <?php $form = ActiveForm::begin([
    // 'action' => ['store'],
      // 'method' => 'post',
      'options' => ['id' => 'ajax-form']
  ]); ?>

  <div class="row" style="margin-bottom: 100px;">
      <div class="col-sm-4">
          <?= $form->field($todo, 'category_id')->dropDownList($categoryList,['prompt'=>'Category', 'id' => 'category-id'])->label(false); ?>

      </div>
      <div class="col-sm-4">
        <?= $form->field($todo, 'name')->textInput(['placeholder' => "Type todo item name", 'id' => 'input-name'])->label(false); ?>
      </div>
      <div class="col-sm-4">
        <?php echo Html::submitButton('Add', ['class' => 'btn btn-success','id'=>'submit_id']) ?>
      </div>
  </div>
  <?php ActiveForm::end(); ?>

  <table class="table table-bordered example" id="exampleid">
    <thead>
      <tr>
        <th scope="col">Todo Item Name</th>
        <th scope="col">Category</th>
        <th scope="col">Timestamp</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($todos as $todo): ?>
          <tr>
            <td><?php echo $todo->name; ?></td>
            <td><?php echo $todo->category->name; ?></td>
            <td><?php echo date('jS F',strtotime($todo->timestamp)); ?></td>
            <td style="text-align: center;">
              <button type="button" class="btn btn-danger" onclick="deleteTodo(<?php echo $todo->id; ?>, this)" name="button">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
<script>
$(document).ready(function (event) {
    $("#ajax-form").on('beforeSubmit', function(event){
          event.preventDefault(); // stopping submitting
          let url = "<?= yii\helpers\Url::to(['store'],false) ?>";

          var data = $(this).serializeArray();
          // var url = $(this).attr('action');
          $.ajax({
              url: url,
              type: 'post',
              dataType: 'json',
              data: data
          })
          .done(function(response) {
            var res = JSON.parse(response);
              if (res.status) {
                $('#ajax-form').get(0).reset();
                var html = '<tr>';
                html += '<td>'+res.data.name+'</td>';
                html += '<td>'+res.data.category.name+'</td>';
                html += '<td>'+moment(res.data.timestamp).format('Do MMMM')+'</td>';
                html += '<td style="text-align: center;">'+'<button type="button" class="btn btn-danger" onclick="deleteTodo('+res.data.id+',this)" name="button">Delete</button>'+'</td>';
                html += '</tr>';
                $('.example').append(html);
              } else {
                // alert("Data not added.");
              }
          })
          .fail(function() {
              console.log("error");
          });
          return false;
    });
});

    function deleteTodo(id, t) {
        var tr= $(t).closest("tr");
        let url = "<?= yii\helpers\Url::to(['delete'],true) ?>";

        $.ajax({
            type: "POST",
            cache: false,
            data:{"id":id,},
            url: url,
            dataType: "json",
            success: function(data){
              if(data.status == 'success') {
                tr.find('td').fadeOut(700, function () {
                    tr.remove();
                });
              }
            }
        });
    }
</script>
