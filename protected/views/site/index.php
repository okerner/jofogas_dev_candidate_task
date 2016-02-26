<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/functions.js'); ?>
<form id="search_form">
  <div class="form-group">
    <label for="searchtext">Search text:</label>
    <input type="text" class="form-control" id="searchtext" placeholder="Please write here a Flickr keyword">
  </div>
  <div class="form-group">
      <label for="keywords">Or please select from the next choices:</label>
      <select id="keywords" class="form-control">
          <option value="">Loading categories...</option>
      </select>
  </div>
  <button type="submit" class="btn btn-default" id="search_button">Search</button>
</form>
<div id="search_result"></div>


