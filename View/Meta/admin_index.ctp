<?php 
$this->Paginator->options(array(
	'update' => '#content',
	'evalScripts' => true,
	'before' => $this->Js->get('#loading_indicator')->effect('fadeIn', array('buffer' => false)),
	'complete' => $this->Js->get('#loading_indicator')->effect('fadeOut', array('buffer' => false))
));
?> 
<h2><?php echo Inflector::humanize($this->params['controller']);?></h2>

<p><?php echo $this->Html->link('Find New Paths', array('action' => 'initialize'));?> | 
<?php echo $this->Html->link('Add', array('action' => 'add'));?></p>

<?php echo $this->element('paging');?>

<table cellspacing="0" cellpadding="0">
<tr>
	<th><?php echo $this->Paginator->sort('path');?></th>
	<th><?php echo $this->Paginator->sort('title');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions">actions</th>
</tr>
<?php
if(isset($data)):
	$a=0;
	foreach($data as $row):
		extract($row);
		$actions = array();
		$actions[] = $this->Html->link('E', array('action' => 'edit', ${$modelClass}['id']), array('title' => 'edit'));
		$actions[] = $this->Html->link('X', array('action' => 'delete', ${$modelClass}['id']), array('title' => 'delete'), sprintf('Are you sure you want to delete the %s record?', ${$modelClass}['path']));
		$actions = implode(' - ', $actions);
		${$modelClass}['title'] = stripslashes(${$modelClass}['title']);
		${$modelClass}['description'] = stripslashes(${$modelClass}['description']);
?>
<tr class="<?php echo $a%2==0?'even':'odd';?>">
<td><?php echo ${$modelClass}['path'];?></td>

<td><?php echo ${$modelClass}['title'];?></td>

<td><?php echo ${$modelClass}['description'];?></td>

<td class="date"><?php echo $this->Time->format('M j, Y g:i A', ${$modelClass}['modified']);?></td>

<td class="actions"><?php echo $actions;?></td>
</tr>
<?php
		$a++;
	endforeach; 
endif;
?> 
</table>

<?php echo $this->element('paging');?>

<?php echo $this->Js->writeBuffer();?>