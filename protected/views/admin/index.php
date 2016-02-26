<?php
/* @var $this AdminController */

$this->pageTitle=Yii::app()->name;
?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/create_category.js'); ?>
<form id="new_category">
  <div class="form-group">
    <label for="new_category">New category name:</label>
    <input type="text" class="form-control" id="new_category_field" placeholder="Please write the new category's name">
  </div>
  <div class="form-group">
      <label for="categories">Parent category:(optional)</label>
      <select id="categories" class="form-control">
          <option value="">Loading categories...</option>
      </select>
  </div>
  <button type="submit" class="btn btn-default" id="new_category_button">Create new category</button>
</form>


