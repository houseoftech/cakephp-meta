<?php 
$action = in_array($this->action, array('add', 'admin_add'))?'Add':'Edit'; 
$action = Inflector::humanize($action);

echo $this->Form->create();?>
<fieldset><legend><?php echo $action . ' ' . $modelClass;?></legend>
<?php
echo $this->Form->input('id');
echo $this->Form->input('path', array('style' => 'width:500px;'));
echo $this->Form->input('controller', array('style' => 'width:500px;'));
echo $this->Form->input('action', array('style' => 'width:500px;'));
echo $this->Form->input('pass', array('style' => 'width:500px;'));
echo stripslashes($this->Form->input('title', array('style' => 'width:500px;')));
echo stripslashes($this->Form->input('description', array('style' => 'width:500px;')));
echo stripslashes($this->Form->input('keywords', array('style' => 'width:500px;')));?>
</fieldset>
<?php echo $this->Form->end('Submit');?>

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