<?php 
$action = in_array($this->action, array('add', 'admin_add'))?'Add':'Edit'; 
$action = Inflector::humanize($action);
?>

<div class="left" style="width:550px;">
<?php
echo $this->Form->create();?>
<fieldset><legend><?php echo $action . ' ' . $modelClass;?></legend>
<?php
echo $this->Form->input('id');
echo $this->Form->input('template', array('label' => 'Template (is this record a template to apply to many pages?)'));
echo $this->Form->input('path', array('style' => 'width:500px;'));
echo $this->Form->input('controller', array('style' => 'width:500px;'));
echo $this->Form->input('action', array('style' => 'width:500px;'));
echo $this->Form->input('pass', array('style' => 'width:500px;'));
echo stripslashes($this->Form->input('title', array('style' => 'width:500px;')));
echo stripslashes($this->Form->input('description', array('style' => 'width:500px;')));
echo stripslashes($this->Form->input('keywords', array('style' => 'width:500px;')));?>
</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>

<div class="left" style="width:500px;">
<h2>Instructions</h2>
<p>Example URL: http://www.domain.com<strong>/articles/view/23/The-Great-New-Article</strong></p>

<h3>Path</h3>
<p>In the example above the Path is everything following the domain name: /articles/view/23/The-Great-New-Article</p>

<p>To create a template use an asterix * as a wildcard. /articles/view/* would match any Path beginning with /articles/view/</p>

<h3>Controller</h3>
<p>The Controller is usually the first part of the Path. articles is the Controller in the example.</p>

<h3>Action</h3>
<p>The action usually follows the Controller in the Path. view is the action in the example.</p>

<p>Sometimes a Controller will only have 1 possible Action and not show in the Path. Such is the case with the pages Controller. The Action in that case is display.</p>

<h3>Pass</h3>
<p>Pass is anything that comes after the Controller and Action in the Path. In the example above the Pass would be 23/The-Great-New-Article</p>

<p>When creating a template, leave this field blank.</p>

<h3>Templates</h3>
<p>When creating a template, you can use variables in the Title, Description, and Keywords fields. Simply use the variable name inside brackets {}.</p>

<p>The available variables depend on the Controller, but most Pages will have available the following list.</p>

<ul>
<li>{id} - Automatically assigned integer representing a record in a database.</li>
<li>{name} - The human readable title of a Page or Post.</li>
<li>{created} - The date and time the record was first created in format yyyy-mm-dd hh:mm:ss.</li>
<li>{modified} - The date and time the record was last updated in format yyyy-mm-dd hh:mm:ss.</li>
</ul>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($){
	var titleCount = $("#MetumTitle").prev("label").append('<span class="character_count"></span>').find(".character_count");
	$("#MetumTitle").keyup(function(){
		titleCount.text(' ' + $(this).val().length + ' characters (70 recommended)');
	}).keyup();
	
	var descriptionCount = $("#MetumDescription").prev("label").append('<span class="character_count"></span>').find(".character_count");
	$("#MetumDescription").keyup(function(){
		descriptionCount.text(' ' + $(this).val().length + ' characters (156 recommended)');
	}).keyup();
	
	var keywordCount = $("#MetumKeywords").prev("label").append('<span class="character_count"></span>').find(".character_count");
	$("#MetumKeywords").keyup(function(){
		keywordCount.text(' ' + $(this).val().length + ' characters');
	}).keyup();
});
//]]>
</script>